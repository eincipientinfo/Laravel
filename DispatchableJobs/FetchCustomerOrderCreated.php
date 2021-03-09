<?php

namespace App\Jobs\FetchIt;

use App\Jobs\CustomerOrderQueueJob;
use App\Models\Customer\CustomerOrder;
use App\Services\FetchitService;
use \Exception;
use Illuminate\Support\Facades\Log;

class FetchitCustomerOrderCreated extends CustomerOrderQueueJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FetchitService $fetchitService)
    {
        if ($this->customerOrder->should_update_fetchit) {
            $fetchitService->orderCreated($this->customerOrder);
        }
    }
}
