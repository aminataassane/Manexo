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

    // Revoke & Role change
    'revoke' => 'Revoke',
    'confirm_revoke' => 'Are you sure you want to revoke platform access for this user?',
    'role_revoked' => 'Platform access for ":name" has been revoked.',
    'change_role' => 'Change role',
    'role_changed' => 'Role for ":name" has been changed to :role.',

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
    'email_subject' => 'Manexo — Access the platform administration',
    'email_heading' => 'Access Manexo administration',
    'email_preheader' => ':inviter invites you to join the Manexo administration as :role.',
    'email_body_text' => 'invites you to join the Manexo platform administration as :role.',
    'email_body_desc' => 'By accepting this invitation, you will have access to the administration dashboard, management tools, and platform settings.',
    'email_role_label' => 'Assigned role',
    'email_cta' => 'Accept the invitation',
    'email_expires' => 'This link is valid for 48 hours.',
    'email_fallback' => 'Button not working? Copy this link into your browser:',
    'email_rights' => 'All rights reserved.',
    'email_auto' => 'This email was sent automatically, please do not reply.',
];
