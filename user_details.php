<?php
session_start(); 

$jsonData = file_get_contents('datas/users.json');
$users = json_decode($jsonData, true);
$username = isset($_GET['username']) ? $_GET['username'] : null;
$user = null;

$jsonData2 = file_get_contents('datas/cars.json');
$cars = json_decode($jsonData2, true);

foreach ($users as $u) {
    if ($u['username'] === $username) {
        $user = $u;
        break;
    }
}
if (!$user) {
    echo "<p>A felhasználó nem található!</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiók adatai</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/details.css">
</head>
<body>
<nav id="menu">
        <div class="d-flex">
            <div id="main"><a href="index.php" style="color: white;">Főoldal</a></div>
            <div id="buttons" class="ml-auto">
                <?php
                    if (isset($_SESSION['username'])) { ?>
                        <?= $_SESSION['username'] ?>
                        <a href="user_details.php?username=<?= $_SESSION['username'] ?>">
                            <img src="https://static.vecteezy.com/system/resources/previews/003/766/124/non_2x/man-s-silhouette-color-icon-user-isolated-illustration-vector.jpg">
                        </a>
                        <?php echo "<button><a href='actions/logout.php'>Kijelentkezés</a></button>"; ?>
                    <?php } else { ?>
                        <button><a href="actions/login.php">Bejelentkezés</a></button>
                        <button><a href="actions/signin.php">Regisztráció</a></button>
                    <?php }
                ?>
            </div>
        </div>
    </nav>
<div id="user" class="container my-5">
        <div class="row" id="center">
            <div class="col-md-6">
                <img src="https://cdn.pixabay.com/photo/2020/07/01/12/58/icon-5359553_640.png" id="profile-picture" class="img-fluid">
            </div>
            <div class="col-md-6">
                <strong>Bejelentkezve, mint:</strong>
                <h1><?= htmlspecialchars($user['username']) ?></h1>
            </div>
        </div>
        <div class="row">
            <?php 
            $none = false;            
            $empty = true;
            if (isset($_SESSION['isadmin']) && !$_SESSION['isadmin']) { 
                $empty = false; ?>
                <strong style="font-size: larger;">Foglalásaim:</strong>
                <div id="kartyak" class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 g-4">
                    <?php 
                    if (!empty($user['bookings'])) { 
                        foreach ($user['bookings'] as $booking) { 
                            $carId = $booking['id'];
                            $car = null;

                            foreach ($cars as $c) {
                                if ($c['id'] == $carId) $car = $c;
                            } ?>

                            <div class="card">
                                <img src="<?= htmlspecialchars($car['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h5>
                                    <p class="card-text">
                                        <p><?= $booking['start_date'] . "-tól " . $booking['end_date'] . "-ig" ?></p>
                                        <?php 
                                        if (strtotime($booking['start_date']) == strtotime($booking['end_date'])) {
                                            $price = number_format($car['daily_price_huf']);
                                        } else {
                                            $days = (strtotime($booking['end_date']) - strtotime($booking['start_date'])) / 86400;
                                            $price = number_format($days * $car['daily_price_huf']);
                                        } ?>
                                        <h6><?= $price ?> Ft</h6>
                                    </p>
                                </div>
                            </div>
                        <?php } 
                    } else $none = true; ?>
                </div>
            <?php } else  if (isset($_SESSION['isadmin']) && $_SESSION['isadmin']) { ?>
                <strong style="font-size: larger;">Összes foglalás:</strong>
                <div id="kartyak" class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 g-4">
                    <?php 
                    foreach ($users as $user) {
                        foreach ($user['bookings'] as $booking) { 
                            $empty = false;
                            $carId = $booking['id'];
                            $car = null;

                            foreach ($cars as $c) {
                                if ($c['id'] == $carId) $car = $c;
                            } ?>
                            <div class="card admin">
                                <img src="<?= htmlspecialchars($car['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h5>
                                    <p class="card-text">
                                        <p><?= $user['username'] . ":<br>" . $booking['start_date'] . "-tól " . $booking['end_date'] . "-ig" ?></p>
                                        <?php 
                                        if (strtotime($booking['start_date']) == strtotime($booking['end_date'])) {
                                            $price = number_format($car['daily_price_huf']);
                                        } else {
                                            $days = (strtotime($booking['end_date']) - strtotime($booking['start_date'])) / 86400;
                                            $price = number_format($days * $car['daily_price_huf']);
                                        } ?>
                                        <h6><?= $price ?> Ft</h6>
                                        <form method="POST" action="actions/delete_reservation.php" style="display: inline;">
                                            <input type="hidden" name="username" value="<?= htmlspecialchars($user['username']) ?>">
                                            <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['id']) ?>">
                                            <input type="hidden" name="start_date" value="<?= htmlspecialchars($booking['start_date']) ?>">
                                            <input type="hidden" name="end_date" value="<?= htmlspecialchars($booking['end_date']) ?>">
                                            <button type="submit" class="styled admin-button red">Töröl</button>
                                        </form>
                                    </p>
                                </div>
                            </div>
                        <?php } 
                    }?>
                </div>
            <?php }?>
        </div>
        <div class="row">
            <?php if ($none) echo "Nincs foglalásod még!"; ?>
            <?php if ($empty) echo "Nincs foglalás!"; ?>
        </div>
</div>
</body>
</html>