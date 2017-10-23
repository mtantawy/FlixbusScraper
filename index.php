<?php

require_once __DIR__.'/vendor/autoload.php';

use FlixbusScraper\Scraper;

$scraper = new Scraper(new DateTime());
$trips = $scraper->scrapTrips();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Flixbus Timetable</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>departs<br/>arrives</th>
                <th>price<br/>duration</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($trips as $trip) {
                echo '<tr>';
                echo '<td>' . $trip->getDepartureDateTime()->format('h:i') . '<br/>' . $trip->getArrivalDateTime()->format('h:i') . '</td>';
                echo '<td>' . $trip->getPrice() . '<br/>' . $trip->getDuration()->h . ' Hrs' . ($trip->isDirect() ? ' ✔':' ✗') . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</body>
</html>
