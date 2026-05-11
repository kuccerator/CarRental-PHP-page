<?php
session_start(); 
$jsonFile = 'datas/users.json';
$wrong = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);
            if (file_exists($jsonFile)) {
                $users = json_decode(file_get_contents($jsonFile), true);
                $admin_email = 'admin@ikarrental.hu';
                $admin_password = 'admin';

                foreach ($users as $user) {
                    if ($email == $admin_email && $user['email'] === $email && $password == $admin_password) {
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['isadmin'] = true; 
                        header("Location: ../index.php"); 
                        exit();
                    } else if ($user['email'] === $email && password_verify($password, $user['password_hash'])) {
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['isadmin'] = false; 
                        header("Location: ../index.php"); 
                        exit();
                    }
                }
            }
            $wrong = true;
        }
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/users-action.css">
</head>
<body>
    <section id="user-action">
    <h1>🔑 <i>BEJELENTKEZEK</i> 🔑</h1>
        <form action="" method="POST">
            <div>
                <label for="username">1️⃣ Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">2️⃣ Jelszó</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div id="submit-button">
                <button type="submit" class="styled">Bejelentkezés</button>
            </div>
            <?php if ($wrong) { ?> 
                <p class="wrong">Helytelen email vagy jelszó!</p>
            <?php } ?>
        </form>    
    </section>
</body>
</html>