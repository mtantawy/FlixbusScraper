<?php

namespace FlixbusScraper;

use DateTimeInterface;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;

class Scraper
{
    public function __construct(DateTimeInterface $rideDate)
    {
        $this->session = new Session(new ChromeDriver('http://localhost:9222', null, ''));
        $this->session->start();
        $this->session->visit(
            sprintf('https://shop.flixbus.com/search?rideDate=%s&adult=1&children=0&departureCity=88&arrivalCity=1374&_locale=en', $rideDate->format('d.m.Y'))
        );
    }

    public function scrapTrips(): array
    {
        if ($this->session->wait(
            10000,
            '$(".ride-available").children().length > 0'
        )) {
            $parser = new Parser($this->session->getPage());
            return $parser->getTrips();
        }
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
