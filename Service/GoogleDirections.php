<?php


namespace App\Services;


use App\Helpers\PolylineEncoder;
use App\Models\FetchIt\Jobs;
use App\Models\FetchIt\Tasks\Type;
use App\Models\FetchIt\VehicleType;
use GuzzleHttp\Client;

/**
 * Have to get a full route for
 * Class GoogleDirections
 * @package App\Services
 */
class GoogleDirections
{
    const NOT_FOUND = 'NOT_FOUND';
    const ZERO_RESULTS = 'ZERO_RESULTS';

    public $start = null;
    public $end = null;
    public $waypoints = null;
    public $mode = null;
    protected $client = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    public function pendingEnd(): bool
    {
        return empty($this->end);
    }

    public function pendingStart(): bool
    {
        return empty($this->start);
    }

    protected function validateCoordinates(?array $coords): bool
    {
        return is_array($coords) ? array_key_exists('lng', $coords) && array_key_exists('lat', $coords) &&
            is_numeric($coords['lat']) && is_numeric($coords['lng']) : true; // if it's empty, we don't care
    }

    public function fromJob(Jobs $job): self
    {
        $route = $job->tasks->map(function($task){
            return array(
                'type' => $task->type_name,
                'lat' => $task->address->lat,
                'lng' => $task->address->lng,
            );
        })->toArray();
        return $this->addWhateverCameIn($route);
    }

    public function addWhateverCameIn(array $positions): self
    {
        $waypoints = array();
        foreach($positions as $key => $position){
            if($position['type'] == Type::DELIVERY){
                // since types are dictated by cms, will have to select the first one and all additional ones will go to the waypoints
                // There might be multiple delivery points, so this check is needed
                if($this->pendingEnd()){
                    $this->addEnd($position);
                }
            } elseif ($position['type'] === Type::PICKUP) {
                // if we haven't assigned anything to the start yet, then add to it
                // there might be multiple pickup points, so this check is needed
                if($this->pendingStart()){
                    $this->addStart($position);
                }
            } else {
                array_push($waypoints, $position);
            }
        }
        $this->addWaypoints($waypoints);
        return $this;
    }

    public function addStart(?array $start): self
    {
        if($this->validateCoordinates($start)){
            $this->start = $start;
            return $this;
        }
        throw new Exception('Start coordinates / format is not correct.');
    }

    public function addTravelMode(?VehicleType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function addEnd(?array $end): self
    {
        if($this->validateCoordinates($end)){
            $this->end = $end;
            return $this;
        }
        throw new Exception('End coordinates format is not correct.');
    }

    public function addWaypoints(array $wayPoints = array()): self
    {
        $filtered = array_filter($wayPoints, function($point){
            return $this->validateCoordinates($point);
        });

        if(count($filtered) === count($wayPoints)){
            $this->waypoints = $wayPoints; //array_merge($this->waypoints, );
            return $this;
        }

        throw new Exception('Waypoint coordinates / format is not correct.');
    }


    public function call()
    {
        return $this->client->get((string) $this);
    }

    /**
     * By default will try to access the first one
     * @param int $index
     */
    public function getRoute(int $index = 0)
    {
        $body = json_decode($this->call()->getBody());
        if($body->status !== self::NOT_FOUND && array_key_exists($index, $body->routes)) {
            return $body->routes[$index];
        }
        return null;
    }

    public function getPolyline(int $index = 0)
    {
        $route = $this->getRoute($index);
        return null !== $route? $route->overview_polyline->points : null;
    }

    public function formattedPolyline(int $index = 0)
    {
        $polyline = $this->getPolyline($index);
        return !empty($polyline) ? array_map(function($point){
            return array('lng' => $point['x'], 'lat' => $point['y']);
        }, PolylineEncoder::decodeValue($polyline)) : null;
    }
    /**
     * Some pre-tested values
     */
    public static function polylineTest(int $index = 0)
    {
        $instance = new self(new Client());
        $instance->addStart(['lng' =>  35.898186, 'lat' => 14.506146])->addEnd(['lng' => 35.902913, 'lat' => 14.517175]);
        return $instance->formattedPolyline($index);
    }

    /**
     * We only care about url in this case
     * @return string
     */
    public function __toString(): string
    {
        $baseUrl = 'https://maps.googleapis.com/maps/api/directions/json?';
        $positions = array();
        $key = config('rest.GOOGLE_DIRECTIONS_API_KEY');

        array_push($positions, "key={$key}");

        if(!empty($this->start)){
            array_push($positions, "origin={$this->start['lat']},{$this->start['lng']}");
        }

        if(!empty($this->end)){
            array_push($positions, "destination={$this->end['lat']},{$this->end['lng']}");
        }

        if(!empty($this->waypoints)){
            $reMapped = array_map(function($point){
                $str = "{$point['lat']},{$point['lng']}";
                return $str;
            }, $this->waypoints);
            array_push($positions, 'waypoints=' . implode(',', $reMapped));
        }

        if(!empty($this->type)){
            array_push($positions, "travelmode={$this->type->travel_mode}");
        }

        return $baseUrl . implode('&', $positions);
    }
}
