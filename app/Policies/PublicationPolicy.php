<?php

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;

class PublicationPolicy
{
    /**
     * Authors edit their own publications; editors and administrators edit any.
     */
    public function update(User $user, Publication $publication): bool
    {
        return $publication->user_id === $user->id || $user->canManageContent();
    }
}
