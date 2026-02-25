<?php
include 'db.php';
session_start();

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing
$role = $_POST['role'];
$year = $_POST['year'];
$department_id = isset($_POST['department']) ? $_POST['department'] : null;
$phone = isset($_POST['phone']) ? $_POST['phone'] : null;
$subject = isset($_POST['subject']) ? $_POST['subject'] : null;

// Get role ID from database
$roleQuery = $conn->prepare("SELECT id FROM roles WHERE role_name = ?");
$roleQuery->bind_param("s", $role);
$roleQuery->execute();
$roleResult = $roleQuery->get_result();
if ($roleRow = $roleResult->fetch_assoc()) {
    $role_id = $roleRow['id'];
} else {
    die("Error: Invalid role");
}

// Insert user into database
$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role_id, department_id, year, phone, subject) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssiiisi", $first_name, $last_name, $email, $password, $role_id, $department_id, $year, $phone, $subject);

if ($stmt->execute()) {
    echo "Registration successful!";
    header("Location: login.php");
} else {
    die("Error: " . $stmt->error);
}
?>
