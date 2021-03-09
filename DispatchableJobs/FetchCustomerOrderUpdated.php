<?php

namespace App\Jobs\FetchIt;

use App\Jobs\CustomerOrderQueueJob;
use App\Services\FetchitService;
use Illuminate\Support\Facades\Log;
use App\Models\Fetch\FetchCommsLog;

class FetchitCustomerOrderUpdated extends CustomerOrderQueueJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FetchitService $fetchitService)
    {
        if ($this->customerOrder->should_update_fetchit) {


            //to add something to mitigate continous calls to fetch (if its even from here)
            if(!empty($this->customerOrder))
            {

                FetchCommsLog::create(["order_id"=>$this->customerOrder->id, "status_id"=>$this->customerOrder->status_id, "target"=>"fetchit"]);
                //a first step, would be idealy checking if we already had something added and increase its 'recent_attempts' field

            }else{
                //maybe we store something somewhere else so we know nulls came in but its not typically the error we see
            }

            $response = $fetchitService->orderUpdated($this->customerOrder);
            //$data = (string) $response->getBody()->getContents(); //commented as it seemed to have no purpose from the handle function but likely i broke something more, just going to test
//            Log::debug($data);
            //return $response;
        }
    }
}
