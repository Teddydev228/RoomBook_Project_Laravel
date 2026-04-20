<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EquipmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_equipment()
    {
        $user = User::factory()->create(['role' => 'Enseignant']);
        Sanctum::actingAs($user);

        Equipment::factory()->count(3)->create();

        $response = $this->getJson('/api/equipment');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_user_can_show_equipment()
    {
        $user = User::factory()->create(['role' => 'Enseignant']);
        Sanctum::actingAs($user);

        $equipment = Equipment::factory()->create();

        $response = $this->getJson("/api/equipment/{$equipment->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $equipment->id,
                     'name' => $equipment->name,
                 ]);
    }

    public function test_admin_can_create_equipment()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $data = [
            'name' => 'Micro sans fil',
            'quantity' => 5,
            'is_available' => true,
        ];

        $response = $this->postJson('/api/equipment', $data);

        $response->assertStatus(201)
                 ->assertJson($data);

        $this->assertDatabaseHas('equipment', $data);
    }

    public function test_non_admin_cannot_create_equipment()
    {
        $user = User::factory()->create(['role' => 'Enseignant']);
        Sanctum::actingAs($user);

        $data = [
            'name' => 'Micro sans fil',
            'quantity' => 5,
        ];

        $response = $this->postJson('/api/equipment', $data);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_equipment()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $equipment = Equipment::factory()->create();

        $data = ['name' => 'Micro avec fil'];

        $response = $this->putJson("/api/equipment/{$equipment->id}", $data);

        $response->assertStatus(200)
                 ->assertJson($data);

        $this->assertDatabaseHas('equipment', ['id' => $equipment->id, 'name' => 'Micro avec fil']);
    }

    public function test_admin_can_delete_equipment()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $equipment = Equipment::factory()->create();

        $response = $this->deleteJson("/api/equipment/{$equipment->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('equipment', ['id' => $equipment->id]);
    }

    public function test_responsable_can_list_equipment()
    {
        $responsable = User::factory()->create(['role' => 'Responsable']);
        Sanctum::actingAs($responsable);

        Equipment::factory()->count(2)->create();

        $response = $this->getJson('/api/equipment');

        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }

    public function test_responsable_cannot_create_equipment()
    {
        $responsable = User::factory()->create(['role' => 'Responsable']);
        Sanctum::actingAs($responsable);

        $data = [
            'name' => 'Vidéoprojecteur',
            'quantity' => 3,
        ];

        $response = $this->postJson('/api/equipment', $data);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_cannot_list_equipment()
    {
        $response = $this->getJson('/api/equipment');

        $response->assertStatus(401);
    }

    public function test_equipment_not_found_returns_404()
    {
        $user = User::factory()->create(['role' => 'Enseignant']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/equipment/999');

        $response->assertStatus(404);
    }

    public function test_create_equipment_with_invalid_data()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $data = [
            'name' => '',
            'quantity' => -5,
        ];

        $response = $this->postJson('/api/equipment', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'quantity']);
    }

    public function test_create_equipment_with_missing_required_fields()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $data = [
            'name' => 'Tableau interactif',
        ];

        $response = $this->postJson('/api/equipment', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['quantity']);
    }

    public function test_update_equipment_with_invalid_data()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $equipment = Equipment::factory()->create();

        $data = ['quantity' => -3];

        $response = $this->putJson("/api/equipment/{$equipment->id}", $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['quantity']);
    }

    public function test_delete_nonexistent_equipment_returns_404()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $response = $this->deleteJson('/api/equipment/999');

        $response->assertStatus(404);
    }

    public function test_create_equipment_without_is_available_defaults_to_true()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $data = [
            'name' => 'Micro sans fil',
            'quantity' => 10,
        ];

        $response = $this->postJson('/api/equipment', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('equipment', array_merge($data, ['is_available' => true]));
    }
}
