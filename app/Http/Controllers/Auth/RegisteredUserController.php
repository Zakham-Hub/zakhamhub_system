<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\TenantDatabaseService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => ['sometimes','required', 'string', 'max:255', 'unique:tenants,subdomain'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if ($request->subdomain) {
            $tenant = Tenant::create([
                'name' => $data['name'],
                'subdomain' => strtolower(str_replace(' ', '', $data['subdomain'])) . '.' . strtolower($request->getHost()),
                'database' => strtolower(
                    str_replace([' ', '.'], '', $data['name'])
                ) . '_' . strtolower(
                    str_replace([' ', '.'], '', $data['subdomain'])
                ),
                'user_id' => $user->id,
            ]);
            (new TenantDatabaseService())->createDB($tenant);
        }
        event(new Registered($user));
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
