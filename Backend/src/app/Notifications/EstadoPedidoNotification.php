<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Sale;

class EstadoPedidoNotification extends Notification
{
    use Queueable;

    protected $sale;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $esRechazado = $this->sale->state === 'Rechazado';

        return [
            'tipo' => 'pedido',
            'titulo' => $esRechazado
                ? 'Tu pedido #' . $this->sale->id_sale . ' ha sido rechazada.'
                : '¡Tu pedido #' . $this->sale->id_sale . ' ha sido aceptado!',
            'sale_id' => $this->sale->id,
            'status' => $this->sale->status,
            'fecha' => now()->toDateTimeString()
        ];
    }
}
