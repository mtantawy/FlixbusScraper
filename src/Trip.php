<?php

namespace FlixbusScraper;

use DateTimeInterface;
use DateTimeImmutable;
use DateInterval;

class Trip
{
    private const TRANSFER_DIRECT = 'Direct';

    private $departureDateTime;
    private $arrivalDateTime;
    private $departureStation;
    private $arrivalStation;
    private $duration;
    private $isDirect;
    private $price;
    private $priceCurrency = '';

    public function getDepartureDateTime(): DateTimeInterface
    {
        return $this->departureDateTime;
    }

    public function getArrivalDateTime(): DateTimeInterface
    {
        return $this->arrivalDateTime;
    }

    public function getDepartureStation(): string
    {
        return $this->departureStation;
    }

    public function getArrivalStation(): string
    {
        return $this->arrivalStation;
    }

    public function getDuration(): DateInterval
    {
        return $this->duration;
    }

    public function isDirect(): bool
    {
        return $this->isDirect;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPriceCurrency(): string
    {
        return $this->priceCurrency;
    }

    public function setDepartureDateTime(string $date, string $time)
    {
        $this->departureDateTime = new DateTimeImmutable($date . ' ' . $time);
    }

    public function setArrivalDateTime(string $date, string $time)
    {
        $this->arrivalDateTime = new DateTimeImmutable($date . ' ' . $time);
        if ($this->arrivalDateTime < $this->departureDateTime) {
            $this->arrivalDateTime = $this->arrivalDateTime->add(new DateInterval('P1D'));
        }
    }

    public function setDepartureStation(string $station)
    {
        $this->departureStation = $station;
    }

    public function setArrivalStation(string $station)
    {
        $this->arrivalStation = $station;
    }

    public function setDuration(DateTimeInterface $departure, DateTimeInterface $arrival)
    {
        $this->duration = $departure->diff($arrival);
    }

    public function setIsDirect(string $transfer)
    {
        $this->isDirect = $transfer === self::TRANSFER_DIRECT;
    }

    public function setPrice(string $price)
    {
        $this->price = (float) trim(str_replace(['$', '€'], '', $price));

        if (false !== mb_stripos($price, '€')) {
            $this->priceCurrency = '€';
        } elseif (false !== mb_stripos($price, '$')) {
            $this->priceCurrency = '$';
        }
    }
}
