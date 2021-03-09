<?php

namespace App\Jobs\FetchIt;

use App\Jobs\CustomerOrderQueueJob;
use App\Services\FetchitService;
use App\Services\NodeServerRequest;
use Illuminate\Support\Facades\Log;
use \Exception;

class FetchitCustomerOrderRejected extends CustomerOrderQueueJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FetchitService $fetchitService)
    {
        if ($this->customerOrder->should_update_fetchit) {
            $fetchitService->orderRemoved($this->customerOrder);
        }
    }


}
