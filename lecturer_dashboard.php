<?php
session_start();

// Optional: Role check (remove if already handled elsewhere)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lecturer Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #667eea, #764ba2);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .glass-card {
        width: 420px;
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
        margin-bottom: 30px;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .btn-custom {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
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

    .logout-btn {
        margin-top: 15px;
        font-size: 14px;
        background: transparent;
        border: 1px solid rgba(255,255,255,0.4);
        color: white;
        border-radius: 20px;
        padding: 8px 15px;
        transition: 0.3s;
    }

    .logout-btn:hover {
        background: rgba(255,255,255,0.2);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .welcome-text {
        font-size: 14px;
        opacity: 0.8;
        margin-bottom: 20px;
    }
</style>
</head>

<body>

<div class="glass-card">
    <h2>Lecturer Dashboard</h2>

    <div class="welcome-text">
        Welcome, Lecturer 👋
    </div>

    <!-- Add Question Button -->
    <a href="add_questions.php">
        <button class="btn-custom">Add Questions</button>
    </a>

    <!-- View Questions Button -->
    <a href="view_questions.php">
        <button class="btn-custom">View Questions</button>
    </a>

    <!-- View Results Button -->
    <a href="view_results.php">
        <button class="btn-custom">View Results</button>
    </a>

    <!-- Logout Button -->
    <a href="logout.php">
        <button class="logout-btn">Logout</button>
    </a>
</div>

</body>
</html>