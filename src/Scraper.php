<?php

namespace FlixbusScraper;

use DateTimeInterface;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;

class Scraper
{
    private $rideDate;

    public function __construct(DateTimeInterface $rideDate)
    {
        $this->rideDate = $rideDate;
        $this->session = new Session(new ChromeDriver('http://localhost:9222', null, ''));
        $this->session->start();
        $this->session->visit(
            sprintf('https://shop.flixbus.com/search?rideDate=%s&adult=1&children=0&departureCity=88&arrivalCity=1374&_locale=en', $this->rideDate->format('d.m.Y'))
        );
    }

    public function scrapTrips(): array
    {
        if ($this->session->wait(
            10000,
            '$(".ride-available").children().length > 0'
        )) {
            $parser = new Parser($this->session->getPage());
            return $this->removeTripsForOtherDates($parser->getTrips());
        }
    }

    private function removeTripsForOtherDates(array $trips): array
    {
        foreach ($trips as $key => $trip) {
            if ($this->rideDate->format('d.m.Y') !== $trip->getDepartureDateTime()->format('d.m.Y')) {
                unset($trips[$key]);
            }
        }

        return array_values($trips);
    }

    public function stopSession()
    {
        if ($this->session instanceof Session && $this->session->isStarted()) {
            try {
                $this->session->stop();
            } catch (Exception $e) {
                ;
            }
        }
    }

    public function __destruct()
    {
        $this->stopSession();
    }
}
