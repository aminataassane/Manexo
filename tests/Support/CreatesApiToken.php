<?php

namespace Tests\Support;

use App\Models\Organization;
use App\Models\User;

trait CreatesApiToken
{
    /**
     * Crée un token Sanctum lié à l’organisation (comme l’UI paramètres API).
     *
     * @param  list<string>  $scopes
     */
    protected function createSanctumTokenForOrganization(User $user, Organization $org, array $scopes): string
    {
        $token = $user->createToken('test-api-token', $scopes);
        $token->accessToken->forceFill([
            'organization_id' => $org->id,
            'scopes' => $scopes,
        ])->save();

        return $token->plainTextToken;
    }
}
