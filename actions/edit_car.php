<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $brand = htmlspecialchars(trim($_POST['brand']));
    $model = htmlspecialchars(trim($_POST['model']));
    $year = intval($_POST['year']);
    $transmission = htmlspecialchars(trim($_POST['transmission']));
    $fuel_type = htmlspecialchars(trim($_POST['fuel_type']));
    $passengers = intval($_POST['passengers']);
    $daily_price_huf = intval($_POST['daily_price_huf']);
    $image = filter_var(trim($_POST['image']), FILTER_VALIDATE_URL);

    $json_file = 'datas/cars.json';
    if (file_exists($json_file)) {
        $json_data = file_get_contents($json_file);
        $cars = json_decode($json_data, true);  
    } else {
        echo "Hiba: Nem található a cars.json fájl!";
        exit;
    }
    $carId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $carIndex = null;
    foreach ($cars as $index => $c) {
        if ($c['id'] === $carId) {
            $carIndex = $index;
            break;
        }
    }

    if ($carIndex === null) {
        echo "<p>Az autó nem található!</p>";
        exit;
    }

    $cars[$carIndex]['brand'] = $brand ?: $cars[$carIndex]['brand'];
    $cars[$carIndex]['model'] = $model ?: $cars[$carIndex]['model'];
    $cars[$carIndex]['year'] = $year ?: $cars[$carIndex]['year'];
    $cars[$carIndex]['transmission'] = $transmission ?: $cars[$carIndex]['transmission'];
    $cars[$carIndex]['fuel_type'] = $fuel_type ?: $cars[$carIndex]['fuel_type'];
    $cars[$carIndex]['passengers'] = $passengers ?: $cars[$carIndex]['passengers'];
    $cars[$carIndex]['daily_price_huf'] = $daily_price_huf ?: $cars[$carIndex]['daily_price_huf'];
    $cars[$carIndex]['image'] = $image ?: $cars[$carIndex]['image'];

    $updated_json = json_encode($cars, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($json_file, $updated_json) === false) {
        echo "Hiba: Nem sikerült elmenteni az adatokat a JSON fájlba!";
        exit;
    }

    header("Location: ../index.php");
    exit;
}

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
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autó szerkesztése</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin-action.css">
</head>
<body>
    <nav id="menu">
            <div class="d-flex">
                <div id="main"><a href="../index.php" style="color: white;">Főoldal</a></div>
                <div id="buttons" class="ml-auto">
                    <?php
                        if (isset($_SESSION['username'])) { ?>
                            <?= $_SESSION['username'] ?>
                            <a href="user_details.php?username=<?= $_SESSION['username'] ?>">
                                <img src="https://static.vecteezy.com/system/resources/previews/003/766/124/non_2x/man-s-silhouette-color-icon-user-isolated-illustration-vector.jpg">
                            </a>
                            <?php echo "<button><a href='../actions/logout.php'>Kijelentkezés</a></button>"; ?>
                        <?php } else { ?>
                            <button><a href="actions/login.php">Bejelentkezés</a></button>
                            <button><a href="actions/signin.php">Regisztráció</a></button>
                        <?php }
                    ?>
                </div>
            </div>
        </nav>
    <section id="admin-action">
        <h1><strong>AUTÓ ADATAINAK SZERKESZTÉSE:</strong></h1>
        <p>Az eredeti adat olvasható a sorokban, azt töltse ki, amelyet módosítani kívánja!</p>
        <form method="POST" action="" style="display: inline;">
            <div>
                <label for="brand">Márka</label>
                <input type="text" id="brand" name="brand" placeholder="<?= $car['brand']?>">
            </div>
            <div>
                <label for="model">Modell</label>
                <input type="text" id="model" name="model" placeholder="<?= $car['model']?>">
            </div>
            <div>
                <label for="year">Év</label>
                <input type="number" id="year" name="year" min="1000" max="<?= date("Y")?>" placeholder="<?= $car['year']?>">
            </div>
            <div>
                <label for="transmission">Váltó típusa</label>
                <select name="transmission">
                    <option value=""><?=$car['transmission']?></option>
                    <option value="Manual">Manuális</option>
                    <option value="Automatic">Automata</option>
                </select>
            </div>
            <div>
                <label for="fuel_type">Üzemanyag típusa</label>
                <input type="text" id="fuel_type" name="fuel_type" placeholder="<?= $car['fuel_type']?>">
            </div>
            <div>
                <label for="passengers">Utasok száma</label>
                <input type="number" id="passengers" name="passengers" min="0" max="10" placeholder="<?= $car['passengers']?>">
            </div>
            <div>
                <label for="daily_price_huf">Napi ár Forintban</label>
                <input type="number" id="daily_price_huf" name="daily_price_huf" placeholder="<?= $car['daily_price_huf']?>">
            </div>
            <div>
                <img src="<?= $car['image'] ?>">
                <label for="image">Új kép hivatkozása</label>
                <input type="url" id="image" name="image"> 
            </div>
            <div id="submit-button">
                    <button type="submit" class="styled">Mentés</button>
                </div>
        </form>
    </section>
</body>
</html>