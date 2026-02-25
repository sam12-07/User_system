<?php
session_start();
include 'db.php';

// Check if lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

// Get lecturer ID from session
$lecturer_id = $_SESSION['user_id'];

// Fetch lecturer's department
$query = "SELECT d.department_name 
          FROM users u 
          JOIN departments d ON u.department_id = d.id 
          WHERE u.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
$lecturer = $result->fetch_assoc();

// Check if department exists
$department_name = $lecturer ? $lecturer['department_name'] : "Unknown";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Lecturer Dashboard</h2>
        <p class="text-center"><strong>Department:</strong> <?= htmlspecialchars($department_name) ?></p>
        
        <div class="d-flex justify-content-center gap-3">
            <a href="add_question.php" class="btn btn-primary">Add Questions</a>
            <a href="view_results.php" class="btn btn-success">View Results</a>
        </div>
    </div>
</body>
</html>
