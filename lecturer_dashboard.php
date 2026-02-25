<?php
session_start();
include 'db.php';

// Check if lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 2) {
    header("Location: login.php");
    exit();
}

// Get Lecturer ID from session
$lecturer_id = $_SESSION['user_id'];

// Fetch assigned subject for the lecturer
$subject_query = "SELECT subjects.id, subjects.subject_name FROM subjects 
                  JOIN users ON subjects.id = users.subject_id 
                  WHERE users.id = ?";
$stmt = $conn->prepare($subject_query);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$subject_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lecturer Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #667eea, #764ba2);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .glass-card {
        width: 450px;
        padding: 40px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        color: white;
        text-align: center;
        animation: fadeIn 0.8s ease-in-out;
    }

    .glass-card h2 {
        font-weight: 600;
        margin-bottom: 30px;
        letter-spacing: 1px;
    }

    .form-select {
        border-radius: 10px;
        padding: 12px;
        font-size: 15px;
        border: none;
        outline: none;
        color: black; /* FIXED DROPDOWN TEXT COLOR */
    }

    .form-select option {
        color: black;
    }

    .btn-custom {
        margin-top: 20px;
        width: 100%;
        padding: 12px;
        border-radius: 30px;
        border: none;
        font-weight: 600;
        letter-spacing: 1px;
        transition: 0.3s ease;
        background: linear-gradient(45deg, #ff6a00, #ee0979);
        color: white;
    }

    .btn-custom:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }

    .btn-custom:disabled {
        background: #999;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .footer-text {
        margin-top: 25px;
        font-size: 13px;
        opacity: 0.8;
    }
</style>
</head>

<body>

<div class="glass-card">
    <h2>Lecturer Dashboard</h2>

    <!-- Subject Selection -->
    <select class="form-select" id="subject">
        <option value="">Select Subject</option>
        <?php while ($subject = $subject_result->fetch_assoc()) { ?>
            <option value="<?= $subject['id'] ?>">
                <?= $subject['subject_name'] ?>
            </option>
        <?php } ?>
    </select>

    <button id="manageQuestions" class="btn-custom" disabled>
        Manage Questions
    </button>

    <div class="footer-text">
        Select your assigned subject to continue
    </div>
</div>

<script>
$(document).ready(function(){
    $("#subject").change(function(){
        $("#manageQuestions").prop("disabled", !$(this).val());
    });

    $("#manageQuestions").click(function(){
        let subject_id = $("#subject").val();
        if(subject_id) {
            window.location.href = "add_questions.php?subject_id=" + subject_id;
        }
    });
});
</script>

</body>
</html>