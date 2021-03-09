<?php


namespace App\Services;

use App\Models\Customer\CustomerOrder;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
//use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Claims\Custom;
use Tymon\JWTAuth\Facades\JWTAuth;

class FetchitService
{
    private $client;
    private $server;
    private $merchantRef = '87d4e817-ea3c-4a01-aaa9-17d97e9026e2';
    private $authHeaders = array();

    public function __construct(Client $client)
    {
        $token = JWTAuth::fromUser(User::fetchitAccount());
        $this->authHeaders['Authorization'] = 'Bearer ' . $token;
        $this->client = $client;
        $this->server = config('rest.REST_SERVER') . '/api/rest-api';
    }

    /**
     * In this case merchant is REST on another system. This is not related to restaurants directly
     * @param CustomerOrder $order
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function orderCreated(CustomerOrder $order)
    {
        return $this->client->post("{$this->server}/order-created/{$this->merchantRef}", array(
            'json' => $order->toFetchItFormRequest(),
            'headers' => $this->authHeaders,
        ));
    }

    /**
     * In this case merchant is REST on another system. This is not related to restaurants directly
     * @param CustomerOrder $order
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function orderUpdated(CustomerOrder $order)
    {
        return $this->client->post("{$this->server}/order-updated/{$this->merchantRef}/{$order->ref}", array(
            'json' => $order->toFetchItFormRequest(),
            'headers' => $this->authHeaders,
        ));
    }

    public function orderRejected(CustomerOrder $order)
    {
        return $this->client->post("{$this->server}/order-deleted/{$this->merchantRef}/{$order->ref}", array(
            'json' => $order->toFetchItFormRequest(),
            'headers' => $this->authHeaders,
        ));
    }

    public function initiateTest(array $feetchers)
    {
        return $this->client->post("{$this->server}/test-order-assignment", array(
            'json' => array(
                'fetchers' => $feetchers
            ),
            'headers' => $this->authHeaders,
        ));
    }

    public function checkRemoteStatus(CustomerOrder $order)
    {
        return json_decode($this->client->get("{$this->server}/check-rest-job-status/{$order->uuid}")
            ->getBody()
            ->getContents());
    }
}
