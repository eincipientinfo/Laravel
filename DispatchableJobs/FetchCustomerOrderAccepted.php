<?php

namespace App\Jobs\FetchIt;

use App\Jobs\CustomerOrderQueueJob;
use App\Models\Customer\CustomerOrder;
use App\Services\FetchitService;
use App\Services\NodeServerRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use \Exception;

class FetchitCustomerOrderAccepted extends CustomerOrderQueueJob
{
    public function handle(FetchitService $fetchitService)
    {
        if ($this->customerOrder->should_update_fetchit) {
            $fetchitService->orderUpdated($this->customerOrder);
        }
    }
}
