<?php


namespace App\Helpers\Range;


use \Iterator;
use Ramsey\Uuid\Uuid;
use App\Helpers\Range\DateTime;

class TimeRange implements Iterator
{
    protected $step = '+15Minutes';
    protected $from = null;
    protected $to = null;
    protected $current = null;
    protected $key = 0;

    public function __construct(?string $from = null, ?string $to = null)
    {
        if (!empty($from)) {
            $this->from = new DateTime($from);
        } else {
            $this->from = new DateTime('2019-01-01 00:00:00');
        }

        if (!empty($to)) {
            $this->to = new DateTime($to);
        } else {
            $this->to = new DateTime('2019-01-01 23:59:59');
        }
        $this->rewind();
    }

    /**
     * Return the current element
     */
    public function current(): ?DateTime
    {
        return $this->current;
    }

    public function from(): ?DateTime
    {
        return clone $this->from;
    }

    public function to(): ?DateTime
    {
        return clone $this->to;
    }

    /**
     * Move forward to next element
     */
    public function next(): ?DateTime
    {
        $this->current = $this->current->modify($this->step);
        $this->key += 1;
        return $this->valid() ? clone $this->current(): null;
    }

    /**
     * Return the key of the current element
     */
    public function key(): int
    {
        return $this->key;
    }

    /**
     * Checks if current position is valid
     */
    public function valid()
    {
        return $this->current >= $this->from && $this->current <= $this->to;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        $this->key = 0;
        $this->current = $this->from();
    }

    public function __toString(): string
    {
        $from = $this->from->format('H:i');
        $to = $this->to->format('H:i');
        $current = $this->current->format('H:i');
        return "DateRange from {$from} to {$to}. Current position at {$current}.";
    }


    public function fromUuidArray(array $uuids)
    {
        usort($uuids, function($first, $second) {
            $firstDate = $this->fromUuid($first);

            $secondDate = $this->fromUuid($second);
            if ($firstDate < $secondDate) {
                return -1;
            } elseif ($firstDate > $secondDate) {
                return 1;
            } else {
                return 0;
            }
        });
        return $uuids;
    }

    /**
     * Set a specific day, so that it would be clear what day it is
     * @param DateTime $date
     * @return $this
     */
    public function setDay(DateTime $date): self
    {
        $year = (int) $date->format('Y');
        $month = (int) $date->format('M');
        $day = (int) $date->format('D');
        $this->current->setDate($year, $month, $day);
        $this->from->setDate($year, $month, $day);
        $this->to->setDate($year, $month, $day);
        return $this;
    }

    public function response()
    {
        $data = array();
        foreach ($this as $key => $value) {
            array_push($data, array(
                'uuid' => $value->toUuid(),
                'value' => $value->format('H:i'),
            ));
        }
        return $data;
    }
}
