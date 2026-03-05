<?php

return [
    // Roles
    'role_super_admin' => 'Super Admin',
    'role_platform_admin' => 'Platform Admin',
    'role_platform_observer' => 'Platform Observer',

    // Users Global - Invite UI
    'invite_button' => 'Invite',
    'invite_title' => 'Platform Invitation',
    'invite_desc' => 'Send an email invitation to access the platform administration.',
    'email_label' => 'Email address',
    'email_placeholder' => 'example@company.com',
    'role_label' => 'Role',
    'send_button' => 'Send invitation',
    'sent' => 'The platform invitation has been sent.',
    'cancelled' => 'The invitation has been cancelled.',
    'confirm_cancel' => 'Are you sure you want to cancel this invitation?',
    'already_invited' => 'A pending invitation already exists for this email address.',
    'already_platform_member' => 'This user already has platform access.',

    // Pending invitations
    'pending_title' => 'Pending invitations',
    'invited_by' => 'Invited by :name',
    'expires_in' => 'Expires :time',

    // Revoke
    'revoke' => 'Revoke',
    'confirm_revoke' => 'Are you sure you want to revoke platform access for this user?',
    'role_revoked' => 'Platform access for ":name" has been revoked.',

    // Accept page
    'accept_title' => 'Platform Invitation',
    'accept_desc' => 'You have been invited to join the Manexo platform administration as :role.',
    'accept_button' => 'Accept invitation',
    'accept_success' => 'Welcome! You now have the :role role on the platform.',
    'invalid' => 'This invitation link is invalid or has already been used.',
    'expired' => 'This invitation link has expired.',
    'wrong_account' => 'This invitation is for :email. Please log out and log in with the correct account.',
    'logout_and_login' => 'Log out and log in',

    // Register page
    'register_banner' => 'You have been invited as :role on the Manexo platform. Create your account to accept.',

    // Email
    'email_subject' => 'Invitation to Manexo Administration',
    'email_heading' => 'Platform Invitation',
    'email_preheader' => ':inviter invites you to join the Manexo administration as :role.',
    'email_body_text' => 'invites you to join the Manexo platform administration as :role.',
    'email_cta' => 'Accept invitation',
    'email_expires' => 'This link expires in 48 hours.',
    'email_fallback' => 'If the button does not work, copy and paste the link below into your browser:',
    'email_rights' => 'All rights reserved.',
    'email_auto' => 'This email was sent automatically, please do not reply.',
];
