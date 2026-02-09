<?php

use App\Livewire\Forms\LoginForm;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $loginForm;

    public string $mode = 'login'; // login | register

    // Register fields (kept separate from LoginForm)
    public string $register_name = '';
    public string $register_email = '';
    public string $register_password = '';
    public string $register_password_confirmation = '';

    public function mount(): void
    {
        // Auto-open the right tab depending on the current route (/login or /register)
        $this->mode = request()->routeIs('register') ? 'register' : 'login';
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate(); // validates LoginForm via #[Validate] attributes

        $this->loginForm->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'register_name' => ['required', 'string', 'max:255'],
            'register_email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email'],
            'register_password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['register_name'],
            'email' => $validated['register_email'],
            'password' => Hash::make($validated['register_password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Send OTP code for email verification
        $user->sendEmailVerificationNotification();

        // Redirect user to OTP verification screen
        $this->redirect(route('verification.notice', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="flex items-center justify-between">
        <a
            href="{{ route('home') }}"
            wire:navigate
            class="text-xs font-medium text-gray-500 hover:text-gray-900 underline underline-offset-4"
        >
            Retour à l'accueil
        </a>
    </div>

    <!-- Tabs -->
    <div class="inline-flex rounded-xl bg-gray-100 p-1 text-sm">
        <a
            href="{{ route('login') }}"
            wire:navigate
            class="px-4 py-2 rounded-lg font-medium transition
                {{ request()->routeIs('login') ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}"
        >
            Connexion
        </a>
        <a
            href="{{ route('register') }}"
            wire:navigate
            class="px-4 py-2 rounded-lg font-medium transition
                {{ request()->routeIs('register') ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}"
        >
            Inscription
        </a>
    </div>

    @if ($mode === 'login')
        <div class="mt-8">
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900">
                Bon retour
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                Connectez-vous pour accéder à votre espace et gérer vos tickets.
            </p>

            <x-auth-session-status class="mt-6" :status="session('status')" />

            <form wire:submit="login" class="mt-6">
                <div>
                    <x-input-label for="login_email" value="Email" />
                    <x-text-input
                        wire:model="loginForm.email"
                        id="login_email"
                        class="block mt-1 w-full h-11 px-3"
                        type="email"
                        name="email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <x-input-error :messages="$errors->get('loginForm.email')" class="mt-2" />
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="login_password" value="Mot de passe" />
                    <x-text-input
                        wire:model="loginForm.password"
                        id="login_password"
                        class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    />
                    <x-input-error :messages="$errors->get('loginForm.password')" class="mt-2" />
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <label for="remember" class="inline-flex items-center">
                        <input
                            wire:model="loginForm.remember"
                            id="remember"
                            type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            name="remember"
                        >
                        <span class="ms-2 text-sm text-gray-600">Se souvenir de moi</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-gray-600 hover:text-gray-900 underline" href="{{ route('password.request') }}" wire:navigate>
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <div class="mt-6">
                    <x-primary-button class="w-full justify-center bg-[#005F02] hover:bg-[#004d02] focus:bg-[#004d02] active:bg-[#003b02] normal-case text-sm tracking-normal py-3">
                        Se connecter
                    </x-primary-button>
                </div>
            </form>

            <p class="mt-6 text-sm text-gray-600">
                Pas encore de compte ?
                <a href="{{ route('register') }}" wire:navigate class="font-medium text-gray-900 underline underline-offset-4 hover:text-[#005F02]">
                    Créer un compte
                </a>
            </p>
        </div>
    @else
        <div class="mt-8">
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900">
                Créer votre compte
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                Rejoignez Manexo pour centraliser et gérer vos tickets simplement.
            </p>

            <form wire:submit="register" class="mt-6">
                <div>
                    <x-input-label for="register_name" value="Nom complet" />
                    <x-text-input
                        wire:model="register_name"
                        id="register_name"
                        class="block mt-1 w-full h-11 px-3"
                        type="text"
                        name="name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <x-input-error :messages="$errors->get('register_name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="register_email" value="Email" />
                    <x-text-input
                        wire:model="register_email"
                        id="register_email"
                        class="block mt-1 w-full h-11 px-3"
                        type="email"
                        name="email"
                        required
                        autocomplete="username"
                    />
                    <x-input-error :messages="$errors->get('register_email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="register_password" value="Mot de passe" />
                    <x-text-input
                        wire:model="register_password"
                        id="register_password"
                        class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                    />
                    <x-input-error :messages="$errors->get('register_password')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="register_password_confirmation" value="Confirmation" />
                    <x-text-input
                        wire:model="register_password_confirmation"
                        id="register_password_confirmation"
                        class="block mt-1 w-full"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <x-input-error :messages="$errors->get('register_password_confirmation')" class="mt-2" />
                </div>

                <div class="mt-6">
                    <x-primary-button class="w-full justify-center bg-[#005F02] hover:bg-[#004d02] focus:bg-[#004d02] active:bg-[#003b02] normal-case text-sm tracking-normal py-3">
                        Créer mon compte
                    </x-primary-button>
                </div>
            </form>

            <p class="mt-6 text-sm text-gray-600">
                Déjà un compte ?
                <a href="{{ route('login') }}" wire:navigate class="font-medium text-gray-900 underline underline-offset-4 hover:text-[#005F02]">
                    Se connecter
                </a>
            </p>
        </div>
    @endif
</div>

