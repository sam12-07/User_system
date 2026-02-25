<?php
session_start();
include 'db.php'; // Ensure database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT role_id, department_id, first_name, last_name FROM users WHERE id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    die("<h3 style='color: red; text-align: center;'>❌ Database Error: " . $conn->error . "</h3>");
}

// Ensure only Lecturers (role_id = 2) can access
if (!$user || $user['role_id'] != 2) {
    die("<h2 style='color: red; text-align: center;'>❌ Access Denied. Only Lecturers can access this page.</h2>");
}

$lecturer_name = $user['first_name'] . " " . $user['last_name'];
$department_id = $user['department_id'];

// Fetch department name
$dept_query = "SELECT department_name FROM departments WHERE id = ?";
if ($stmt = $conn->prepare($dept_query)) {
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $department = $result->fetch_assoc();
    $stmt->close();
} else {
    die("<h3 style='color: red; text-align: center;'>❌ Database Error: " . $conn->error . "</h3>");
}

$department_name = $department ? $department['department_name'] : "Unknown Department";

// Fetch distinct years from the subjects table
$years = [];
$year_query = "SELECT DISTINCT year FROM subjects WHERE department_id = ? ORDER BY year ASC";
if ($stmt = $conn->prepare($year_query)) {
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $years[] = $row['year'];
    }
    $stmt->close();
} else {
    die("<h3 style='color: red; text-align: center;'>❌ Database Error: " . $conn->error . "</h3>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lecturer Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

/* Glass Container */
.glass-card {
    width: 600px;
    padding: 45px;
    border-radius: 25px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 1px solid rgba(255,255,255,0.25);
    box-shadow: 0 20px 40px rgba(0,0,0,0.35);
    color: white;
    animation: fadeIn 0.8s ease-in-out;
}

/* Headings */
.glass-card h2 {
    text-align: center;
    font-weight: 600;
    margin-bottom: 10px;
}

.glass-card h3 {
    text-align: center;
    font-size: 16px;
    margin-bottom: 25px;
    opacity: 0.9;
}

/* Labels */
label {
    font-size: 14px;
    font-weight: 500;
}

/* Inputs */
select {
    width: 100%;
    padding: 12px;
    border-radius: 12px;
    border: none;
    outline: none;
    margin-top: 6px;
    margin-bottom: 20px;
    background: rgba(255,255,255,0.95);
    color: black;
    transition: 0.3s ease;
}

select:focus {
    box-shadow: 0 0 0 3px rgba(255,255,255,0.4);
}

/* Button */
.btn-modern {
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

.btn-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.4);
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

    <h2>Welcome, Lecturer <?= htmlspecialchars($lecturer_name) ?></h2>
    <h3>Department: <strong><?= htmlspecialchars($department_name) ?></strong></h3>

    <form>

        <label>Select Year</label>
        <select id="year" onchange="fetchSubjects()">
            <option value="">-- Choose Year --</option>
            <?php foreach ($years as $year): ?>
                <option value="<?= $year ?>">Year <?= $year ?></option>
            <?php endforeach; ?>
        </select>

        <label>Select Subject</label>
        <select id="subject">
            <option value="">-- Choose Subject --</option>
        </select>

        <button type="button" class="btn-modern" onclick="viewResults()">
            View Results
        </button>

    </form>

</div>

<script>
function fetchSubjects() {
    let year = document.getElementById('year').value;
    if (year) {
        $.ajax({
            url: 'fetch_subjects.php',
            type: 'POST',
            data: { year: year },
            success: function(response) {
                $('#subject').html(response);
            }
        });
    } else {
        $('#subject').html('<option value="">-- Choose Subject --</option>');
    }
}

function viewResults() {
    let subjectId = document.getElementById('subject').value;
    if (subjectId) {
        window.location.href = 'view_students.php?subject_id=' + subjectId;
    } else {
        alert('Please select a subject.');
    }
}
</script>

</body>
</html>