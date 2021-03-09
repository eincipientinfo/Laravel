<?php


namespace App\Helpers\Range;

use \DateTime as StandardDateTime;

class DateTime extends StandardDateTime
{
    const UUID = '7673a17a-8943-4bad-b46f-ff97e3a41ba4';
    const UUID_PATTERN = UUID_PATTERN;

    public function time()
    {
        return $this->format('H:i');
    }

    public function timeWithSeconds()
    {
        return $this->format('H:i:00');
    }

    public function from(): DateTime
    {
        return clone $this;
    }

    public function to(): DateTime
    {
        return (clone $this)->modify('+15Minutes');
    }

    public function toInt(): int
    {
        return $this->setDate(2019, 01, 01)->getTimestamp();
    }

    public function uuidTail(): string
    {
        return sprintf("%'.012s",  base_convert($this->toInt(), 10, 16));
    }

    public function toUuid(): string
    {
        return preg_replace(self::UUID_PATTERN, '${1}-${2}-${3}-${4}-' . $this->uuidTail(), self::UUID);
    }

    public static function fromUuid(string $uuid): DateTime
    {
        try {
            $tail = preg_replace(self::UUID_PATTERN, '${5}', $uuid);
            $seconds = (int) base_convert($tail, 16, 10);
            return (new DateTime())->setTimestamp($seconds)->setDate(2019, 01, 01);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Will check if range starts where other ends
     * @param DateTime $other
     * @return bool
     */
    public function lower(DateTime $other): bool
    {
        return $other->to() == $this->from();
    }

    /**
     * Will check if range ends where other starts
     * @param DateTime $other
     * @return bool
     */
    public function upper(DateTime $other): bool
    {
        return $this->from() == $other->to();
    }

    public function __toString(): string
    {
        return $this->format('H:i:s');
    }
}
