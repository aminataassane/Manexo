<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Lignes de langue de validation
    |--------------------------------------------------------------------------
    */

    'accepted' => 'Le champ :attribute doit être accepté.',
    'active_url' => "Le champ :attribute n'est pas une URL valide.",
    'after' => 'Le champ :attribute doit être une date postérieure au :date.',
    'alpha' => 'Le champ :attribute ne doit contenir que des lettres.',
    'alpha_dash' => 'Le champ :attribute ne doit contenir que des lettres, des chiffres, des tirets et des underscores.',
    'alpha_num' => 'Le champ :attribute ne doit contenir que des lettres et des chiffres.',

    'boolean' => 'Le champ :attribute doit être vrai ou faux.',

    'confirmed' => 'La confirmation de :attribute ne correspond pas.',

    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',

    'max' => [
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],

    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],

    'password' => [
        'letters' => 'Le :attribute doit contenir au moins une lettre.',
        'mixed' => 'Le :attribute doit contenir au moins une majuscule et une minuscule.',
        'numbers' => 'Le :attribute doit contenir au moins un chiffre.',
        'symbols' => 'Le :attribute doit contenir au moins un symbole.',
        'uncompromised' => 'Le :attribute a été compromis. Veuillez en choisir un autre.',
    ],

    'required' => 'Le champ :attribute est obligatoire.',

    'string' => 'Le champ :attribute doit être une chaîne de caractères.',

    'unique' => 'Cette valeur de :attribute est déjà utilisée.',

    'attributes' => [
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
        'name' => 'nom',
        'firstname' => 'prénom',
        'lastname' => 'nom',
        'register_name' => 'nom complet',
        'register_email' => 'adresse e-mail',
        'register_password' => 'mot de passe',
    ],
];

