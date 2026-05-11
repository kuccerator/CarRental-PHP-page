<?php
session_start();

$username = isset($_POST['username']) ? $_POST['username'] : null;
$car_id = isset($_POST['car_id']) ? $_POST['car_id'] : null;
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;

if (!$username || !$car_id || !$start_date || !$end_date) {
    die("Hiányzó paraméterek!");
}

$jsonData = file_get_contents('datas/users.json');
$users = json_decode($jsonData, true);

$found = false;
foreach ($users as &$user) {
    if ($user['username'] === $username) {
        foreach ($user['bookings'] as $key => $booking) {
            if (
                $booking['id'] == $car_id &&
                $booking['start_date'] == $start_date &&
                $booking['end_date'] == $end_date
            ) {
                unset($user['bookings'][$key]); 
                $user['bookings'] = array_values($user['bookings']); 
                $found = true;
                break 2;
            }
        }
    }
}

if ($found) {
    file_put_contents('datas/users.json', json_encode($users, JSON_PRETTY_PRINT));
    $_SESSION['message'] = "Foglalás sikeresen törölve!";
} else {
    $_SESSION['error'] = "Foglalás nem található!";
}

header("Location: ../user_details.php?username=" . urlencode($_SESSION['username']));
exit;
