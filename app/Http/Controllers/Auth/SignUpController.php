<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpFormRequest;
use Domain\Auth\Contracts\RegisterNewUserContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SignUpController extends Controller
{
    public function page(): Application|Factory|View|RedirectResponse
    {
        return view('auth.sign-up');
    }

    public function handle(
        SignUpFormRequest $request,
        RegisterNewUserContract $action
    ): RedirectResponse {
        $action(
            $request->get('name'),
            $request->get('email'),
            $request->get('password')
        );

        return redirect()
            ->intended(route('home'));
    }

}