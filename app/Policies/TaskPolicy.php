<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;


class TaskPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user)
    {
        return $user->role === 'admin' || $user->can_create_tasks; // assuming 'can_create_tasks' is a boolean in User
    }

    public function assign(User $user)
    {
        return $user->role === 'admin' || $user->can_assign_tasks; // assuming 'can_assign_tasks' is a boolean in User
    }

    public function update(User $user, Task $task)
    {
        return $user->id === $task->user_id || $user->role === 'admin';
    }

    public function delete(User $user, Task $task)
{

    return $user->role === 'admin';
}
    public function markAsCompleted(User $user, Task $task)
    {
        return $user->id === $task->user_id || $user->role === 'admin';
    }
}
