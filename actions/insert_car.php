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

    // Hibakezelés már benne van a Form-ban a required-del!!

    $json_file = 'datas/cars.json';
    if (file_exists($json_file)) {
        $json_data = file_get_contents($json_file);
        $cars = json_decode($json_data, true);  
    } else {
        echo "Hiba: Nem található a cars.json fájl!";
        exit;
    }

    $new_car = [
        "id" => count($cars) + 1, 
        "brand" => $brand,
        "model" => $model,
        "year" => $year,
        "transmission" => $transmission,
        "fuel_type" => $fuel_type,
        "passengers" => $passengers,
        "daily_price_huf" => $daily_price_huf,
        "image" => $image,
        "bookings" => []
    ];
    $cars[] = $new_car;  

    $updated_json = json_encode($cars, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($json_file, $updated_json) === false) {
        echo "Hiba: Nem sikerült elmenteni az adatokat a JSON fájlba!";
        exit;
    }

    header("Location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Új autó hozzáadása</title>
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
        <h1><strong>ÚJ AUTÓ ADATAI:</strong></h1>
        <form method="POST" action="" style="display: inline;">
            <div>
                <label for="brand">🚗 Márka</label>
                <input type="text" id="brand" name="brand" required>
            </div>
            <div>
                <label for="model">🛠️ Modell</label>
                <input type="text" id="model" name="model" required>
            </div>
            <div>
                <label for="year">⌛ Év</label>
                <input type="number" id="year" name="year" min="1000" max="<?= date("Y") ?>" required>
            </div>
            <div>
                <label for="transmission">🔢 Váltó típusa</label>
                <select name="transmission" required>
                    <option value=""></option>
                    <option value="Manual">Manuális</option>
                    <option value="Automatic">Automata</option>
                </select>
            </div>
            <div>
                <label for="fuel_type">⛽ Üzemanyag típusa</label>
                <input type="text" id="fuel_type" name="fuel_type" required>
            </div>
            <div>
                <label for="passengers">🧑🏽‍🤝‍🧑🏼 Utasok száma</label>
                <input type="number" id="passengers" name="passengers" min="0" max="10" required>
            </div>
            <div>
                <label for="daily_price_huf">💰 Napi ár Forintban</label>
                <input type="number" id="daily_price_huf" name="daily_price_huf" required>
            </div>
            <div>
                <label for="image">📸 Kép hivatkozása</label>
                <input type="url" id="image" name="image" required> 
            </div>
            <div id="submit-button">
                    <button type="submit" class="styled">Hozzáadás</button>
                </div>
        </form>
    </section>
</body>
</html>