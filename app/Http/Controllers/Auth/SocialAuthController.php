<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Domain\Auth\Models\User;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Support\SessionRegenerator;

class SocialAuthController extends Controller
{
    public function redirect(string $driver): RedirectResponse
    {
        try {
            return Socialite::driver($driver)
                ->redirect();
        } catch (\Throwable $exception) {
            throw new DomainException('Произошла ошибка или драйвер не поддерживается');
        }
    }

    public function callback(string $driver): RedirectResponse
    {
        if ($driver !== 'github') {
            throw new DomainException('Драйвер не поддерживается');
        }

        $driverUser = Socialite::driver($driver)->user();

        $user = User::query()->firstOrCreate([
            $driver . '_id' => $driverUser->getId(),
        ], [
            'name' => $driverUser->getName() ?? $driverUser->getEmail(),
            'email' => $driverUser->getEmail(),
            'password' => bcrypt(str()->random(20))
        ]);

        SessionRegenerator::run(fn() => auth()->login($user));

        return redirect()
            ->intended(route('home'));
    }

}