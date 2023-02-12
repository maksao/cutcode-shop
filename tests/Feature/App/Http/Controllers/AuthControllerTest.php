<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Listeners\SendEmailNewUserListener;
use App\Notifications\NewUserNotification;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

//use Illuminate\Foundation\Testing\WithFaker;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_login_page_success(): void
    {
        $this->get(action([SignInController::class, 'page']))
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.login');
    }

    /** @test */
    public function it_sign_up_page_success(): void
    {
        $this->get(action([SignUpController::class, 'page']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.sign-up');
    }

    /** @test */
    public function it_forgot_page_success(): void
    {
        $this->get(action([ForgotPasswordController::class, 'page']))
            ->assertOk()
            ->assertSee('Забыли пароль')
            ->assertViewIs('auth.forgot-password');
    }

    /** @test */
    public function it_sign_in_success(): void
    {
        $password = '123456789';

        $user = UserFactory::new()->create([
            'email' => 'testing@cutcode.ru',
            'password' => bcrypt($password)
        ]);

        $request = SignInFormRequest::factory()->create([
            'email' => $user->email,
            'password' => $password
        ]);

        $response = $this->post(action([SignInController::class, 'handle']), $request);

        $response->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_logout_success(): void
    {
        $user = UserFactory::new()->create([
            'email' => 'testing@cutcode.ru',
        ]);

        $this->actingAs($user)
            ->delete(action([SignInController::class, 'logOut']));

        $this->assertGuest();
    }

    /** @test */
    public function it_sign_up_success(): void
    {
        //dd($this->app->runningUnitTests());
        Notification::fake();
        Event::fake();

        $request = SignUpFormRequest::factory()->create([
            'email' => 'info@ya.ru',
            'password' => '1234567890',
            'password_confirmation' => '1234567890'
        ]);

        // Записи не существует
        $this->assertDatabaseMissing('users', [
            'email' => $request['email']
        ]);

        // Создание пользователя
        $response = $this->post(action([SignUpController::class, 'handle']), $request);

        // Валидация
        $response->assertValid();

        // Запись существует
        $this->assertDatabaseHas('users', [
            'email' => $request['email']
        ]);

        // Получить пользователя из базы
        $user = User::query()
            ->where('email', $request['email'])
            ->first();

        // Ивент зарегистрирован
        Event::assertDispatched(Registered::class);

        // Слушатель зарегистрирован
        Event::assertListening(Registered::class, SendEmailNewUserListener::class);

        // Запускаем ивент руками т.к. нотисы вызываются на очередях
        $event = new Registered($user);
        $listener = new SendEmailNewUserListener();
        $listener->handle($event);

        // Оповещение отправлено пользователю
        Notification::assertSentTo($user, NewUserNotification::class);

        // Авторизован конкретный пользователь
        $this->assertAuthenticatedAs($user);

        // Осуществлен редирект на главную страницу
        $response->assertRedirect(route('home'));
    }
}
