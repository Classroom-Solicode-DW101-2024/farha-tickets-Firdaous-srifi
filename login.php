
<?php
// demarage de la session
session_start();

//connex m3a la base de donnees
require "config.php";

$error = "";

//t verifiat la formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        try {
            $sql = "SELECT id, firstname, password FROM users WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            //Vérification dyal l'authentification
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['firstname'];
                header("Location: main.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FarhaEvents</title>
    <link rel="stylesheet" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="main.php" class="logo">
                <img src="images/2.png" alt="FarhaEvents Logo">
            </a>
        </div>
    </nav>

    <div class="auth-container">
    <h1>Login</h1>
    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form action="login.php" method="POST" class="auth-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <div class="auth-links">
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
</div>
</body>
</html>