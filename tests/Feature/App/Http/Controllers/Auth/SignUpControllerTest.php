<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\SignUpFormRequest;
use App\Listeners\SendEmailNewUserListener;
use App\Notifications\NewUserNotification;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SignUpControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sign_up_page_success(): void
    {
        $this->get(action([SignUpController::class, 'page']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.sign-up');
    }

    /** @test */
    public function it_sign_up_success(): void
    {
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
