<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WhatsAppSession;

class WhatsAppSessionPolicy
{
    /**
     * Determine if the user can view any sessions.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the session.
     */
    public function view(User $user, WhatsAppSession $session): bool
    {
        return $user->id === $session->user_id;
    }

    /**
     * Determine if the user can create sessions.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the session.
     */
    public function update(User $user, WhatsAppSession $session): bool
    {
        return $user->id === $session->user_id;
    }

    /**
     * Determine if the user can delete the session.
     */
    public function delete(User $user, WhatsAppSession $session): bool
    {
        return $user->id === $session->user_id;
    }
}
