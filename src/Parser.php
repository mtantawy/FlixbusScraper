<?php

namespace FlixbusScraper;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;

class Parser
{
    private $page;

    public function __construct(DocumentElement $page)
    {
        $this->page = $page;
    }

    public function getTrips(): array
    {
        $trips = [];
        $rides = $this->page->findAll('css', '.ride-available');
        foreach ($rides as $ride) {
            if (! $this->isBookable($ride)) {
                continue;
            }

            $trip = new Trip();
            $trip->setDepartureDateTime(
                $ride->getAttribute('data-departure-date'),
                $ride->find('css', '.departure')->getText()
            );
            $trip->setArrivalDateTime(
                $ride->getAttribute('data-departure-date'),
                $ride->find('css', '.arrival')->getText()
            );
            $trip->setDepartureStation($ride->find('css', '.ride__station--departure')->getText());
            $trip->setArrivalStation($ride->find('css', '.ride__station--arrival')->getText());
            $trip->setDuration($trip->getDepartureDateTime(), $trip->getArrivalDateTime());
            $trip->setIsDirect($ride->find('css', '.transf-num')->getText());
            $trip->setPrice($ride->find('css', '.total')->getText());

            $trips[] = $trip;
        }

        return $trips;
    }

    private function isBookable(NodeElement $ride): bool
    {
        return null !== $ride->find('css', '.total');
    }
}
