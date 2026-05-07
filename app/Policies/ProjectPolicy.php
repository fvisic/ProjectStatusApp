<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        // Managers can view all projects (read-only for others)
        if ($user->isManager()) {
            return true;
        }

        return $user->id === $project->created_by;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        // Managers can only edit their own projects
        return $user->id === $project->created_by;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->created_by;
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->id === $project->created_by;
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->id === $project->created_by;
    }
}
