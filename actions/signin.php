<?php
    session_start(); 
    $jsonFile = 'datas/users.json';
    $wrong_pass = false; $wrong_data = false;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $password1 = htmlspecialchars($_POST['password1']);
        if ($password !== $password1) {
            $wrong_pass = true;
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
            $users = [];
            if (file_exists($jsonFile)) {
                $users = json_decode(file_get_contents($jsonFile), true);
            }
            $users[] = [
                'username' => $username,
                'email' => $email,
                'password_hash' => $hashedPassword
            ];
            // Adatok mentése
            foreach ($users as $user) {
                if (!($user['username'] == $username || $user['email'] == $email || password_verify($password, $user['password_hash']))) {
                    file_put_contents($jsonFile, json_encode($users, JSON_PRETTY_PRINT));
                    header("Location: login.php"); 
                    exit(); 
                }
                else {
                    $wrong_data = true;
                    break;
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/users-action.css">
</head>
<body>
    <section>
        <h1>🧾 <i>REGISZTRÁLOK</i> 🧾</h1>
        <form action="" method="POST">
            <div>
                <label for="username">Felhasználónév 💁🏼</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="email">E-mail 📩</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Jelszó 🔐</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="password">Jelszó mégegyszer ✍🏼</label>
                <input type="password" id="password1" name="password1" required>
            </div>
            <div id="submit-button">
                <button type="submit" class="styled">Regisztráció</button>
            </div>
            <?php if ($wrong_pass) { ?>
                <p class="wrong">HIBA: A két jelszó nem egyezik!</p>
            <?php } else if ($wrong_data) { ?>
                <p class="wrong">Használt felhasználónév, email vagy jelszó!</p>
            <?php } ?>
        </form>

    </section>
</body>
</html>

