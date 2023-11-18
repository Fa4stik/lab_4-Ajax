<?php
if (isset($_GET["vehicle_type"]) && isset($_GET["vehicle_brand"]) && isset($_GET["distance"])) {
    $filename = "вар1.csv";
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $fuelConsumption = 0;

    foreach ($lines as $line) {
        list($type, $brand, $consumption) = str_getcsv($line, ";");
        if ($_GET["vehicle_type"] === $type && $_GET["vehicle_brand"] === $brand) {
            $fuelConsumption = str_replace(',', '.', $consumption);
            break;
        }
    }

    $distance = (float) $_GET["distance"];
    $totalConsumption = ($fuelConsumption * $distance) / 100;
    echo number_format($totalConsumption, 2, '.', '');
} else {
    echo "Необходимо указать тип транспорта, марку и расстояние.";
}
?>
