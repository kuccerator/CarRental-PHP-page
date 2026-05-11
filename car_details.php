<?php
session_start(); 
$jsonData = file_get_contents('datas/cars.json');
$cars = json_decode($jsonData, true);
$carId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$car = null;
foreach ($cars as $c) {
    if ($c['id'] === $carId) {
        $car = $c;
        break;
    }
}
if (!$car) {
    echo "<p>Az autó nem található!</p>";
    exit;
}

// Dátum kiválasztása: Jóváhagyás gomb
$selectedStartDate = '';
$selectedEndDate = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedStartDate = $_POST['startDate'] ?? '';
    $selectedEndDate = $_POST['endDate'] ?? '';            
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></title>
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
    <div class="container my-5">
        <div class="row">
            <!-- Adatok az autóról -->
            <div class="col-md-6">
                <img src="<?= htmlspecialchars($car['image']) ?>" class="img-fluid" alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>">
                <?php if (isset($_SESSION['username'])) { ?>
                    <p>Kiválasztott dátum: </p>
                    <?php 
                    if ($selectedStartDate && $selectedEndDate) { ?>
                        <p class="select"><?= htmlspecialchars($selectedStartDate) . '-tól ' . htmlspecialchars($selectedEndDate) . '-ig'; ?>
                    </p><?php
                    } else { ?>
                        <p class="select">Még nincs kiválasztva dátum, válassz!</p>
                    <?php }
                    ?>
                <?php } ?>
            </div>
            <div class="col-md-6">
                <h1><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h1>
                <ul class="list-unstyled">
                    <li><strong>Üzemanyag:</strong> <?= htmlspecialchars($car['fuel_type']) ?></li>
                    <li><strong>Váltó:</strong> <?= htmlspecialchars($car['transmission'] === "Automatic" ? "Automata" : "Manuális") ?></li>
                    <li><strong>Gyártási év:</strong> <?= htmlspecialchars($car['year']) ?></li>
                    <li><strong>Férőhelyek száma:</strong> <?= htmlspecialchars($car['passengers']) ?></li>
                </ul>
                <h2><?= htmlspecialchars(number_format($car['daily_price_huf'], 0, ',', ' ')) ?> Ft/nap</h2>
                <?php if (isset($_SESSION['username'])) { ?>
                    <!-- Gombok -->
                    <div id="gombok">
                        <button class="btn btn-warning" id="datePickerBtn">Dátum kiválasztása</button>
                        <?php if (!empty($selectedStartDate) && !empty($selectedEndDate)) { ?>
                            <form method="POST" action="actions/reservation.php">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($car['id']) ?>">
                                <input type="hidden" name="startDate" value="<?= htmlspecialchars($selectedStartDate) ?>">
                                <input type="hidden" name="endDate" value="<?= htmlspecialchars($selectedEndDate) ?>">
                                <button type="submit" class="btn btn-warning">Lefoglalom</button>
                            </form>
                        <?php } else { ?>
                            <p id="warning">Kérjük, válassza ki a kezdő és záró dátumokat a foglaláshoz!</p>
                        <?php } ?>
                    </div>
                <?php } ?>
                <!-- "Dátum kiválasztása" gomb után megjelenő form -->   
                <form method="POST" action=""  name="date" style="display: none;" id="datePickerForm">
                    <div class="form-group">
                        <label for="startDate">Kezdő dátum</label>
                        <input type="date" class="form-control" id="startDate" name="startDate" required>
                    </div>
                    <div class="form-group">
                        <label for="endDate">Befejező dátum</label>
                        <input type="date" class="form-control" id="endDate" name="endDate" required>
                    </div>
                    <button action="submit" class="btn">Jóváhagyom</button>
                </form>
                <!-- Fenti form megjelenítése -->
                <script>
                    document.getElementById('datePickerBtn').addEventListener('click', function() {
                        document.getElementById('datePickerForm').style.display = 'block';
                    });
                </script>
            </div>
        </div>
    </div>
</body>
</html>
