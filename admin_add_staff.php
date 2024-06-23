<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "staff_promotion";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $religion = $_POST['religion'];
    $position = $_POST['position'];
    $date_of_joining = $_POST['date_of_joining'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $department = $_POST['department'];

    if (!empty($name) && !empty($religion) && !empty($position) && !empty($date_of_joining) && !empty($email) && !empty($password) && !empty($department)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO staff (name, religion, position, date_of_joining, email, password, department) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("sssssss", $name, $religion, $position, $date_of_joining, $email, $hashedPassword, $department);
            if ($stmt->execute()) {
                $success = "Staff account created successfully!";
            } else {
                $error = "Error executing statement: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Error preparing statement: " . $conn->error;
        }
    } else {
        $error = "All fields are required";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="css/admin.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <div class="sidebar">
                <a href="admin_dashboard.php">
                    <i class="fa-solid fa-chart-line icon"></i>
                    <h1>Statistics</h1>
                </a>
                <a href="admin_manage_staff.php">
                    <i class="fa-solid fa-user-tie icon"></i>
                    <h1>Manage Staff</h1>
                </a>
                <a href="admin_promote_staff.php">
                    <i class="fa-solid fa-arrow-up icon"></i>
                    <h1>Promote Staff</h1>
                </a>
                <a href="admin_add_staff.php" class="active">
                    <i class="fa-solid fa-user-plus icon"></i>
                    <h1>Add Staff</h1>
                </a>
                <span></span>
                <a href="index.html">
                    <i class="fa-solid fa-house icon"></i>
                    <h1>Home</h1>
                </a>
                <a href="staff.php">
                    <i class="fa-solid fa-chalkboard-user icon"></i>
                    <h1>Staff Page</h1>
                </a>
                <a href="logout.php">
                    <i class="fa-solid fa-right-from-bracket icon"></i>
                    <h1>Logout</h1>
                </a>
        </div> 
        <main class="main-content">
            <p>Welcome, use the form below to add a staff member.</p>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
            <form  method="POST">
                <div class="form-container">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="example@gmail.com" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="form-group">
                        <label for="religion">Religion</label>
                        <select name="religion" id="religion" required>
                            <option value="">Select religion Status</option>
                            <option value="Muslim">Muslim</option>
                            <option value="Christian">Christian</option>
                            <option value="Null">Null</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="position">Position</label>
                        <select name="position" id="position" required>
                            <option value="">Select position</option>
                            <option value="Lecturer I">Lecturer I</option>
                            <option value="Lecturer II">Lecturer II</option>
                            <option value="Senior Lecturer">Senior Lecturer</option>
                            <option value="Senior Lecturer II">Senior Lecturer II</option>
                            <option value="Senior Lecturer III">Senior Lecturer III</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date_of_joining">Date of Joining</label>
                        <input type="date" name="date_of_joining" id="date_of_joining" required>
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <input type="text" name="department" id="department" required>
                    </div>
                </div>
                <div class="btn-div">
                    <button class="btn" type="submit">Register Staff</button>
                </div>
            </form>
        </main>
        </div>
    </body>
</html>
