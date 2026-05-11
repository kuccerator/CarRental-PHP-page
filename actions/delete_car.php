<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $carId = isset($_POST['car_id']) ? (int)$_POST['car_id'] : null;
    if ($carId === null) {
        die('Érvénytelen azonosító!');
    }

    $filePath = 'datas/cars.json';
    $jsonData = file_get_contents($filePath);
    $cars = json_decode($jsonData, true);

    if ($cars === null) {
        die('Nem sikerült a JSON fájl beolvasása.');
    }

    // Az autó eltávolítása azonosító alapján
    $updatedCars = array_filter($cars, function ($car) use ($carId) {
        return $car['id'] !== $carId;
    });

    if (file_put_contents($filePath, json_encode(array_values($updatedCars), JSON_PRETTY_PRINT)) === false) {
        die('Nem sikerült a fájl frissítése.');
    }
    header('Location: ../index.php');
    exit();
}
?>
