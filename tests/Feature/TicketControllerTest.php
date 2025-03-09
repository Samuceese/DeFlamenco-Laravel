<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Evento;
use App\Models\Ticket;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Asegurarse de que los roles existan
        Role::firstOrCreate(['name' => 'cliente']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'empresa']);  // Asegurarse de que el rol 'empresa' exista

        // Crear un usuario y asignarle el rol 'cliente'
        $this->user = User::factory()->create();
        $this->user->assignRole('cliente');

        // Crear un cliente asociado al usuario
        $this->cliente = Cliente::factory()->create(['user_id' => $this->user->id]);

        Auth::login($this->user);

        // Crear un evento
        $this->evento = Evento::factory()->create();

        // Crear un ticket asociado al usuario
        $this->ticket = Ticket::factory()->create([
            'idClient' => $this->cliente->id, // Usar el ID del cliente recién creado
            'idEvent' => $this->evento->id,
        ]);
    }

    public function testIndex()
    {
        $response = $this->get(route('tickets.index'));

        $response->assertStatus(200);
        $response->assertViewIs('tickets.index');
        $response->assertViewHas('tickets');
    }

    public function testValidarTicketExistente()
    {
        $response = $this->get(route('ticket.validar', $this->ticket->id));

        $response->assertStatus(200);
        $response->assertViewIs('tickets.valido');
        $response->assertViewHas('ticket');
    }


}
