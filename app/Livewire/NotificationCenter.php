<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;

class NotificationCenter extends Component
{
    public bool $showDropdown = false;

    public function getUnreadCountProperty(): int
    {
        return auth()->user()->unreadNotifications->count();
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown = ! $this->showDropdown;
    }

    public function markAsRead(string $id): void
    {
        $notification = auth()->user()->notifications()->find($id);

        if (! $notification) {
            return;
        }

        $notification->markAsRead();

        $projectId = $notification->data['project_id'] ?? null;

        if (! $projectId) {
            return;
        }

        $project = Project::find($projectId);

        if ($project && auth()->user()->can('update', $project)) {
            $this->showDropdown = false;
            $this->redirect(route('projects.edit', $project->id), navigate: true);
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.notification-center', [
            'notifications' => auth()->user()->notifications()->latest()->take(20)->get(),
        ]);
    }
}
