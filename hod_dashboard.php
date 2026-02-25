<?php
session_start();
include 'db.php'; // Ensure database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT role_id, department_id, first_name, last_name FROM users WHERE id = ?";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    die("<h3 class='text-danger text-center'>❌ Database Error: " . $conn->error . "</h3>");
}

// Check if user is an HOD (role_id = 3)
if (!$user || $user['role_id'] != 3) {
    die("<h2 class='text-danger text-center'>❌ Access Denied. Only HODs can access this page.</h2>");
}

// Get HOD details
$hod_name = $user['first_name'] . " " . $user['last_name'];
$department_id = $user['department_id'];

// Fetch department name
$dept_query = "SELECT department_name FROM departments WHERE id = ?";
if ($stmt = $conn->prepare($dept_query)) {
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dept = $result->fetch_assoc();
    $stmt->close();
} else {
    die("<h3 class='text-danger text-center'>❌ Database Error: " . $conn->error . "</h3>");
}
$department_name = $dept['department_name'];
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
            background: url('banner.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
        }
        h2, h3 {
            text-align: center;
        }
        .loading {
            display: none;
            text-align: center;
            font-size: 14px;
            color: blue;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, <strong><?= htmlspecialchars($hod_name) ?></strong></h2>
        <p class="text-center">Managing Department: <strong><?= htmlspecialchars($department_name) ?></strong></p>

        <h3>Select Year & Subject to View Student Results</h3>
        <form>
            <!-- Year Dropdown -->
            <div class="mb-3">
                <label for="year" class="form-label"><strong>Select Year:</strong></label>
                <select id="year" class="form-select">
                    <option value="">-- Select Year --</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                </select>
            </div>

            <!-- Subject Dropdown (Initially Empty) -->
            <div class="mb-3">
                <label for="subject" class="form-label"><strong>Select Subject:</strong></label>
                <select id="subject" class="form-select">
                    <option value="">-- Choose Year First --</option>
                </select>
            </div>

            <!-- Loading Indicator -->
            <div class="loading" id="loading">🔄 Loading subjects...</div>

            <!-- View Button -->
            <button type="button" class="btn btn-primary w-100" onclick="viewStudents()">View Student Details</button>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            // Function to load subjects based on selected year
            function loadSubjects(year) {
                let subjectSelect = $("#subject");
                subjectSelect.empty();
                subjectSelect.append('<option value="">-- Loading... --</option>');
                $("#loading").show();

                $.ajax({
                    type: "POST",
                    url: "fetch_hod.php",
                    data: { year: year, department_id: <?= $department_id ?> },
                    dataType: "json",
                    success: function (data) {
                        subjectSelect.empty();
                        subjectSelect.append('<option value="">-- Select Subject --</option>');
                        $("#loading").hide();

                        if (data.length > 0) {
                            data.forEach(function (subject) {
                                subjectSelect.append(`<option value="${subject.id}">${subject.subject_name}</option>`);
                            });
                        } else {
                            subjectSelect.append('<option value="">No subjects found for this year</option>');
                        }
                    },
                    error: function () {
                        subjectSelect.empty();
                        subjectSelect.append('<option value="">Error loading subjects</option>');
                        $("#loading").hide();
                        alert("❌ Error fetching subjects.");
                    }
                });
            }

            // Load subjects when year is changed
            $("#year").change(function () {
                let selectedYear = $(this).val();
                if (selectedYear) {
                    loadSubjects(selectedYear);
                } else {
                    $("#subject").empty();
                    $("#subject").append('<option value="">-- Choose Year First --</option>');
                }
            });
        });

        function viewStudents() {
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
