<?php
session_start(); 
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iKarRental</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <nav id="menu">
        <div class="d-flex">
            <div><a href="index.php" style="color: white;">iKarRental</a></div>
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
    <h1>Kölcsönözz autókat<br>könnyedén!</h1>
    <form id="form" method="GET">
        <div class="container text-center">
            <div class="row">
                <div class="col"></div>
                <div class="col-10">
                    <!-- Férőhely -->
                    <input type="number" id="ferohely" name="passengers" value="0" min="0" max="10">
                    <span>férőhely</span>

                    <!-- Dátum -->
                    <input type="date" name="start_date" class="formed">
                    <span>-tól</span>
                    <input type="date" name="end_date" class="formed">
                    <span>-ig</span>

                    <!-- Váltó típusa -->
                    <select class="formed" name="transmission">
                        <option value="">Váltó típusa</option>
                        <option value="Manual">Manuális</option>
                        <option value="Automatic">Automata</option>
                    </select>

                    <!-- Ár -->
                    <input type="number" name="min_price" placeholder="14.000" class="price"> -
                    <input type="number" name="max_price" placeholder="21.000" class="price">
                    <span id="ft">Ft</span>
                </div>
                <div class="col"></div>
            </div>
            <div class="row">
                <div class="col"></div>
                <div class="col-6">
                    <!-- Szűrés -->
                    <button type="submit" class="styled">Szűrés</button>
                    <?php if (isset($_SESSION['isadmin']) && $_SESSION['isadmin']) { ?>
                        <button type="submit" class="styled"><a href="actions/insert_car.php">Autó hozzáadása</a></button>
                    <?php } ?>
                </div>
                <div class="col"></div>
            </div>
        </div>
    </form>

    <?php
    $jsonData = file_get_contents('datas/cars.json');
    $cars = json_decode($jsonData, true);
    $filteredCars = $cars;

    if ($_GET) {
        // Férőhely
        $passengers = isset($_GET['passengers']) && $_GET['passengers'] !== '' ? (int)$_GET['passengers'] : null;

        // Váltó típusa
        $transmission = isset($_GET['transmission']) && $_GET['transmission'] !== '' ? $_GET['transmission'] : null;

        // Árintervallum
        $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : null;

        // Dátum
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

        // Szűrés
        $filteredCars = array_filter($cars, function ($car) use ($passengers, $transmission, $minPrice, $maxPrice, $startDate, $endDate) {
            if ($passengers !== null && $car['passengers'] < $passengers) {
                return false;
            }
        
            if ($transmission !== null && $car['transmission'] !== $transmission) {
                return false;
            }
            
            if ($minPrice !== null && $car['daily_price_huf'] < $minPrice) {
                return false;
            }
            if ($maxPrice !== null && $car['daily_price_huf'] > $maxPrice) {
                return false;
            }

            if ($startDate && $endDate) {
                $startDateObj = strtotime($startDate);
                $endDateObj = strtotime($endDate);

                foreach ($car['bookings'] as $booking) {
                    $bookingStart = strtotime($booking['start_date']);
                    $bookingEnd = strtotime($booking['end_date']);
                    if (($startDateObj >= $bookingStart && $startDateObj <= $bookingEnd) || ($endDateObj >= $bookingStart && $endDateObj <= $bookingEnd)) {
                        return false; 
                    }
                }
            }
            return true;
        });
    }
    ?>
    <?php if ($filteredCars): ?>
        <div id="kartyak" class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 g-4">
            <?php foreach ($filteredCars as $car): ?>
                <div class="col">
                    <a href="car_details.php?id=<?= htmlspecialchars($car['id']) ?>" class="card-link">
                        <div class="card <?php if (isset($_SESSION['isadmin']) && $_SESSION['isadmin']) echo "larger";?>">
                            <img src="<?= htmlspecialchars($car['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h5>
                                <p class="card-text">
                                    <?php if ($car['transmission'] === "Automatic") echo "automata"; else if ($car['transmission'] === "Manual") echo "manuális"; ?>
                                    - <?= htmlspecialchars($car['passengers']) ?> férőhely <br>
                                    <h6><?= htmlspecialchars(number_format($car['daily_price_huf'], 0, ',', ' ')) ?> Ft</h6>
                                    <?php if (isset($_SESSION['isadmin']) && $_SESSION['isadmin']) { ?>
                                        <a href="actions/edit_car.php?id=<?= htmlspecialchars($car['id']) ?>" class="card-link"><button class="styled admin-button grey"> Szerkeszt </button></a>
                                        <form method="POST" action="actions/delete_car.php" style="display: inline;">
                                            <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['id']) ?>">
                                            <button type="submit" class="styled admin-button red">Töröl</button>
                                        </form>
                                    <?php } ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Nincs megjeleníthető autó.</p>
    <?php endif; ?>

</body>
</html>