<?php

return [
    // Roles
    'role_super_admin' => 'Super Admin',
    'role_platform_admin' => 'Administrateur plateforme',
    'role_platform_observer' => 'Observateur plateforme',

    // Users Global - Invite UI
    'invite_button' => 'Inviter',
    'invite_title' => 'Invitation plateforme',
    'invite_desc' => 'Envoyez une invitation par email pour accéder à l\'administration de la plateforme.',
    'email_label' => 'Adresse e-mail',
    'email_placeholder' => 'exemple@entreprise.com',
    'role_label' => 'Rôle',
    'send_button' => 'Envoyer l\'invitation',
    'sent' => 'L\'invitation plateforme a été envoyée.',
    'cancelled' => 'L\'invitation a été annulée.',
    'confirm_cancel' => 'Êtes-vous sûr de vouloir annuler cette invitation ?',
    'already_invited' => 'Une invitation en attente existe déjà pour cette adresse email.',
    'already_platform_member' => 'Cet utilisateur a déjà un accès à la plateforme.',

    // Pending invitations
    'pending_title' => 'Invitations en attente',
    'invited_by' => 'Invité par :name',
    'expires_in' => 'Expire :time',

    // Revoke
    'revoke' => 'Révoquer',
    'confirm_revoke' => 'Êtes-vous sûr de vouloir révoquer l\'accès plateforme de cet utilisateur ?',
    'role_revoked' => 'L\'accès plateforme de « :name » a été révoqué.',

    // Accept page
    'accept_title' => 'Invitation plateforme',
    'accept_desc' => 'Vous avez été invité à rejoindre l\'administration de la plateforme Manexo en tant que :role.',
    'accept_button' => 'Accepter l\'invitation',
    'accept_success' => 'Bienvenue ! Vous avez maintenant le rôle :role sur la plateforme.',
    'invalid' => 'Ce lien d\'invitation est invalide ou a déjà été utilisé.',
    'expired' => 'Ce lien d\'invitation a expiré.',
    'wrong_account' => 'Cette invitation est destinée à :email. Veuillez vous déconnecter et vous reconnecter avec le bon compte.',
    'logout_and_login' => 'Se déconnecter et se reconnecter',

    // Register page
    'register_banner' => 'Vous avez été invité en tant que :role sur la plateforme Manexo. Créez votre compte pour accepter.',

    // Email
    'email_subject' => 'Invitation à l\'administration Manexo',
    'email_heading' => 'Invitation plateforme',
    'email_preheader' => ':inviter vous invite à rejoindre l\'administration Manexo en tant que :role.',
    'email_body_text' => 'vous invite à rejoindre l\'administration de la plateforme Manexo en tant que :role.',
    'email_cta' => 'Accepter l\'invitation',
    'email_expires' => 'Ce lien expire dans 48 heures.',
    'email_fallback' => 'Si le bouton ne fonctionne pas, copiez et collez le lien ci-dessous dans votre navigateur :',
    'email_rights' => 'Tous droits réservés.',
    'email_auto' => 'Cet email a été envoyé automatiquement, merci de ne pas y répondre.',
];
