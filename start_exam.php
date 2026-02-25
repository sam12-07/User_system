<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT users.first_name, users.last_name, departments.department_name, users.year
    FROM users 
    JOIN departments ON users.department_id = departments.id
    WHERE users.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Error: User data not found!");
}

if (!isset($_GET['subject_id']) || !is_numeric($_GET['subject_id'])) {
    die("Error: Invalid subject ID!");
}

$subject_id = (int) $_GET['subject_id'];

$query = "SELECT id, question_text, option_a, option_b, option_c, option_d, marks FROM questions WHERE subject_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$total_questions = count($questions);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Start Exam</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #141e30, #243b55);
    font-family: 'Segoe UI', sans-serif;
    color: white;
}

/* Glass containers */
.glass-box {
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(15px);
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
}

/* Timer */
#timer {
    font-size: 18px;
    padding: 10px 20px;
    border-radius: 25px;
    background: linear-gradient(45deg,#ff416c,#ff4b2b);
}

/* Question Nav */
.question-number {
    padding: 8px 12px;
    margin: 4px;
    border-radius: 8px;
    cursor: pointer;
    display: inline-block;
    transition: 0.3s;
    font-weight: 600;
}

.unattempted { background: white; color: black; }
.attempted { background: #28a745; color: white; }
.tagged { background: #ffc107 !important; color: black !important; }

.question-number:hover {
    transform: scale(1.1);
}

/* Buttons */
.btn-modern {
    border-radius: 25px;
    padding: 8px 20px;
    font-weight: 600;
}

.option-card {
    background: rgba(255,255,255,0.1);
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 8px;
    transition: 0.3s;
}

.option-card:hover {
    background: rgba(255,255,255,0.2);
}
</style>
</head>

<body>
<div class="container mt-4">

    <div class="glass-box d-flex justify-content-between align-items-center">
        <div>
            <h5>👤 <?= $user['first_name']." ".$user['last_name'] ?></h5>
            <p class="mb-0">📚 <?= $user['department_name'] ?> | 🎓 Year: <?= $user['year'] ?></p>
        </div>
        <div id="timer">Time Left: 15:00</div>
    </div>

    <!-- Status -->
    <div class="glass-box mt-3 d-flex justify-content-between text-center">
        <div id="attempted-count">0 Attempted</div>
        <div id="unattempted-count"><?= $total_questions ?> Unattempted</div>
        <div id="tagged-count">0 Tagged</div>
    </div>

    <!-- Question Navigation -->
    <div class="glass-box mt-3">
        <?php foreach ($questions as $index => $q) { ?>
            <div class="question-number unattempted" id="qnum<?= $index ?>" onclick="showQuestion(<?= $index ?>)">
                <?= $index+1 ?>
            </div>
        <?php } ?>
    </div>

    <!-- Question Box -->
    <div class="glass-box mt-3">
        <form id="examForm" action="process_exam.php" method="POST">
            <input type="hidden" name="subject_id" value="<?= $subject_id ?>">

            <?php foreach ($questions as $index => $question) { ?>
            <div class="question" id="question<?= $index ?>" style="display:none;">
                <h5><?= ($index+1).". ".$question['question_text'] ?></h5>
                <p><strong>Marks:</strong> <?= $question['marks'] ?></p>

                <input type="hidden" name="question_ids[]" value="<?= $question['id'] ?>">

                <?php foreach(["A","B","C","D"] as $opt){ ?>
                <div class="form-check option-card">
                    <input class="form-check-input answer-option"
                        type="radio"
                        name="answers[<?= $question['id'] ?>]"
                        value="<?= $opt ?>"
                        data-question="<?= $index ?>">
                    <label class="form-check-label">
                        <?= $question['option_'.strtolower($opt)] ?>
                    </label>
                </div>
                <?php } ?>

                <button type="button" class="btn btn-warning btn-modern"
                    onclick="toggleTag(<?= $index ?>)">Tag Question</button>
            </div>
            <?php } ?>

            <div class="d-flex justify-content-between mt-3">
                <button type="button" onclick="prevQuestion()" class="btn btn-secondary btn-modern">Previous</button>
                <button type="button" onclick="nextQuestion()" class="btn btn-primary btn-modern">Next</button>
                <button type="submit" class="btn btn-success btn-modern"
                    id="submitExam" style="display:none;" disabled>Submit Exam</button>
            </div>
        </form>
    </div>

</div>

<script>
let currentQuestion = 0;
let attemptedCount = 0, unattemptedCount = <?= $total_questions ?>, taggedCount = 0;

/* 🔥 TIME INCREASED TO 15 MINUTES */
let timerTime = 900; // 15 minutes
let timerInterval;

function updateCounters() {
    document.getElementById("attempted-count").textContent = attemptedCount + " Attempted";
    document.getElementById("unattempted-count").textContent = unattemptedCount + " Unattempted";
    document.getElementById("tagged-count").textContent = taggedCount + " Tagged";
}

function showQuestion(index) {
    document.querySelectorAll(".question").forEach(q => q.style.display="none");
    document.getElementById("question"+index).style.display="block";
    currentQuestion=index;

    if(currentQuestion === <?= $total_questions ?>-1)
        document.getElementById("submitExam").style.display="inline-block";
    else
        document.getElementById("submitExam").style.display="none";
}

document.querySelectorAll(".answer-option").forEach(option=>{
    option.addEventListener("change",function(){
        let index=this.dataset.question;
        let qnum=document.getElementById("qnum"+index);
        if(qnum.classList.contains("unattempted")){
            qnum.classList.remove("unattempted");
            qnum.classList.add("attempted");
            attemptedCount++; unattemptedCount--;
        }
        updateCounters();
        if(attemptedCount===<?= $total_questions ?>)
            document.getElementById("submitExam").disabled=false;
    });
});

function toggleTag(index){
    let qnum=document.getElementById("qnum"+index);
    qnum.classList.toggle("tagged");
    taggedCount += qnum.classList.contains("tagged")?1:-1;
    updateCounters();
}

function startTimer(){
    timerInterval=setInterval(function(){
        if(timerTime<=0){
            clearInterval(timerInterval);
            document.getElementById("examForm").submit();
        } else {
            timerTime--;
            let minutes=Math.floor(timerTime/60);
            let seconds=timerTime%60;
            document.getElementById("timer").textContent=
                "Time Left: "+minutes+":"+(seconds<10?"0"+seconds:seconds);
        }
    },1000);
}

function nextQuestion(){ if(currentQuestion<<?= $total_questions ?>-1) showQuestion(currentQuestion+1); }
function prevQuestion(){ if(currentQuestion>0) showQuestion(currentQuestion-1); }

showQuestion(0);
startTimer();
</script>

</body>
</html>