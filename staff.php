<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit;
}

require 'config.php';

// Handle document upload
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_SESSION['staff_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $current_position = $_POST['current_position'];
    $next_position = $_POST['next_position'];
    $date_of_joining = $_POST['date_of_joining'];
    $employment_document = $_FILES['employment_document'];
    $promotion_document = $_FILES['promotion_document'];

    if (!empty($name) && !empty($email) && !empty($department) && !empty($current_position) && !empty($next_position) && !empty($date_of_joining) && !empty($employment_document['name']) && !empty($promotion_document['name'])) {
        $target_dir = "uploads/";

        // Check if the directory exists, if not, create it
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $employment_target_file = $target_dir . basename($employment_document["name"]);
        $promotion_target_file = $target_dir . basename($promotion_document["name"]);
        $uploadOk = 1;
        $employmentFileType = strtolower(pathinfo($employment_target_file, PATHINFO_EXTENSION));
        $promotionFileType = strtolower(pathinfo($promotion_target_file, PATHINFO_EXTENSION));

        // Function to check and upload a document
        function uploadDocument($document, $target_file, $fileType, &$error) {
            // Check if file is an actual document
            $check = filesize($document["tmp_name"]);
            if ($check === false) {
                $error = "File is not a document.";
                return false;
            }

            // Check file size
            if ($document["size"] > 5000000) {
                $error = "Sorry, your file is too large.";
                return false;
            }

            // Allow certain file formats
            if ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
                $error = "Sorry, only PDF, DOC & DOCX files are allowed.";
                return false;
            }

            // Try to move the uploaded file
            if (move_uploaded_file($document["tmp_name"], $target_file)) {
                return true;
            } else {
                $error = "Sorry, there was an error uploading your file.";
                return false;
            }
        }

        $employment_upload_success = uploadDocument($employment_document, $employment_target_file, $employmentFileType, $error);
        $promotion_upload_success = uploadDocument($promotion_document, $promotion_target_file, $promotionFileType, $error);

        if ($employment_upload_success && $promotion_upload_success) {
            $stmt = $pdo->prepare("INSERT INTO request (staff_id, name, email, department, current_position, next_position, date_of_joining, employment_document_path, promotion_document_path, status) VALUES (:staff_id, :name, :email, :department, :current_position, :next_position, :date_of_joining, :employment_document_path, :promotion_document_path, 'pending')");
            $stmt->execute([
                'staff_id' => $staff_id,
                'name' => $name,
                'email' => $email,
                'department' => $department,
                'current_position' => $current_position,
                'next_position' => $next_position,
                'date_of_joining' => $date_of_joining,
                'employment_document_path' => $employment_target_file,
                'promotion_document_path' => $promotion_target_file
            ]);
            $success = "The files have been uploaded.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Staff Dashboard</h2>
            <span></span>
            <a href="staff.php" class="active">
                <i class="fa-solid fa-chalkboard-user icon"></i>
                <h1>Staff Page</h1>
            </a>
            <a href="index.html">
                <i class="fa-solid fa-house icon"></i>
                <h1>Home</h1>
            </a>
            <a href="logout.php">
                <i class="fa-solid fa-right-from-bracket icon"></i>
                <h1>Logout</h1>
            </a>
        </div> 
        <div class="main-content">
            <h1>Upload Document for Promotion</h1>
            <?php
            if (isset($error)) {
                echo "<p style='color:red;'>$error</p>";
            }
            if (isset($success)) {
                echo "<p style='color:green;'>$success</p>";
            }
            ?>
            <form method="post" enctype="multipart/form-data">
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
                        <label for="department">Department</label>
                        <input type="text" name="department" id="department" required>
                    </div>
                    <div class="form-group">
                        <label for="current_position">Current Position</label>
                        <select name="current_position" id="current_position" required>
                            <option value="">Select position</option>
                            <option value="Lecturer I">Lecturer I</option>
                            <option value="Lecturer II">Lecturer II</option>
                            <option value="Senior Lecturer">Senior Lecturer</option>
                            <option value="Senior Lecturer II">Senior Lecturer II</option>
                            <option value="Senior Lecturer III">Senior Lecturer III</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="next_position">Next Position</label>
                        <select name="next_position" id="next_position" required>
                            <option value="">Select position</option>
                            <option value="Lecturer I">Lecturer I</option>
                            <option value="Lecturer II">Lecturer II</option>
                            <option value="Senior Lecturer">Senior Lecturer</option>
                            <option value="Senior Lecturer II">Senior Lecturer II</option>
                            <option value="Senior Lecturer III">Senior Lecturer III</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date_of_joining">Last Promotion Date</label>
                        <input type="date" name="date_of_joining" id="date_of_joining" required>
                    </div>
                    <div class="form-group">
                        <label for="employment_document">Upload Employment Document:</label>
                        <input type="file" id="employment_document" name="employment_document" required>
                    </div>
                    <div class="form-group">
                        <label for="promotion_document">Upload Last Promotion Document:</label>
                        <input type="file" id="promotion_document" name="promotion_document" required>
                    </div>
                </div>
                <div class="btn-div">
                    <button class="btn" type="submit">Upload</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
