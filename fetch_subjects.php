<?php
include 'db.php';
session_start();

// Check if student is logged in
if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized access!");
}

$student_id = $_SESSION['user_id'];

// Fetch student's department
$department_query = "SELECT department_id FROM users WHERE id = ?";
$stmt = $conn->prepare($department_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$department_id = $student['department_id'];

if (isset($_POST['year'])) {
    $year = $_POST['year'];

    // Fetch subjects based on department and selected year
    $subject_query = "SELECT id, subject_name FROM subjects WHERE department_id = ? AND year = ?";
    $stmt = $conn->prepare($subject_query);
    $stmt->bind_param("ii", $department_id, $year);
    $stmt->execute();
    $subject_result = $stmt->get_result();

    echo '<option value="">Select Subject</option>';
    while ($subject = $subject_result->fetch_assoc()) {
        echo '<option value="'.$subject['id'].'">'.$subject['subject_name'].'</option>';
    }
}
?>
