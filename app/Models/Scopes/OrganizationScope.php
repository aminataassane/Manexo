<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * Automatically adds WHERE organization_id = <current org> to every query
     * on models that use the BelongsToOrganization trait.
     *
     * The scope is only applied when an organization is selected in the session.
     * This avoids errors in CLI commands, queue workers, and seeders.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $orgId = session('current_organization_id');

        if ($orgId !== null) {
            $builder->where($model->getTable() . '.organization_id', $orgId);
        }
    }
}
