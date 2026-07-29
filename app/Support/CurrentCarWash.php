<?php

namespace App\Support;

use App\Models\CarWash;
use LogicException;

class CurrentCarWash
{
    private ?CarWash $carWash = null;

    public function set(CarWash $carWash): void
    {
        $this->carWash = $carWash;
    }

    public function get(): CarWash
    {
        return $this->carWash
            ?? throw new LogicException('Current car wash context has not been set.');
    }

    public function id(): int
    {
        return (int) $this->get()->getKey();
    }

    public function has(): bool
    {
        return $this->carWash !== null;
    }

    public function clear(): void
    {
        $this->carWash = null;
    }
}
