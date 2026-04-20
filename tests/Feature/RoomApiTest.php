<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoomApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_rooms()
    {
        $user = User::factory()->create(['role' => 'Enseignant']);
        Sanctum::actingAs($user);

        Room::factory()->count(3)->create();

        $response = $this->getJson('/api/rooms');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_user_can_show_room()
    {
        $user = User::factory()->create(['role' => 'Enseignant']);
        Sanctum::actingAs($user);

        $room = Room::factory()->create();

        $response = $this->getJson("/api/rooms/{$room->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $room->id,
                     'name' => $room->name,
                 ]);
    }

    public function test_admin_can_create_room()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $data = [
            'name' => 'Salle A101',
            'capacity' => 30,
            'building' => 'A',
            'is_available' => true,
        ];

        $response = $this->postJson('/api/rooms', $data);

        $response->assertStatus(201)
                 ->assertJson($data);

        $this->assertDatabaseHas('rooms', $data);
    }

    public function test_non_admin_cannot_create_room()
    {
        $user = User::factory()->create(['role' => 'Enseignant']);
        Sanctum::actingAs($user);

        $data = [
            'name' => 'Salle A101',
            'capacity' => 30,
            'building' => 'A',
        ];

        $response = $this->postJson('/api/rooms', $data);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_room()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $room = Room::factory()->create();

        $data = ['name' => 'Salle B202'];

        $response = $this->putJson("/api/rooms/{$room->id}", $data);

        $response->assertStatus(200)
                 ->assertJson($data);

        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'name' => 'Salle B202']);
    }

    public function test_admin_can_delete_room()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $room = Room::factory()->create();

        $response = $this->deleteJson("/api/rooms/{$room->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }

    public function test_responsable_can_list_rooms()
    {
        $responsable = User::factory()->create(['role' => 'Responsable']);
        Sanctum::actingAs($responsable);

        Room::factory()->count(2)->create();

        $response = $this->getJson('/api/rooms');

        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }

    public function test_responsable_cannot_create_room()
    {
        $responsable = User::factory()->create(['role' => 'Responsable']);
        Sanctum::actingAs($responsable);

        $data = [
            'name' => 'Salle C303',
            'capacity' => 25,
            'building' => 'C',
        ];

        $response = $this->postJson('/api/rooms', $data);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_cannot_list_rooms()
    {
        $response = $this->getJson('/api/rooms');

        $response->assertStatus(401);
    }

    public function test_room_not_found_returns_404()
    {
        $user = User::factory()->create(['role' => 'Enseignant']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/rooms/999');

        $response->assertStatus(404);
    }

    public function test_create_room_with_invalid_data()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $data = [
            'name' => '',
            'capacity' => -5,
            'building' => 'A',
        ];

        $response = $this->postJson('/api/rooms', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'capacity']);
    }

    public function test_create_room_with_missing_required_fields()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $data = [
            'name' => 'Salle D404',
        ];

        $response = $this->postJson('/api/rooms', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['capacity', 'building']);
    }

    public function test_update_room_with_invalid_data()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $room = Room::factory()->create();

        $data = ['capacity' => -10];

        $response = $this->putJson("/api/rooms/{$room->id}", $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['capacity']);
    }

    public function test_delete_nonexistent_room_returns_404()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $response = $this->deleteJson('/api/rooms/999');

        $response->assertStatus(404);
    }

    public function test_create_room_without_is_available_defaults_to_true()
    {
        $admin = User::factory()->create(['role' => 'Administrateur']);
        Sanctum::actingAs($admin);

        $data = [
            'name' => 'Salle E505',
            'capacity' => 40,
            'building' => 'E',
        ];

        $response = $this->postJson('/api/rooms', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('rooms', array_merge($data, ['is_available' => true]));
    }
}
