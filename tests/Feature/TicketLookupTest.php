<?php

namespace Tests\Feature;

use App\Http\Controllers\TicketController;
use App\Models\Customer;
use App\Models\Draw;
use App\Models\Prize;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketLookupTest extends TestCase
{
    public function test_lookup_returns_the_ticket_and_participant_contact_information(): void
    {
        $draw = Draw::create([
            'id' => 'draw-'.Str::uuid(),
            'title' => 'Sorteo de prueba',
            'draw_date' => now()->addMonth()->toDateString(),
            'draw_time' => '20:00',
        ]);
        $customer = Customer::create([
            'full_name' => 'María Consulta',
            'phone' => '991 077 380',
            'document_number' => '12345678',
        ]);
        $prize = Prize::create([
            'id' => 'prize-'.Str::uuid(),
            'draw_id' => $draw->id,
            'name' => 'Premio de prueba',
            'price' => 10,
            'active' => true,
        ]);
        $ticket = Ticket::create([
            'id' => 'ticket-'.Str::uuid(),
            'draw_id' => $draw->id,
            'prize_id' => $prize->id,
            'customer_id' => $customer->id,
            'ticket_number' => '9876',
            'purchase_type' => 'individual',
            'total_amount' => 10,
            'status' => 'aprobado',
        ]);

        try {
            $response = app(TicketController::class)->lookup(Request::create('/admin/tickets/consulta', 'GET', [
                'ticket_number' => '#9876',
            ]));

            $this->assertSame('tickets.lookup', $response->name());
            $this->assertSame($ticket->id, $response->getData()['ticket']->id);
            $this->assertSame('María Consulta', $response->getData()['ticket']->customer->full_name);
            $this->assertSame('991 077 380', $response->getData()['ticket']->customer->phone);
        } finally {
            $ticket->delete();
            $prize->delete();
            $customer->delete();
            $draw->delete();
        }
    }
}
