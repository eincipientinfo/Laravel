<?php

namespace App\Listeners;

use App\Events\CustomerOrders\AcceptedOrder;
use App\Events\CustomerOrders\DeletedOrder;
use App\Events\CustomerOrders\NewOrder;
use App\Events\CustomerOrders\RejectedOrder;
use App\Events\CustomerOrders\OrderUpdatedEvent;
use App\Jobs\Fetchit\FetchitCustomerOrderAccepted;
use App\Jobs\Fetchit\FetchitCustomerOrderCreated;
use App\Jobs\Fetchit\FetchitCustomerOrderDeleted;
use App\Jobs\Fetchit\FetchitCustomerOrderUpdated;
use App\Jobs\Node\NodeCustomerOrderAccepted;
use App\Jobs\Node\NodeCustomerOrderCreated;
use App\Jobs\Node\NodeCustomerOrderDeleted;
use App\Jobs\Node\NodeCustomerOrderRejected;
use App\Jobs\Node\NodeCustomerOrderUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CustomerOrderEventListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function onOrderCreated(NewOrder $event)
    {
//        Log::debug(__METHOD__);
        if ($event->getOrder()->should_update_fetchit) {
            FetchitCustomerOrderCreated::dispatch($event->getOrder());
        }
        NodeCustomerOrderCreated::dispatch($event->getOrder());
    }

    public function onOrderAccepted(AcceptedOrder $event)
    {
//        Log::debug(__METHOD__);
        if ($event->getOrder()->should_update_fetchit) {
            FetchitCustomerOrderAccepted::dispatch($event->getOrder());
        }
        NodeCustomerOrderAccepted::dispatch($event->getOrder());
    }

    public function onOrderRejected(RejectedOrder $event)
    {
//        Log::debug(__METHOD__);
        if ($event->getOrder()->should_update_fetchit) {
            FetchitCustomerOrderCreated::dispatch($event->getOrder());
        }
        NodeCustomerOrderRejected::dispatch($event->getOrder());
    }

    public function onOrderUpdated(OrderUpdatedEvent $event)
    {
//        Log::debug(__METHOD__);
        if ($event->getOrder()->should_update_fetchit) {
            FetchitCustomerOrderUpdated::dispatch($event->getOrder());
        }
    }

    public function onOrderRemoved(DeletedOrder $event)
    {
//        Log::debug(__METHOD__);
        if ($event->getOrder()->should_update_fetchit) {
            FetchitCustomerOrderDeleted::dispatch($event->getOrder());
        }
        NodeCustomerOrderDeleted::dispatch($event->getOrder());
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(NewOrder::class, 'App\Listeners\CustomerOrderEventListener@onOrderCreated');
        $events->listen(AcceptedOrder::class, 'App\Listeners\CustomerOrderEventListener@onOrderAccepted');
        $events->listen(RejectedOrder::class, 'App\Listeners\CustomerOrderEventListener@onOrderRejected');
        $events->listen(OrderUpdatedEvent::class, 'App\Listeners\CustomerOrderEventListener@onOrderUpdated');
        $events->listen(DeletedOrder::class, 'App\Listeners\CustomerOrderEventListener@onOrderRemoved');
    }
}
