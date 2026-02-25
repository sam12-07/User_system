<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("❌ Error: User not logged in.");
}

$lecturer_id = $_SESSION['user_id'];

$lecturerQuery = "SELECT s.id AS subject_id, s.subject_name, d.department_name 
                  FROM users u
                  JOIN subjects s ON CAST(u.subject AS UNSIGNED) = s.id 
                  JOIN departments d ON s.department_id = d.id
                  WHERE u.id = ?";

$stmt = $conn->prepare($lecturerQuery);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
$lecturerInfo = $result->fetch_assoc();

if (!$lecturerInfo) {
    die("❌ Error: No subject assigned.");
}

$department_name = $lecturerInfo['department_name'];

$subjectQuery = "SELECT id, subject_name FROM subjects WHERE department_id = 
                 (SELECT department_id FROM subjects WHERE id = ?)"; 

$stmt = $conn->prepare($subjectQuery);
$stmt->bind_param("i", $lecturerInfo['subject_id']);
$stmt->execute();
$result = $stmt->get_result();
$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

$selected_subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : ($subjects[0]['id'] ?? null);

$countQuery = "SELECT COUNT(*) as total FROM questions WHERE subject_id = ?";
$stmt = $conn->prepare($countQuery);
$stmt->bind_param("i", $selected_subject_id);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->fetch_assoc();
$savedQuestions = $count['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Questions</title>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    background: 
        linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
        url('banner.jpg') no-repeat center center/cover;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Glass Card */
.glass-card {
    width: 650px;
    padding: 45px;
    border-radius: 25px;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 1px solid rgba(255,255,255,0.25);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    color: white;
    animation: fadeIn 0.8s ease-in-out;
}

/* Headings */
.glass-card h1 {
    text-align: center;
    margin-bottom: 8px;
    font-size: 28px;
    font-weight: 600;
}

.glass-card h2 {
    text-align: center;
    font-size: 15px;
    margin-bottom: 25px;
    opacity: 0.85;
}

/* Progress Badge */
.progress-box {
    text-align: center;
    margin-bottom: 25px;
    font-weight: 500;
    background: rgba(255,255,255,0.15);
    padding: 8px 18px;
    border-radius: 20px;
    display: inline-block;
}

/* Inputs */
label {
    font-size: 13px;
    font-weight: 500;
}

input, select {
    width: 100%;
    padding: 12px;
    margin-top: 6px;
    margin-bottom: 15px;
    border-radius: 12px;
    border: none;
    outline: none;
    font-size: 14px;
    background: rgba(255,255,255,0.95);
    color: black;
    transition: 0.3s ease;
}

input:focus, select:focus {
    box-shadow: 0 0 0 3px rgba(255,255,255,0.4);
}

/* Main Button */
.btn-custom {
    width: 100%;
    padding: 13px;
    border-radius: 30px;
    border: none;
    font-weight: 600;
    letter-spacing: 1px;
    background: linear-gradient(45deg, #ff6a00, #ee0979);
    color: white;
    cursor: pointer;
    transition: 0.3s ease;
}

.btn-custom:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.4);
}

/* Bottom Buttons */
.button-group {
    display: flex;
    justify-content: space-between;
    margin-top: 25px;
}

.small-btn {
    width: 48%;
    padding: 11px;
    border-radius: 30px;
    text-align: center;
    text-decoration: none;
    color: white;
    font-weight: 500;
    transition: 0.3s ease;
}

.blue-btn {
    background: rgba(52,152,219,0.9);
}

.purple-btn {
    background: rgba(155,89,182,0.9);
}

.small-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.4);
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(25px); }
    to { opacity: 1; transform: translateY(0); }
}

</style>
</head>

<body>

<div class="glass-card">

    <h1>Add Questions</h1>
    <h2>Department: <?= htmlspecialchars($department_name) ?></h2>

    <div style="text-align:center;">
        <div class="progress-box">
            Total Questions Added: <span id="progress-text"><?= $savedQuestions ?></span>
        </div>
    </div>

    <form id="question-form">

        <label>Subject</label>
        <select name="subject_id" id="subject-select" required>
            <?php foreach ($subjects as $subject) : ?>
                <option value="<?= $subject['id'] ?>" <?= $selected_subject_id == $subject['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($subject['subject_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Question</label>
        <input type="text" name="question_text" required>

        <label>Option A</label>
        <input type="text" name="option_a" required>

        <label>Option B</label>
        <input type="text" name="option_b" required>

        <label>Option C</label>
        <input type="text" name="option_c" required>

        <label>Option D</label>
        <input type="text" name="option_d" required>

        <label>Correct Answer</label>
        <select name="correct_option" required>
            <option value="1">Option A</option>
            <option value="2">Option B</option>
            <option value="3">Option C</option>
            <option value="4">Option D</option>
        </select>

        <label>Marks</label>
        <select name="marks" required>
            <option value="2">2 Marks</option>
            <option value="4">4 Marks</option>
            <option value="6">6 Marks</option>
            <option value="8">8 Marks</option>
        </select>

        <button type="submit" class="btn-custom">Save Question</button>

    </form>

    <div class="button-group">
        <a href="lec_dashboard.php" class="small-btn blue-btn">View Results</a>
        <a href="view_question.php" class="small-btn purple-btn">View Questions</a>
    </div>

</div>

</body>
</html>