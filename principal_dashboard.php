<?php
session_start();
include("db.php"); // Ensure database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access Denied. Please <a href='login.php'>login</a> again.");
}

$user_name = $_SESSION['user_name'] ?? "User"; // Display user's name

// Fetch departments
$departments = [];
$result = $conn->query("SELECT id, department_name FROM departments");
while ($row = $result->fetch_assoc()) {
    $departments[$row['id']] = $row['department_name'];
}

// Fetch subjects grouped by department and year
$subjects_by_department_year = [];
$result = $conn->query("SELECT id, subject_name, department_id, year FROM subjects");
while ($row = $result->fetch_assoc()) {
    $subjects_by_department_year[$row['department_id']][$row['year']][] = [
        'id' => $row['id'],
        'name' => $row['subject_name']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('banner.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            text-align: center;
        }
        .container {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.2);
            max-width: 600px;
            margin: auto;
            margin-top: 50px;
        }
        select, button {
            padding: 10px;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
    <script>
        function updateSubjects() {
            var department_id = document.getElementById("department").value;
            var year = document.getElementById("year").value;
            var subjectDropdown = document.getElementById("subject");

            subjectDropdown.innerHTML = "<option value=''>-- Select Subject --</option>";

            var subjectsByDeptYear = <?php echo json_encode($subjects_by_department_year); ?>;

            if (subjectsByDeptYear[department_id] && subjectsByDeptYear[department_id][year]) {
                subjectsByDeptYear[department_id][year].forEach(function(subject) {
                    var option = document.createElement("option");
                    option.value = subject.id;
                    option.textContent = subject.name;
                    subjectDropdown.appendChild(option);
                });
            }
        }

        function viewResults() {
            var subjectId = document.getElementById("subject").value;
            if (subjectId) {
                window.location.href = 'view_students.php?subject_id=' + subjectId;
            } else {
                alert('Please select a subject.');
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($user_name); ?></h2>

    <!-- Filter Form -->
    <form class="filter-form">
        <label for="department">Select Department:</label>
        <select name="department" id="department" onchange="updateSubjects()">
            <option value="">-- Select Department --</option>
            <?php foreach ($departments as $id => $name): ?>
                <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="year">Select Year:</label>
        <select name="year" id="year" onchange="updateSubjects()">
            <option value="1">1st Year</option>
            <option value="2">2nd Year</option>
            <option value="3">3rd Year</option>
        </select>

        <label for="subject">Select Subject:</label>
        <select name="subject" id="subject">
            <option value="">-- Select Subject --</option>
        </select>

        <button type="button" onclick="viewResults()">View Student Results</button>
    </form>
</div>

</body>
</html>
