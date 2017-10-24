<?php

namespace FlixbusScraper;

class Analyser
{
    private $days;
    private $lowestPrice;
    private $highestPrice;
    private $mostCommonPrice;

    public function __construct(array $days)
    {
        $this->days = $days;
    }

    public function getLowestPrice(): float
    {
        if (null === $this->lowestPrice) {
            $this->calculateAggregatePrices();
        }

        return $this->lowestPrice;
    }

    public function getHighestPrice(): float
    {
        if (null === $this->highestPrice) {
            $this->calculateAggregatePrices();
        }

        return $this->highestPrice;
    }

    public function getMostCommonPrice(): float
    {
        if (null === $this->mostCommonPrice) {
            $this->calculateAggregatePrices();
        }

        return $this->mostCommonPrice;
    }

    private function calculateAggregatePrices()
    {
        $lowestPrice = PHP_INT_MAX;
        $highestPrice = 0;
        $priceHistogram = [];
        foreach ($this->days as $date => $trips) {
            foreach ($trips as $trip) {
                if ($trip->getPrice() > $highestPrice) {
                    $highestPrice = $trip->getPrice();
                }

                if ($trip->getPrice() < $lowestPrice) {
                    $lowestPrice = $trip->getPrice();
                }

                if (isset($priceHistogram[(string) $trip->getPrice()])) {
                    $priceHistogram[(string) $trip->getPrice()] += 1;
                } else {
                    $priceHistogram[(string) $trip->getPrice()] = 1;
                }
            }
        }

        $this->lowestPrice = $lowestPrice;
        $this->highestPrice = $highestPrice;
        $this->mostCommonPrice = array_search(max($priceHistogram), $priceHistogram);
    }
}
