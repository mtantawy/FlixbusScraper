<?php

require_once __DIR__.'/vendor/autoload.php';

use FlixbusScraper\Scraper;
use FlixbusScraper\Analyser;
use FlixbusScraper\ViewHelper;

$startDateTime = isset($_GET['startDate']) ? new DateTimeImmutable($_GET['startDate']) : new DateTimeImmutable();
$endDateTime = isset($_GET['endDate']) ? new DateTimeImmutable($_GET['endDate']) : $startDateTime->add(new DateInterval('P1D'));
$period = new DatePeriod(
    $startDateTime,
    new DateInterval('P1D'),
    $endDateTime->add(new DateInterval('P1D')) // because end-date is excluded in the generated period
);

$days = [];
foreach ($period as $date) {
    $scraper = new Scraper($date);
    $days[$date->format('d.m.Y')] = $scraper->scrapTrips();
    $scraper->stopSession();
}
$analyser = new Analyser($days);
$lowestPrice = $analyser->getLowestPrice();
$highestPrice = $analyser->getHighestPrice();
$mostCommonPrice = $analyser->getMostCommonPrice();

$viewHelper = new ViewHelper();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Flixbus Timetable</title>
</head>
<body>
    <div>
        <form>
            Start: <input type="text" name="startDate" value="<?php echo $startDateTime->format('d.m.Y');?>">
            End: <input type="text" name="endDate" value="<?php echo $endDateTime->format('d.m.Y');?>">
            <input type="submit" value="Get Timetable!">
        </form>
    </div>
    <div>
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

                                    $backgroundColor = $viewHelper->getTripBackgroundColor(
                                        $trip->getPrice(),
                                        $lowestPrice,
                                        $highestPrice,
                                        $mostCommonPrice
                                    );

                                    echo "<tr style='background-color: $backgroundColor;'>";
                                    echo '<td><strong>' . $trip->getDepartureDateTime()->format('h:i') . '</strong><br/>' . $trip->getArrivalDateTime()->format('h:i') . '</td>';
                                    echo '<td><strong>' . $trip->getPrice() . ' ' . $trip->getPriceCurrency() . '</strong><br/>' . $trip->getDuration()->h . ' Hrs' . ($trip->isDirect() ? ' ✔':' ✗') . '</td>';
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
    </div>
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
