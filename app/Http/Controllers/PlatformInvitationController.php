<?php

namespace App\Http\Controllers;

use App\Models\PlatformInvitation;
use App\Models\User;
use App\Services\SuperAdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformInvitationController extends Controller
{
    public function accept(Request $request, string $token)
    {
        $invitation = PlatformInvitation::with('inviter')
            ->where('token', $token)
            ->first();

        if (! $invitation || $invitation->status !== 'pending') {
            return view('platform-invitations.accept', [
                'error' => __('platform_invitations.invalid'),
                'invitation' => null,
            ]);
        }

        if ($invitation->expires_at->isPast()) {
            $invitation->update(['status' => 'expired']);

            return view('platform-invitations.accept', [
                'error' => __('platform_invitations.expired'),
                'invitation' => null,
            ]);
        }

        $existingUser = User::where('email', $invitation->email)->first();

        if ($existingUser) {
            // Already a platform admin?
            if ($existingUser->hasPlatformAccess()) {
                return view('platform-invitations.accept', [
                    'error' => __('platform_invitations.already_platform_member'),
                    'invitation' => null,
                ]);
            }

            /** @var User|null $currentUser */
            $currentUser = Auth::user();

            if ($currentUser) {
                if ($currentUser->email === $invitation->email) {
                    return $this->acceptInvitation($invitation, $currentUser);
                }

                return view('platform-invitations.accept', [
                    'error' => __('platform_invitations.wrong_account', ['email' => $invitation->email]),
                    'invitation' => $invitation,
                    'showLogout' => true,
                ]);
            }

            session()->put('url.intended', route('platform-invitations.accept', ['token' => $token]));

            return redirect()->route('login');
        }

        // User doesn't exist — redirect to register
        $request->session()->put('platform_invitation_token', $token);

        return redirect()->route('register', [
            'platform_invitation' => $token,
            'email' => $invitation->email,
        ]);
    }

    public function processAccept(Request $request, string $token)
    {
        $invitation = PlatformInvitation::with('inviter')
            ->where('token', $token)
            ->first();

        if (! $invitation || $invitation->status !== 'pending') {
            return view('platform-invitations.accept', [
                'error' => __('platform_invitations.invalid'),
                'invitation' => null,
            ]);
        }

        if ($invitation->expires_at->isPast()) {
            $invitation->update(['status' => 'expired']);

            return view('platform-invitations.accept', [
                'error' => __('platform_invitations.expired'),
                'invitation' => null,
            ]);
        }

        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser || $currentUser->email !== $invitation->email) {
            return redirect()->route('platform-invitations.accept', ['token' => $token]);
        }

        if ($currentUser->hasPlatformAccess()) {
            return view('platform-invitations.accept', [
                'error' => __('platform_invitations.already_platform_member'),
                'invitation' => null,
            ]);
        }

        return $this->acceptInvitation($invitation, $currentUser);
    }

    private function acceptInvitation(PlatformInvitation $invitation, User $user)
    {
        $user->forceFill([
            'platform_role' => $invitation->platform_role,
        ])->save();

        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        SuperAdminAuditService::log('platform_invitation.accepted', 'User', $user->id, [
            'email' => $user->email,
            'platform_role' => $invitation->platform_role->value,
            'invited_by' => $invitation->inviter?->name,
        ]);

        return redirect()->route('platform-admin.dashboard')
            ->with('success', __('platform_invitations.accept_success', ['role' => $invitation->platform_role->label()]));
    }
}
