<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\Sale;
use Illuminate\Notifications\Notification;

use function Symfony\Component\Clock\now;

class NuevoPedidoNotificacion extends Notification
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
    public function via(object $notifiable): array
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
        return [
            'tipo' => 'venta',
            'titulo' => '¡Nueva venta en curso!',
            'contenido' => 'Un usuario ha solicitado un producto',
            'fecha' => now()
        ];
    }
}
