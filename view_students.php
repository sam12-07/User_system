<?php
session_start();
include 'db.php'; // Ensure database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check user role
$user_id = $_SESSION['user_id'];
$query = "SELECT role_id, department_id FROM users WHERE id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    die("<h3 style='color: red; text-align: center;'>❌ Database Error: " . $conn->error . "</h3>");
}

// Allow access to HODs (3), Principals (1), and Lecturers (2)
$allowed_roles = [1, 2, 3];

if (!$user || !in_array($user['role_id'], $allowed_roles)) {
    die("<h2 style='color: red; text-align: center;'>❌ Access Denied. Only Principals, Lecturers, and HODs can access this page.</h2>");
}

// Get subject ID from URL
if (!isset($_GET['subject_id']) || empty($_GET['subject_id'])) {
    die("<h3 style='color: red; text-align: center;'>❌ Invalid Subject Selection.</h3>");
}

$subject_id = intval($_GET['subject_id']);

// Get subject name
$subject_query = "SELECT subject_name FROM subjects WHERE id = ?";
if ($stmt = $conn->prepare($subject_query)) {
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();
    $stmt->close();
} else {
    die("<h3 style='color: red; text-align: center;'>❌ Database Error: " . $conn->error . "</h3>");
}

if (!$subject) {
    die("<h3 style='color: red; text-align: center;'>❌ Subject Not Found.</h3>");
}

$subject_name = $subject['subject_name'];

// Fetch student results with total marks
$results_query = "
    SELECT u.first_name, u.last_name, r.score_percentage, r.total_marks, r.created_at 
    FROM results r
    JOIN users u ON r.student_id = u.id
    WHERE r.student_id IN (
        SELECT DISTINCT student_id 
        FROM student_answers sa 
        JOIN questions q ON sa.question_id = q.id 
        WHERE q.subject_id = ?
    )
";

$students = [];

if ($stmt = $conn->prepare($results_query)) {
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
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
    <title>Student Results</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: url('banner.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 1100px;
            color: white;
        }

        h2 {
            font-weight: 600;
            margin-bottom: 20px;
        }

        .table {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .table thead {
            background: rgba(0, 0, 0, 0.6);
        }

        .table tbody tr {
            transition: 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.01);
        }

        .btn-modern {
            background: linear-gradient(45deg, #4e73df, #1cc88a);
            border: none;
            border-radius: 30px;
            padding: 8px 20px;
            color: white;
            font-weight: 500;
            transition: 0.3s ease;
        }

        .btn-modern:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .search-box {
            border-radius: 30px;
            padding: 8px 15px;
            border: none;
            outline: none;
            width: 250px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="glass-card">

    <h2 class="text-center">
        Results for Subject: <strong><?= htmlspecialchars($subject_name) ?></strong>
    </h2>

    <div class="top-bar">
        <input type="text" id="searchInput" class="search-box" placeholder="🔍 Search Student...">
        <div>
            <button class="btn-modern me-2" onclick="exportTable()">⬇ Export CSV</button>
            <a href="logout.php" class="btn btn-danger rounded-pill">Logout</a>
        </div>
    </div>

    <?php if (empty($students)): ?>
        <p class="text-center text-warning">No results available for this subject.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table id="resultsTable" class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Score %</th>
                        <th>Total Marks</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['first_name']) ?></td>
                            <td><?= htmlspecialchars($student['last_name']) ?></td>
                            <td><?= round($student['score_percentage'], 2) ?>%</td>
                            <td><?= htmlspecialchars($student['total_marks']) ?></td>
                            <td><?= htmlspecialchars($student['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<!-- JavaScript -->
<script>
    // 🔍 Live Search Filter
    document.getElementById("searchInput").addEventListener("keyup", function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#resultsTable tbody tr");

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });

    // 📥 Export Table to CSV
    function exportTable() {
        let table = document.getElementById("resultsTable");
        let rows = table.querySelectorAll("tr");
        let csv = [];

        rows.forEach(row => {
            let cols = row.querySelectorAll("td, th");
            let rowData = [];
            cols.forEach(col => rowData.push(col.innerText));
            csv.push(rowData.join(","));
        });

        let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
        let downloadLink = document.createElement("a");
        downloadLink.download = "student_results.csv";
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.click();
    }
</script>

</body>
</html>