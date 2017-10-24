<?php

require_once __DIR__.'/vendor/autoload.php';

use FlixbusScraper\Scraper;

$startDateTime = new DateTimeImmutable();
$endDateTime = $startDateTime->add(new DateInterval('P2D'));
$period = new DatePeriod($startDateTime, new DateInterval('P1D'), $endDateTime);

$days = [];
foreach ($period as $date) {
    $scraper = new Scraper($date);
    $days[$date->format('d.m.Y')] = $scraper->scrapTrips();
    $scraper->stopSession();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Flixbus Timetable</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <?php
                foreach ($days as $dateString => $trips) {
                    echo '<th>' . $dateString . '</th>';
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php foreach ($days as $dateString => $trips) : ?>
                <td valign="top">
                    <table>
                        <thead>
                            <tr>
                                <th>departs<br/>arrives</th>
                                <th>price<br/>duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($trips as $trip) {
                                if ($trip->getDepartureDateTime()->format('d.m.Y') !== $dateString) {
                                    continue;
                                }

                                echo '<tr>';
                                echo '<td>' . $trip->getDepartureDateTime()->format('h:i') . '<br/>' . $trip->getArrivalDateTime()->format('h:i') . '</td>';
                                echo '<td>' . $trip->getPrice() . ' ' . $trip->getPriceCurrency() . '<br/>' . $trip->getDuration()->h . ' Hrs' . ($trip->isDirect() ? ' ✔':' ✗') . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
                </td>
            </tr>
        </tbody>
    </table>
</body>
<style type="text/css">
    table {
        border-collapse: collapse;
    }

    table, th, td {
        border: 1px solid black;
    }
</style>
</html>
