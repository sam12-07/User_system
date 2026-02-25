<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(["status" => "error", "message" => "❌ Error: User not logged in."]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = intval($_POST['subject_id']);
    $question_text = trim($_POST['question_text']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_option = intval($_POST['correct_option']);
    $marks = intval($_POST['marks']);

    $insertQuery = "INSERT INTO questions (subject_id, question_text, option_a, option_b, option_c, option_d, correct_option, marks) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("isssssii", $subject_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $marks);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "✅ Question saved!", "new_count" => $stmt->affected_rows]);
}
?>
