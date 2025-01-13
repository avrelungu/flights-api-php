<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeImmutable;

class FlightDTO
{
    private string $number;

    private string $origin;

    private string $destination;

    private DateTimeImmutable $arrivalTime;

    private DateTimeImmutable $departureTime;

    /**
     * Get the value of number
     */ 
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set the value of number
     *
     * @return  self
     */ 
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get the value of origin
     */ 
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set the value of origin
     *
     * @return  self
     */ 
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get the value of destination
     */ 
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set the value of destination
     *
     * @return  self
     */ 
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get the value of arrivalTime
     */ 
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * Set the value of arrivalTime
     *
     * @return  self
     */ 
    public function setArrivalTime(string $arrivalTime)
    {
        $this->arrivalTime = new DateTimeImmutable($arrivalTime);

        return $this;
    }

    /**
     * Get the value of departureTime
     */ 
    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    /**
     * Set the value of departureTime
     *
     * @return  self
     */ 
    public function setDepartureTime(string $departureTime)
    {
        $this->departureTime = new DateTimeImmutable($departureTime);

        return $this;
    }
}