<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle branch selection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['branch_id'])) {
    $user_id = $_SESSION['user_id'];
    $branch_id = $_POST['branch_id'];
    
    // Check if user already has a branch selected
    $check_sql = "SELECT * FROM user_branches WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing selection
        $update_sql = "UPDATE user_branches SET branch_id = ?, selected_at = NOW() WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $branch_id, $user_id);
        $update_stmt->execute();
    } else {
        // Insert new selection
        $insert_sql = "INSERT INTO user_branches (user_id, branch_id, selected_at) VALUES (?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $branch_id);
        $insert_stmt->execute();
    }
    
    $_SESSION['success_message'] = "Branch selection has been saved!";
    header("Location: view_question.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Branch Selection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .branch-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .branch-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .selected {
            border: 2px solid #0d6efd;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Online Exam </a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-light">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <h2 class="text-center mb-4">Select Your Engineering Branch</h2>
        <p class="text-center mb-5">Choose the branch you want to take the exam for</p>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php
            $branches = [
                ['id' => 1, 'name' => 'Computer Science', 'icon' => '💻', 'description' => 'Focus on software development, algorithms, and computer systems.'],
                ['id' => 2, 'name' => 'Mechanical', 'icon' => '⚙️', 'description' => 'Study of machinery, thermodynamics, and mechanical systems.'],
                ['id' => 3, 'name' => 'Electrical', 'icon' => '⚡', 'description' => 'Covers electronics, power systems, and circuit design.'],
                ['id' => 4, 'name' => 'Civil', 'icon' => '🏗️', 'description' => 'Focuses on structural engineering and construction.'],
                ['id' => 5, 'name' => 'Chemical', 'icon' => '🧪', 'description' => 'Study of chemical processes and material science.'],
                ['id' => 6, 'name' => 'Aerospace', 'icon' => '🚀', 'description' => 'Focuses on aircraft and spacecraft engineering.']
            ];

            foreach ($branches as $branch):
            ?>
            <div class="col">
                <div class="card h-100 branch-card">
                    <div class="card-body text-center">
                        <h1 class="card-title mb-3"><?php echo $branch['icon']; ?></h1>
                        <h5 class="card-title"><?php echo htmlspecialchars($branch['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($branch['description']); ?></p>
                        <form method="POST" action="">
                            <input type="hidden" name="branch_id" value="<?php echo $branch['id']; ?>">
                            <button type="submit" class="btn btn-primary">Select Branch</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <small>&copy; 2025 Online Exam Portal. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>