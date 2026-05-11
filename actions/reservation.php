<?php
session_start(); ?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foglalás</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/details.css">
</head>
<body>
    <div id="reservation">
        <?php if (!empty($_POST['id']) && !empty($_POST['startDate']) && !empty($_POST['endDate'])) {
            $carId = $_POST['id'];
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];

            $jsonData = file_get_contents('datas/cars.json');
            $cars = json_decode($jsonData, true);

            $jsonData2 = file_get_contents('datas/users.json');
            $users = json_decode($jsonData2, true);

            if (strtotime($startDate) > strtotime($endDate) || strtotime($startDate) < strtotime(date('Y-m-d'))) { ?>
                <img src="https://static-00.iconduck.com/assets.00/failure-icon-1024x1024-lpdgzomh.png">
                <?php echo "<p>Helytelen adatok. Kérjük, próbálja újra!</p>"; ?>
                <button><a href="../car_details.php?id=<?=$carId?>">Vissza</a></button>
            <?php } else {
                foreach ($cars as &$car) {
                    if ($car['id'] == $carId) {
                        $isConflict = false;
                        foreach ($car['bookings'] as $b) {
                            $existingStart = strtotime($b['start_date']);
                            $existingEnd = strtotime($b['end_date']);
                            $newStart = strtotime($startDate);
                            $newEnd = strtotime($endDate);
            
                            // Átfedés ellenőrzése
                            if ($newStart <= $existingEnd && $newEnd >= $existingStart) {
                                $isConflict = true;
                                break; 
                            }
                        }
            
                        if ($isConflict) { ?>
                                <img src="https://static-00.iconduck.com/assets.00/failure-icon-1024x1024-lpdgzomh.png">
                            <?php echo "<p>HIBA: Az időszak ütközik egy meglévő foglalással.</p>"; ?>
                            <button><a href="../car_details.php?id=<?=$carId?>">Vissza</a></button>
                        <?php } else {
                            // Foglalás hozzáadása
                            $car['bookings'][] = [
                                'start_date' => $startDate,
                                'end_date' => $endDate
                            ]; 

                            foreach ($users as &$user) {
                                if ($user['username'] == $_SESSION['username']) {
                                    $user['bookings'][] = [
                                        'id' => $carId,
                                        'start_date' => $startDate,
                                        'end_date' => $endDate
                                    ]; 
                                    break;
                                }
                            }
                            ?>
                            <img src="https://static-00.iconduck.com/assets.00/success-icon-2048x2048-8woikx05.png">
                            <?php 
                            if (strtotime($startDate) == strtotime($endDate)) {
                               $price = $car['daily_price_huf'];
                            } else {
                               $price = ((strtotime($endDate) - strtotime($startDate)) / 86400 * $car['daily_price_huf']);
                            }
                            ?>                           
                            <p>Foglalás sikeresen hozzáadva:</p> 
                            <ul>
                                <li><?=$startDate ."-tól " . $endDate . "-ig"?></li>
                                <li><?=$car['brand'] . " " . $car['model']?></li>
                                <li><?="Ár összesen: " . number_format($price, 0, ',', ' ') . " Ft"?></li>
                            </ul>
                            <button><a href="../index.php">Vissza a főoldalra</a></button>
                        <?php }
                        break; 
                    }
                }
            }

            file_put_contents('datas/cars.json', json_encode($cars, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            file_put_contents('datas/users.json', json_encode($users, JSON_PRETTY_PRINT));
        } else {
            echo '<p style="text-align: center;">Hiányzó adatok. Kérjük, próbálja újra!</p>'; ?>
        <?php }
        ?>
    </div>
</body>
</html>
