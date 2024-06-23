<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

require 'config.php'; // Assuming this file contains your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['request_id'])) {
    $requestId = $_POST['request_id'];
    $action = $_POST['action'];

    // Update the status based on the action
    switch ($action) {
        case 'approve':
            $status = 'approved'; // Move the $status assignment here

            // Fetch the details of the approved promotion
            $stmt = $pdo->prepare("SELECT * FROM request WHERE id = :id");
            $stmt->execute(['id' => $requestId]);
            $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($promotion) {
                $stmt = $pdo->prepare("INSERT INTO promotions (staff_id, promotion_date, status) VALUES (:staff_id, :promotion_date, :status)");
                $result = $stmt->execute([
                    'staff_id' => $promotion['staff_id'],
                    'promotion_date' => date('Y-m-d'),
                    'status' => $status
                ]);

                if ($result) {
                    $stmt = $pdo->prepare("DELETE FROM request WHERE id = :id");
                    $deleted = $stmt->execute(['id' => $requestId]);

                    if ($deleted) {
                        $success = "Promotion approved and transferred successfully!";
                    } else {
                        $error = "Error deleting promotion from request table.";
                    }
                } else {
                    $error = "Error inserting promotion into promotions table.";
                }
            } else {
                $error = "Error: Promotion not found or not approved.";
            }
            break; 
        case 'reject':
            $status = 'rejected';

            // Update the status in the 'request' table
            $stmt = $pdo->prepare("UPDATE request SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $status, 'id' => $requestId]);

            if ($stmt->rowCount() > 0) {
                $success = "Promotion rejected successfully!";
            } else {
                $error = "Error rejecting promotion.";
            }
            break;
        case 'pending':
        default:
            $status = 'pending';

            // Update the status in the 'request' table
            $stmt = $pdo->prepare("UPDATE request SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $status, 'id' => $requestId]);

            if ($stmt->rowCount() > 0) {
                $success = "Promotion status updated successfully!";
            } else {
                $error = "Error updating promotion status.";
            }
            break;
    }
}

// Fetch all promotion requests from the database
$promotionsQuery = $pdo->query("SELECT * FROM request");
$promotionsList = $promotionsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promote Staff</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="css/admin.css" rel="stylesheet">
    <style>
        .approved {
            color: green;
        }

        .rejected {
            color: red;
        }

        .pending {
            color: orange;
        }
    </style>
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
                <a href="admin_promote_staff.php" class="active">
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
                <h3>Promotions List</h3>
                <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
                <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
                <table>
                    <thead>
                    <tr>
                        <th>Staff ID</th>
                        <th>Current Position</th>
                        <th>Next Position</th>
                        <th>Date of Joining</th>
                        <th>Employment Document</th>
                        <th>Promotion Document</th>
                        <th>Promotion Status</th>
                        <th>Promotion Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($promotionsList as $promotion): ?>
                        <tr>
                            <td><?php echo $promotion['staff_id']; ?></td>
                            <td><?php echo $promotion['current_position']; ?></td>
                            <td><?php echo $promotion['next_position']; ?></td>
                            <td><?php echo $promotion['date_of_joining']; ?></td>
                            <td><?php echo $promotion['employment_document_path']; ?></td>
                            <td><?php echo $promotion['promotion_document_path']; ?></td>
                            <td>
                                <?php
                                $status = $promotion['status'];
                                $statusClass = '';

                                switch ($status) {
                                    case 'approved':
                                        $statusClass = 'approved';
                                        break;
                                    case 'rejected':
                                        $statusClass = 'rejected';
                                        break;
                                    case 'pending':
                                    default:
                                        $statusClass = 'pending';
                                        break;
                                }
                                ?>
                                <span class="<?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="request_id" value="<?php echo $promotion['id']; ?>">
                                    <select name="action" onchange="this.form.submit()">
                                        <option value="">Select</option>
                                        <option value="approve">Approve</option>
                                        <option value="reject">Reject</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </body>
</html>
