<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignInFormRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Support\SessionRegenerator;

class SignInController extends Controller
{
    public function page(): Application|Factory|View|RedirectResponse
    {
        //flash()->info('Test');
        //return redirect()->route('home');
        return view('auth.login');
    }

    public function handle(SignInFormRequest $request): RedirectResponse
    {
        if (!auth()->attempt($request->validated())) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        SessionRegenerator::run();

        return redirect()
            ->intended(route('home'));
    }

    public function logOut(): RedirectResponse
    {
        SessionRegenerator::run(fn() => auth()->logout());

        return redirect()->route('home');
    }

}