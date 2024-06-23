<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "staff_promotion";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all staff
$staffResult = $conn->query("SELECT * FROM staff");
$staffList = [];
if ($staffResult->num_rows > 0) {
    while ($row = $staffResult->fetch_assoc()) {
        $staffList[] = $row;
    }
}

// Delete staff
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $conn->query("DELETE FROM staff WHERE id = $deleteId");
    header("Location: admin_manage_staff.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manage Staff</title>
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
            <a href="admin_manage_staff.php" class="active">
                <i class="fa-solid fa-user-tie icon"></i>
                <h1>Manage Staff</h1>
            </a>
            <a href="admin_promote_staff.php">
                <i class="fa-solid fa-arrow-up icon"></i>
                <h1>Promote Staff</h1>
            </a>
            <a href="admin_add_staff.php">
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

        <div class="manage-staffs">
            <h3>Staff List</h3>
            <table>
                <thead>
                    <tr>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staffList as $staff): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($staff['id']); ?></td>
                            <td><?php echo htmlspecialchars($staff['name']); ?></td>
                            <td><?php echo htmlspecialchars($staff['email']); ?></td>
                            <td><?php echo htmlspecialchars($staff['department']); ?></td>
                            <td>
                                <a href="admin_manage_staff.php?delete_id=<?php echo $staff['id']; ?>" onclick="return confirm('Are you sure you want to delete this staff member?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
