<?php 
require "config.php";

$error = "";

// verification wash kayn user deja connecté
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['pending_purchase'])) {
        header("Location: process_purchase.php");
        exit();
    }
    header("Location: main.php");
    exit();
}

//formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else if ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } else {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE mailUser = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $emailExists = $stmt->fetchColumn();
        
        if ($emailExists) {
            $error = "Email already in use";
        } else {
            $stmt = $pdo->query("SELECT MAX(CAST(idUser AS UNSIGNED)) as maxId FROM utilisateur");
            $maxId = $stmt->fetchColumn();
            $newId = $maxId ? $maxId + 1 : 1;
            
            $sql = "INSERT INTO utilisateur (idUser, nomUser, prenomUser, mailUser, motPasse) 
                    VALUES (:idUser, :lastname, :firstname, :email, :password)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':idUser' => $newId,
                ':lastname' => $lastname,
                ':firstname' => $firstname,
                ':email' => $email,
                ':password' => $password
            ]);
            
            if ($result) {
                $_SESSION['user_id'] = $newId;
                $_SESSION['prenomUser'] = $firstname;
                
                if (isset($_GET['redirect']) && $_GET['redirect'] === 'checkout') {
                    header("Location: process_purchase.php");
                } else {
                    header("Location: main.php");
                }
                exit();
            } else {
                $error = "Registration failed";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FarhaEvents</title>
    <link rel="stylesheet" href="css/register.css">
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
    <h1>Register</h1>
    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form action="register.php" method="POST" class="auth-form">
        <div class="form-group">
            <label for="firstname">First Name</label>
            <input type="text" name="firstname" id="firstname" required>
        </div>
        <div class="form-group">
            <label for="lastname">Last Name</label>
            <input type="text" name="lastname" id="lastname" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
        </div>
        <button type="submit">Register</button>
    </form>
    <div class="auth-links">
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>
</body>
</html>