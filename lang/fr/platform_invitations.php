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

    // Revoke & Role change
    'revoke' => 'Révoquer',
    'confirm_revoke' => 'Êtes-vous sûr de vouloir révoquer l\'accès plateforme de cet utilisateur ?',
    'role_revoked' => 'L\'accès plateforme de « :name » a été révoqué.',
    'change_role' => 'Changer le rôle',
    'role_changed' => 'Le rôle de « :name » a été changé en :role.',

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
    'email_subject' => 'Manexo — Accédez à l\'administration de la plateforme',
    'email_heading' => 'Accédez à l\'administration Manexo',
    'email_preheader' => ':inviter vous invite à rejoindre l\'administration Manexo en tant que :role.',
    'email_body_text' => 'vous invite à rejoindre l\'administration de la plateforme Manexo en tant que :role.',
    'email_body_desc' => 'En acceptant cette invitation, vous aurez accès au tableau de bord d\'administration, aux outils de gestion et aux paramètres de la plateforme.',
    'email_role_label' => 'Rôle attribué',
    'email_cta' => 'Accepter l\'invitation',
    'email_expires' => 'Ce lien est valable 48 heures.',
    'email_fallback' => 'Le bouton ne fonctionne pas ? Copiez ce lien dans votre navigateur :',
    'email_rights' => 'Tous droits réservés.',
    'email_auto' => 'Cet e-mail a été envoyé automatiquement, merci de ne pas y répondre.',
];
