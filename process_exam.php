<?php
session_start();
include("db.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Unknown';
$date_time = date("Y-m-d H:i:s");

$correct_answers = 0;
$total_questions = 0;
$total_marks = 0;
$obtained_marks = 0;
$score_percentage = 0;
$exam_completed = false;
$submitted_answers = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['answers']) || empty($_POST['answers'])) {
        die("No answers submitted.");
    }

    $answers = $_POST['answers'];
    $total_questions = count($answers);
    $total_marks = $total_questions;

    foreach ($answers as $question_id => $selected_option) {

        $stmt = $conn->prepare("SELECT question_text, option_a, option_b, option_c, option_d, correct_option FROM questions WHERE id = ?");
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $stmt->bind_result($question_text, $option_a, $option_b, $option_c, $option_d, $correct_option);
        $stmt->fetch();
        $stmt->close();

        $is_correct = ($selected_option == $correct_option) ? 1 : 0;
        if ($is_correct) {
            $correct_answers++;
            $obtained_marks++;
        }

        $stmt = $conn->prepare("INSERT INTO student_answers (student_id, question_id, selected_option, is_correct) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $user_id, $question_id, $selected_option, $is_correct);
        $stmt->execute();
        $stmt->close();

        $submitted_answers[] = [
            "question_text" => $question_text,
            "options" => ["A"=>$option_a,"B"=>$option_b,"C"=>$option_c,"D"=>$option_d],
            "correct_option" => $correct_option,
            "selected_option" => $selected_option,
            "is_correct" => $is_correct
        ];
    }

    if ($total_questions > 0) {
        $score_percentage = ($correct_answers / $total_questions) * 100;
    }

    $stmt = $conn->prepare("INSERT INTO results (student_id, total_questions, correct_answers, score_percentage, total_marks, obtained_marks, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiddds", $user_id, $total_questions, $correct_answers, $score_percentage, $total_marks, $obtained_marks, $date_time);
    $stmt->execute();
    $stmt->close();

    $exam_completed = true;
}

/* ✅ PASS / FAIL LOGIC (Only Display, No DB Change) */
$status = ($score_percentage >= 55) ? "PASS" : "FAIL";
$status_color = ($score_percentage >= 55) ? "#28a745" : "#dc3545";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Exam Results</title>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg,#141e30,#243b55);
    color: white;
    display: flex;
    justify-content: center;
    padding: 30px;
}

.result-card {
    width: 90%;
    max-width: 900px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
}

h2, h3 {
    text-align: center;
}

.score-box {
    text-align: center;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 20px;
    background: rgba(255,255,255,0.08);
}

.progress {
    height: 20px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    overflow: hidden;
    margin-top: 10px;
}

.progress-bar {
    height: 100%;
    background: <?= $status_color ?>;
    width: <?= round($score_percentage,2) ?>%;
    text-align: center;
    font-size: 12px;
    line-height: 20px;
    color: white;
}

.question-box {
    background: rgba(255,255,255,0.08);
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
}

.correct { color: #00ff99; font-weight: bold; }
.incorrect { color: #ff4d4d; font-weight: bold; }
.correct-answer { color: #00d4ff; font-weight: bold; }

.button {
    display: inline-block;
    padding: 10px 25px;
    margin: 15px 10px 0;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.dashboard {
    background: linear-gradient(45deg,#00c6ff,#0072ff);
    color: white;
}

.logout {
    background: linear-gradient(45deg,#ff416c,#ff4b2b);
    color: white;
}

.button:hover {
    transform: scale(1.05);
}
</style>
</head>

<body>

<div class="result-card">
    <h2>📊 Exam Results</h2>
    <p style="text-align:center;"><strong>Student:</strong> <?= htmlspecialchars($user_name); ?></p>

    <?php if ($exam_completed): ?>

    <div class="score-box">
        <h3>Total Score: <?= $obtained_marks ?> / <?= $total_marks ?></h3>
        <h3 style="color: <?= $status_color ?>;">Status: <?= $status ?></h3>

        <div class="progress">
            <div class="progress-bar">
                <?= round($score_percentage,2) ?>%
            </div>
        </div>
    </div>

    <h3>Review Your Answers</h3>

    <?php foreach ($submitted_answers as $qa): ?>
        <div class="question-box">
            <p><strong>Question:</strong> <?= htmlspecialchars($qa['question_text']); ?></p>
            <ul>
                <?php foreach ($qa['options'] as $key => $option): ?>
                    <li 
                        <?php 
                        if ($key == $qa['selected_option']) {
                            echo $qa['is_correct'] ? 'class="correct"' : 'class="incorrect"';
                        }
                        ?>>
                        (<?= $key ?>) <?= htmlspecialchars($option); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="correct-answer">
                Correct Answer: (<?= $qa['correct_option']; ?>)
            </p>
        </div>
    <?php endforeach; ?>

    <?php else: ?>
        <p style="color:red; text-align:center;">No exam data available.</p>
    <?php endif; ?>

    <div style="text-align:center;">
        <a href="dashboard.php" class="button dashboard">Go to Dashboard</a>
        <a href="logout.php" class="button logout">Logout</a>
    </div>

</div>

</body>
</html>