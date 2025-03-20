<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 's2322103';
$user = 's2322103';
$password = 'MX5BJvcP';

try {
    $dbconn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT name, lon, lat, temperature, humidity, wind_speed FROM weather_data";
    $stmt = $dbconn->prepare($query);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($results as $row) {
        $temperature = $row['temperature'];
        $humidity = $row['humidity'];
        $heatIndex = calculateHeatIndex($temperature, $humidity);
        $row['heat_index'] = $heatIndex;
        $data[] = $row;
    }

    echo json_encode($data);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function calculateHeatIndex($temperature, $humidity) {
    $c1 = -42.379;
    $c2 = 2.04901523;
    $c3 = 10.14333127;
    $c4 = -0.22475541;
    $c5 = -0.00683783;
    $c6 = -0.05481717;
    $c7 = 0.00122874;
    $c8 = 0.00085282;
    $c9 = -0.00000199;

    $heatIndex = $c1 + $c2 * $temperature + $c3 * $humidity + $c4 * $temperature * $humidity + $c5 * $temperature * $temperature + $c6 * $humidity * $humidity + $c7 * $temperature * $temperature * $humidity + $c8 * $temperature * $humidity * $humidity + $c9 * $temperature * $temperature * $humidity * $humidity;

    return $heatIndex;
}
?>
