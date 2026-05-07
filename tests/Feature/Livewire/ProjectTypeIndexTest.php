<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ProjectTypeIndex;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectTypeIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);
        $this->manager = User::factory()->create(['role' => 'manager']);
    }

    public function test_non_admin_cannot_access(): void
    {
        Livewire::actingAs($this->manager)
            ->test(ProjectTypeIndex::class)
            ->assertForbidden();
    }

    public function test_admin_can_view_component(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProjectTypeIndex::class)
            ->assertOk();
    }

    public function test_admin_can_create_project_type(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProjectTypeIndex::class)
            ->set('name', 'New Type')
            ->set('color', 'blue')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('project_types', ['name' => 'New Type', 'color' => 'blue']);
    }

    public function test_name_is_required(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProjectTypeIndex::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_color_must_be_valid(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProjectTypeIndex::class)
            ->set('name', 'Type')
            ->set('color', 'invalid-color')
            ->call('save')
            ->assertHasErrors(['color']);
    }

    public function test_admin_can_edit_project_type(): void
    {
        $type = ProjectType::create(['name' => 'Old Name', 'color' => 'blue', 'sort_order' => 1]);

        Livewire::actingAs($this->admin)
            ->test(ProjectTypeIndex::class)
            ->call('edit', $type->id)
            ->assertSet('editingId', $type->id)
            ->assertSet('name', 'Old Name')
            ->set('name', 'New Name')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('project_types', ['id' => $type->id, 'name' => 'New Name']);
    }

    public function test_admin_can_delete_project_type(): void
    {
        $type = ProjectType::create(['name' => 'Deletable', 'color' => 'red', 'sort_order' => 1]);

        Livewire::actingAs($this->admin)
            ->test(ProjectTypeIndex::class)
            ->call('delete', $type->id);

        $this->assertSoftDeleted('project_types', ['id' => $type->id]);
    }

    public function test_cannot_delete_type_in_use(): void
    {
        $type = ProjectType::create(['name' => 'In Use', 'color' => 'green', 'sort_order' => 1]);
        Project::factory()->create(['project_type_id' => $type->id]);

        Livewire::actingAs($this->admin)
            ->test(ProjectTypeIndex::class)
            ->call('delete', $type->id);

        $this->assertNotSoftDeleted('project_types', ['id' => $type->id]);
    }

    public function test_admin_can_restore_soft_deleted_type(): void
    {
        $type = ProjectType::create(['name' => 'Restorable', 'color' => 'teal', 'sort_order' => 1]);
        $type->delete();

        Livewire::actingAs($this->admin)
            ->test(ProjectTypeIndex::class)
            ->call('restore', $type->id);

        $this->assertNotSoftDeleted('project_types', ['id' => $type->id]);
    }

    public function test_cancel_edit_resets_state(): void
    {
        $type = ProjectType::create(['name' => 'Cancel Me', 'color' => 'blue', 'sort_order' => 1]);

        $component = Livewire::actingAs($this->admin)
            ->test(ProjectTypeIndex::class)
            ->call('edit', $type->id)
            ->assertSet('editingId', $type->id);

        $component->call('cancelEdit')
            ->assertSet('editingId', null)
            ->assertSet('name', '');
    }

    public function test_project_count_is_visible_in_list(): void
    {
        $type = ProjectType::create(['name' => 'With Projects', 'color' => 'blue', 'sort_order' => 1]);
        Project::factory()->count(3)->create(['project_type_id' => $type->id]);

        Livewire::actingAs($this->admin)
            ->test(ProjectTypeIndex::class)
            ->assertSee('With Projects');
    }
}
