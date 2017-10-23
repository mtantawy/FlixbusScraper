<?php

namespace FlixbusScraper;

use Behat\Mink\Mink;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;
use Selenium\Client as SeleniumClient;

class Scraper
{
    public function __construct()
    {
        $this->session = new Session(new ChromeDriver('http://localhost:9222', null, ''));
        $this->session->start();
        $this->session->visit('https://shop.flixbus.com/search?rideDate=03.11.2017&adult=1&children=0&departureCity=88&arrivalCity=1374&_locale=en');
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

    public function __destruct()
    {
        if ($this->session instanceof Session) {
            try {
                $this->session->stop();
            } catch (Exception $e) {
                ;
            }
        }
    }
}
