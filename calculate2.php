<?php
// 入力データの取得
$location = $_POST['location'];
$temperature = $_POST['temperature'];
$humidity = $_POST['humidity'];
$wind_speed = $_POST['wind_speed'];

// データベース接続設定
$host = 'localhost';
$dbname = 's2322103';
$user = 's2322103';
$password = 'MX5BJvcP';

// データベースに接続
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

if (!$conn) {
    echo json_encode([
        'status' => 'error',
        'message' => 'データベース接続に失敗しました。',
    ]);
    exit;
}

// 地名から緯度と経度を取得
$result = pg_query_params($conn, 'SELECT lat, lon FROM weather_data WHERE name = $1', [$location]);

if (!$result || pg_num_rows($result) == 0) {
    echo json_encode([
        'status' => 'error',
        'message' => '地名が見つかりません。',
        'debug' => 'Location: ' . $location,
    ]);
    exit;
}

$row = pg_fetch_assoc($result);
$lat = $row['lat'];
$lon = $row['lon'];

// 体感温度の計算
$A = 1.76 + 1.4 * pow($wind_speed, 0.75);
$feels_like_temperature = 37 - (37 - $temperature) / (0.68 - 0.0014 * $humidity + 1 / $A) - 0.29 * $temperature * (1 - $humidity / 100);

echo json_encode([
    'status' => 'success',
    'lat' => $lat,
    'lon' => $lon,
    'feels_like_temperature' => $feels_like_temperature
]);

// データベース接続を閉じる
pg_close($conn);
?>