<?php

namespace App\Http\Controllers;

use App\Events\UserNotificationReceived;
use App\Models\OrganizationInvitation;
use App\Models\OrganizationMembership;
use App\Models\Scopes\OrganizationScope;
use App\Models\User;
use App\Notifications\InvitationAcceptedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function accept(Request $request, string $token)
    {
        // Bypass org scope: invitation acceptance is a public route,
        // the user may not have the matching org in session.
        $invitation = OrganizationInvitation::withoutGlobalScope(OrganizationScope::class)
            ->with('organization', 'inviter')
            ->where('token', $token)
            ->first();

        if (! $invitation || $invitation->status !== 'pending') {
            return view('invitations.accept', [
                'error' => __('invitations.invalid'),
                'invitation' => null,
            ]);
        }

        // Check expiration
        if ($invitation->expires_at->isPast()) {
            $invitation->update(['status' => 'expired']);

            return view('invitations.accept', [
                'error' => __('invitations.invalid'),
                'invitation' => null,
            ]);
        }

        $existingUser = User::where('email', $invitation->email)->first();

        if ($existingUser) {
            // Check if already a member
            $alreadyMember = OrganizationMembership::where('organization_id', $invitation->organization_id)
                ->where('user_id', $existingUser->id)
                ->exists();

            if ($alreadyMember) {
                return view('invitations.accept', [
                    'error' => __('invitations.already_member'),
                    'invitation' => null,
                ]);
            }

            /** @var User|null $currentUser */
            $currentUser = Auth::user();

            if ($currentUser) {
                if ($currentUser->email === $invitation->email) {
                    // Correct account logged in — accept directly
                    return $this->acceptInvitation($invitation, $currentUser);
                }

                // Wrong account logged in
                return view('invitations.accept', [
                    'error' => __('invitations.wrong_account', ['email' => $invitation->email]),
                    'invitation' => $invitation,
                    'showLogout' => true,
                ]);
            }

            // Not logged in — store intended URL and redirect to login
            session()->put('url.intended', route('invitations.accept', ['token' => $token]));

            return redirect()->route('login');
        }

        // User doesn't exist — redirect to register
        $request->session()->put('invitation_token', $token);

        return redirect()->route('register', [
            'invitation' => $token,
            'email' => $invitation->email,
        ]);
    }

    public function processAccept(Request $request, string $token)
    {
        $invitation = OrganizationInvitation::withoutGlobalScope(OrganizationScope::class)
            ->with('organization')
            ->where('token', $token)
            ->first();

        if (! $invitation || $invitation->status !== 'pending') {
            return view('invitations.accept', [
                'error' => __('invitations.invalid'),
                'invitation' => null,
            ]);
        }

        if ($invitation->expires_at->isPast()) {
            $invitation->update(['status' => 'expired']);

            return view('invitations.accept', [
                'error' => __('invitations.invalid'),
                'invitation' => null,
            ]);
        }

        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser || $currentUser->email !== $invitation->email) {
            return redirect()->route('invitations.accept', ['token' => $token]);
        }

        $alreadyMember = OrganizationMembership::where('organization_id', $invitation->organization_id)
            ->where('user_id', $currentUser->id)
            ->exists();

        if ($alreadyMember) {
            return view('invitations.accept', [
                'error' => __('invitations.already_member'),
                'invitation' => null,
            ]);
        }

        return $this->acceptInvitation($invitation, $currentUser);
    }

    private function acceptInvitation(OrganizationInvitation $invitation, User $user)
    {
        OrganizationMembership::create([
            'organization_id' => $invitation->organization_id,
            'user_id' => $user->id,
            'role' => $invitation->role,
        ]);

        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Notify the inviter that the invitation was accepted
        if ($invitation->invited_by) {
            $inviter = User::find($invitation->invited_by);
            if ($inviter) {
                $orgName = $invitation->organization?->name ?? '';
                $inviter->notify(new InvitationAcceptedNotification(
                    acceptedByName: $user->name,
                    acceptedByEmail: $user->email,
                    organizationName: $orgName,
                    organizationId: $invitation->organization_id,
                    role: $invitation->role,
                ));
                event(new UserNotificationReceived(userId: (int) $inviter->id, notificationType: 'invitation_accepted'));
            }
        }

        session(['current_organization_id' => $invitation->organization_id]);

        return redirect()->route('dashboard')
            ->with('toast', [
                'type' => 'success',
                'message' => __('invitations.accept_success', ['org' => $invitation->organization->name]),
            ]);
    }
}
