<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

$department_query = "SELECT department_id FROM users WHERE id = ?";
$stmt = $conn->prepare($department_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$department_id = $student['department_id'];

$year_query = "SELECT DISTINCT year FROM subjects WHERE department_id = ? ORDER BY year";
$stmt = $conn->prepare($year_query);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$year_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
body {
    background: linear-gradient(135deg, #141e30, #243b55);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', sans-serif;
}

/* Glass Card */
.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    padding: 40px;
    width: 100%;
    max-width: 600px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    color: white;
    animation: fadeIn 0.8s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px);}
    to { opacity: 1; transform: translateY(0);}
}

.glass-card h2 {
    text-align: center;
    margin-bottom: 30px;
    font-weight: 600;
}

/* Dropdown Styling */
.form-control {
    background: rgba(255,255,255,0.9) !important;
    border-radius: 12px;
    border: none;
    padding: 10px 15px;
    font-weight: 500;
    color: #000 !important;
}

.form-control:focus {
    box-shadow: 0 0 10px rgba(0,123,255,0.6);
}

select option {
    color: #000 !important;
    background: #fff !important;
}

/* Button */
.btn-start {
    background: linear-gradient(45deg, #00c6ff, #0072ff);
    border: none;
    border-radius: 30px;
    padding: 10px 25px;
    font-weight: 600;
    transition: 0.3s;
}

.btn-start:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,114,255,0.5);
}

.btn-start:disabled {
    background: #aaa;
    cursor: not-allowed;
}

/* Small info text */
.info-text {
    text-align: center;
    font-size: 14px;
    margin-top: 15px;
    opacity: 0.8;
}
</style>
</head>

<body>

<div class="glass-card">
    <h2>🎓 Student Dashboard</h2>

    <!-- Year Selection -->
    <div class="mb-3">
        <label class="mb-2">Select Year</label>
        <select class="form-control" id="year">
            <option value="">Select Year</option>
            <?php while ($year = $year_result->fetch_assoc()) { ?>
                <option value="<?= $year['year'] ?>">Year <?= $year['year'] ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Subject Selection -->
    <div class="mb-3">
        <label class="mb-2">Select Subject</label>
        <select class="form-control" id="subject" disabled>
            <option value="">Select Subject</option>
        </select>
    </div>

    <!-- Start Exam Button -->
    <div class="text-center mt-4">
        <button id="startExam" class="btn btn-start" disabled>Start Exam</button>
    </div>

    <div class="info-text">
        Select year and subject to begin your exam.
    </div>
</div>

<script>
$(document).ready(function(){

    $("#year").change(function(){
        let year = $(this).val();
        if(year) {
            $.ajax({
                url: "fetch_subjects.php",
                type: "POST",
                data: { year: year },
                success: function(response) {
                    $("#subject").html(response).prop("disabled", false);
                    $("#startExam").prop("disabled", true);
                }
            });
        } else {
            $("#subject").html('<option value="">Select Subject</option>').prop("disabled", true);
            $("#startExam").prop("disabled", true);
        }
    });

    $("#subject").change(function(){
        $("#startExam").prop("disabled", !$(this).val());
    });

    $("#startExam").click(function(){
        let subject_id = $("#subject").val();
        if(subject_id) {
            window.location.href = "start_exam.php?subject_id=" + subject_id;
        }
    });
});
</script>

</body>
</html>