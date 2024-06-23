<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['mailuid'];
    $password = $_POST['pwd'];

    $sql = "SELECT * FROM staff WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($staff && password_verify($password, $staff['password'])) {
        $_SESSION['staff_id'] = $staff['id'];
        header("Location: staff.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}

// Close the PDO connection
$pdo = null;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Staff Login</title>
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,600,700,700i&display=swap" rel="stylesheet">
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/login.css" rel="stylesheet">
    </head>
    <body>
        <div class="form-group">
            <form action="login.php" method="post">
                <label>
                    <h2>Staff Sign In</h2>
                </label>
                <label for="exampleInputEmail1">Email address</label>
                <input type="text" class="" id="exampleInputEmail1" name="mailuid" placeholder="example@gmail.com">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="" id="exampleInputPassword1" name="pwd" placeholder="********">
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
  
