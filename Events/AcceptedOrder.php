<?php

namespace App\Events\CustomerOrders;

use App\Models\Customer\CustomerOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AcceptedOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels, OrderGetterTrait;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CustomerOrder $order)
    {
        $this->setOrder($order);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
