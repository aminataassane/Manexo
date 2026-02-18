<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    // On sépare Prénom et Nom pour respecter le design
    public string $firstname = '';
    public string $lastname = '';

    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // On combine Prénom + Nom pour remplir le champ 'name' de la BDD
        $fullName = trim($validated['firstname'] . ' ' . $validated['lastname']);

        $user = User::create([
            'name' => $fullName,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Envoi email verification
        $user->sendEmailVerificationNotification();
        $this->redirect(route('verification.notice', absolute: false));
    }
}; ?>

<div class="relative z-10 w-full max-w-[440px] px-4">
    <div class="fade-in shadow-slate-200/50 sm:p-6 sm:w-[120%] sm:-ml-[10%] bg-white w-full border-slate-100 border rounded-xl p-5 shadow-2xl">

        <!-- Header -->
        <div class="text-center mb-4">
            <div class="inline-flex items-center gap-1.5 mb-2 group cursor-pointer">
                <div class="relative flex items-center justify-center h-7 w-7 rounded-md bg-[#005F02]/5">
                    <!-- Utilisation de l'icone Iconify qui ressemble à Lucide layers -->
                    <iconify-icon icon="solar:layers-linear" class="text-[#005F02] text-base transition-transform group-hover:rotate-180 duration-700" stroke-width="1.5"></iconify-icon>
                </div>
                <span class="text-base font-semibold tracking-tight text-[#002e01]">Manexo</span>
            </div>
            <h1 class="font-serif text-lg font-medium tracking-tight text-[#002e01]">Créer votre compte</h1>
            <p class="mt-0.5 text-xs text-slate-500">Centralisez et gérez vos tickets simplement.</p>
        </div>

        <!-- Form -->
        <form wire:submit="register" class="space-y-2.5">

            <div class="grid grid-cols-2 gap-2.5">
                <div class="space-y-1">
                    <label for="firstname" class="block text-[11px] font-medium text-slate-700">Prénom</label>
                    <input wire:model="firstname" id="firstname" type="text" autocomplete="given-name" required class="block w-full rounded-md border-0 bg-slate-50 py-1.5 px-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm sm:leading-6 transition-all duration-200">
                    <x-input-error :messages="$errors->get('firstname')" class="mt-1" />
                </div>
                <div class="space-y-1">
                    <label for="lastname" class="block text-[11px] font-medium text-slate-700">Nom</label>
                    <input wire:model="lastname" id="lastname" type="text" autocomplete="family-name" required class="block w-full rounded-md border-0 bg-slate-50 py-1.5 px-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm sm:leading-6 transition-all duration-200">
                    <x-input-error :messages="$errors->get('lastname')" class="mt-1" />
                </div>
            </div>

            <div class="space-y-1">
                <label for="email" class="block text-[11px] font-medium text-slate-700">Adresse e-mail</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5">
                        <iconify-icon icon="solar:letter-linear" class="text-slate-400 text-sm" stroke-width="1.5"></iconify-icon>
                    </div>
                    <input wire:model="email" id="email" type="email" autocomplete="email" placeholder="exemple@entreprise.com" required class="block w-full rounded-md border-0 bg-slate-50 py-1.5 pl-8 pr-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm sm:leading-6 transition-all duration-200">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password & Confirmation Grid -->
            <div class="grid grid-cols-2 gap-2.5">
                <div class="space-y-1">
                    <label for="password" class="block text-[11px] font-medium text-slate-700">Mot de passe</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5">
                            <iconify-icon icon="solar:lock-password-linear" class="text-slate-400 text-sm" stroke-width="1.5"></iconify-icon>
                        </div>
                        <input wire:model="password" id="password" type="password" required class="block w-full rounded-md border-0 bg-slate-50 py-1.5 pl-8 pr-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm sm:leading-6 transition-all duration-200">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="space-y-1">
                    <label for="password_confirmation" class="block text-[11px] font-medium text-slate-700">Confirmation</label>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" required class="block w-full rounded-md border-0 bg-slate-50 py-1.5 px-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm sm:leading-6 transition-all duration-200">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>
            </div>

            <div class="pt-1.5">
                <button type="submit" class="group flex w-full items-center justify-center gap-2 rounded-lg bg-[#005F02] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#004d02] hover:shadow-lg hover:shadow-[#005F02]/20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#005F02] transition-all duration-200 active:scale-[0.98]">
                    Créer mon compte
                    <iconify-icon icon="solar:arrow-right-linear" class="text-white text-sm transition-transform group-hover:translate-x-1" stroke-width="1.5"></iconify-icon>
                </button>
            </div>
        </form>

        <div class="mt-3 text-center">
            <p class="text-[11px] text-slate-500">
                Déjà un compte ?
                <a href="{{ route('login') }}" class="font-normal text-[#005F02] hover:underline hover:text-[#004d02] transition-colors">Se connecter</a>
            </p>
        </div>
    </div>
    <!-- Footer removed here -->
</div>
