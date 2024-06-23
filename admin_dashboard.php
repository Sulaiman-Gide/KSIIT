<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

require 'database.php';

try {
    // Create a new Database instance with the desired driver ('mysqli' or 'pdo')
    $db = new Database('pdo');
    $conn = $db->getConnection();

    // Fetch statistics related to staff promotions
    $totalStaffResult = $conn->query("SELECT COUNT(*) AS total FROM staff");
    $totalStaff = $totalStaffResult->fetch(PDO::FETCH_ASSOC)['total'];

    $totalPromotionsResult = $conn->query("SELECT COUNT(*) AS total FROM promotions");
    $totalPromotions = $totalPromotionsResult->fetch(PDO::FETCH_ASSOC)['total'];

    $rejectedPromotionsResult = $conn->query("SELECT COUNT(*) AS total FROM request WHERE status = 'rejected'");
    $rejectedPromotions = $rejectedPromotionsResult->fetch(PDO::FETCH_ASSOC)['total'];

    $pendingPromotionsResult = $conn->query("SELECT COUNT(*) AS total FROM request WHERE status = 'pending'");
    $pendingPromotions = $pendingPromotionsResult->fetch(PDO::FETCH_ASSOC)['total'];

    $acceptedPromotionsResult = $conn->query("SELECT COUNT(*) AS total FROM promotions WHERE status = 'accepted'");
    $acceptedPromotions = $acceptedPromotionsResult->fetch(PDO::FETCH_ASSOC)['total'];

    $newRequestsResult = $conn->query("SELECT COUNT(*) AS total FROM request");
    $newRequests = $newRequestsResult->fetch(PDO::FETCH_ASSOC)['total'];

    // Fetch recent promotions
    $recentPromotionsResult = $conn->query("SELECT 
        promotions.staff_id, 
        staff.name, 
        staff.email, 
        staff.department, 
        promotions.promotion_date FROM promotions INNER JOIN staff ON 
        promotions.staff_id = staff.id ORDER BY 
        promotions.promotion_date DESC");  
    $recentPromotions = [];

    if ($recentPromotionsResult->rowCount() > 0) {
        while ($row = $recentPromotionsResult->fetch(PDO::FETCH_ASSOC)) {
            $recentPromotions[] = $row;
        }
    }

    // Close the database connection
    $db->closeConnection();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Staff Promotion Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="css/admin_dashboard.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <a href="admin_dashboard.php" class="active">
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
        <main class="main-content">
            <canvas id="myChart" style="width:100%"></canvas>
            <script>
                // PHP variables passed to JavaScript
                const totalStaff = <?php echo json_encode($totalStaff); ?>;
                const totalPromotions = <?php echo json_encode($totalPromotions); ?>;
                const pendingPromotions = <?php echo json_encode($pendingPromotions); ?>;
                const rejectedPromotions = <?php echo json_encode($rejectedPromotions); ?>;
                const newRequests = <?php echo json_encode($newRequests); ?>;

                // Chart data
                const xValues = ["Total Staff", "Accepted Promotions", "Pending Promotions", "Rejected Promotions", "New Requests"];
                const yValues = [totalStaff, totalPromotions, pendingPromotions, rejectedPromotions, newRequests];
                const barColors = ["red", "green", "blue", "red", "orange"];

                new Chart("myChart", {
                type: "bar",
                data: {
                    labels: xValues,
                    datasets: [{
                    backgroundColor: barColors,
                    data: yValues
                    }]
                },
                options: {
                    legend: {display: false},
                    title: {
                        display: true,
                        text: "KSIIT Staff & Promotion Statistics"
                    }
                }
                });
            </script>
            <div class="recent-promotions">
                <h3>Recent Promotions</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Name</th>
                            <th>Staff Email</th>
                            <th>Department</th>
                            <th>Promotion Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPromotions as $promotion): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($promotion['staff_id']); ?></td>
                                <td><?php echo htmlspecialchars($promotion['name']); ?></td>
                                <td><?php echo htmlspecialchars($promotion['email']); ?></td>
                                <td><?php echo htmlspecialchars($promotion['department']); ?></td>
                                <td><?php echo date('F d, Y', strtotime($promotion['promotion_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
