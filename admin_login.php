<?php
session_start();

$defaultAdminUsername = 'admin';
$defaultAdminPassword = 'adminpassword';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $defaultAdminUsername && $password === $defaultAdminPassword) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,600,700,700i&display=swap" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
</head>
<body>
    <div class="form-group">
        <form action="admin_login.php" method="post">
            <label>
                <h2>Admin Sign In</h2>
            </label>
            <label for="adminUsername">Username</label>
            <input type="text" class="" id="adminUsername" name="username" placeholder="Username" required>
            <label for="adminPassword">Password</label>
            <input type="password" class="" id="adminPassword" name="password" placeholder="********" required>
            <div class="form-footer">
                <div class="form-check">
                    <input type="checkbox" class="check-input" id="rememberMe" name="rememberMe">
                    <label class="check-label" for="rememberMe">Remember me</label>
                </div>
                <a href="index.html" class="go-back">Go back?</a>
            </div>
            <?php
                if (isset($error)) {
                    echo '<div class="error">'.$error.'</div>';
                }
            ?>
            <button type="submit" class="btn" name="login-submit">Login</button> 
        </form>
    </div>
</body>
</html>

