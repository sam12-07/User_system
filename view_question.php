<?php
session_start();
include 'db.php'; // Database connection file

// 1. Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("❌ Error: User not logged in.");
}

// 2. Grab the user’s role from session
$role = $_SESSION['role'] ?? null;

// 3. Determine which year to display (default = 1)
$year = isset($_GET['year']) ? (int)$_GET['year'] : 1;

// Prepare variables
$questions   = null;
$subjectName = null;

if ($role === 'principal') {
    /**
     * PRINCIPAL LOGIC:
     * Show all questions for the selected year (across all subjects).
     */
    $query = "
        SELECT q.*, s.subject_name
        FROM questions q
        JOIN subjects s ON q.subject_id = s.id
        WHERE q.year = ?
        ORDER BY q.id DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $questions = $stmt->get_result();

    // For heading label
    $subjectName = "All Subjects";

} else {
    /**
     * LECTURER LOGIC:
     * 1) Find the lecturer's assigned subject
     * 2) Show questions only for that subject & the selected year
     */
    $lecturer_id = $_SESSION['user_id'];

    // Fetch the lecturer's assigned subject
    $subjectQuery = "
        SELECT s.id, s.subject_name 
        FROM users u 
        JOIN subjects s ON u.subject = s.id 
        WHERE u.id = ?
    ";
    $stmt = $conn->prepare($subjectQuery);
    $stmt->bind_param("i", $lecturer_id);
    $stmt->execute();
    $result  = $stmt->get_result();
    $subject = $result->fetch_assoc();

    if (!$subject) {
        die("❌ Error: No subject assigned to this lecturer.");
    }

    // Fetch questions for this lecturer's subject & year
    $query = "SELECT * FROM questions WHERE subject_id = ? AND year = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $subject['id'], $year);
    $stmt->execute();
    $questions = $stmt->get_result();

    // For heading label
    $subjectName = $subject['subject_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Questions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            padding: 30px;
            background: url('banner.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }

        .glass-container {
            max-width: 1000px;
            margin: auto;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            color: white;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        select {
            padding: 8px 15px;
            border-radius: 25px;
            border: none;
            outline: none;
        }

        .search-box {
            padding: 8px 15px;
            border-radius: 25px;
            border: none;
            outline: none;
            width: 250px;
        }

        .question-card {
            background: rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            transition: 0.3s ease;
            cursor: pointer;
        }

        .question-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.25);
        }

        .options {
            margin-top: 15px;
            display: none;
        }

        .correct {
            color: #00ff99;
            font-weight: bold;
        }

        .btn-modern {
            background: linear-gradient(45deg, #4e73df, #1cc88a);
            border: none;
            border-radius: 30px;
            padding: 6px 15px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-modern:hover {
            transform: scale(1.05);
        }

        @media(max-width: 768px) {
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

<div class="glass-container">

    <h1>
        Questions for <?= htmlspecialchars($subjectName) ?> (Year <?= $year ?>)
    </h1>

    <div class="top-bar">
        <select id="year-select" onchange="changeYear()">
            <option value="1" <?= $year == 1 ? 'selected' : '' ?>>1st Year</option>
            <option value="2" <?= $year == 2 ? 'selected' : '' ?>>2nd Year</option>
            <option value="3" <?= $year == 3 ? 'selected' : '' ?>>3rd Year</option>
        </select>

        <input type="text" id="searchInput" class="search-box" placeholder="🔍 Search Questions...">
    </div>

    <?php if ($questions && $questions->num_rows > 0): ?>
        <?php while ($row = $questions->fetch_assoc()): ?>
            <div class="question-card">

                <?php if ($role === 'principal'): ?>
                    <p style="color:#80d0ff;font-size:14px;">
                        Subject: <?= htmlspecialchars($row['subject_name']) ?>
                    </p>
                <?php endif; ?>

                <div class="question-text">
                    <strong><?= htmlspecialchars($row['question_text']) ?></strong>
                </div>

                <div class="options">
                    <ul style="margin-top:10px;list-style:none;padding-left:0;">
                        <li class="<?= $row['correct_option'] === 'A' ? 'correct' : '' ?>">
                            A) <?= htmlspecialchars($row['option_a']) ?>
                        </li>
                        <li class="<?= $row['correct_option'] === 'B' ? 'correct' : '' ?>">
                            B) <?= htmlspecialchars($row['option_b']) ?>
                        </li>
                        <li class="<?= $row['correct_option'] === 'C' ? 'correct' : '' ?>">
                            C) <?= htmlspecialchars($row['option_c']) ?>
                        </li>
                        <li class="<?= $row['correct_option'] === 'D' ? 'correct' : '' ?>">
                            D) <?= htmlspecialchars($row['option_d']) ?>
                        </li>
                    </ul>

                    <button class="btn-modern" onclick="copyQuestion(event, this)">
                        📋 Copy Question
                    </button>
                </div>

            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No questions found for <?= htmlspecialchars($subjectName) ?> (Year <?= $year ?>).</p>
    <?php endif; ?>

</div>

<script>
    function changeYear() {
        const selectedYear = document.getElementById("year-select").value;
        window.location.href = "view_question.php?year=" + selectedYear;
    }

    // Expand / Collapse Questions
    document.querySelectorAll(".question-card").forEach(card => {
        card.addEventListener("click", function() {
            const options = this.querySelector(".options");
            options.style.display = options.style.display === "block" ? "none" : "block";
        });
    });

    // Search Filter
    document.getElementById("searchInput").addEventListener("keyup", function () {
        let filter = this.value.toLowerCase();
        let cards = document.querySelectorAll(".question-card");

        cards.forEach(card => {
            let text = card.textContent.toLowerCase();
            card.style.display = text.includes(filter) ? "" : "none";
        });
    });

    // Copy Question
    function copyQuestion(event, btn) {
        event.stopPropagation();
        const card = btn.closest(".question-card");
        const text = card.innerText;
        navigator.clipboard.writeText(text);
        btn.innerText = "✅ Copied!";
        setTimeout(() => btn.innerText = "📋 Copy Question", 1500);
    }
</script>

</body>
</html>