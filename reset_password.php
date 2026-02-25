<?php
session_start();
include 'db.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php"); // Redirect if no email is set
    exit();
}

$email = $_SESSION['reset_email']; // Get email from session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    if ($new_password !== $repeat_password) {
        $error = "❌ Passwords do not match!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in the database
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            unset($_SESSION['reset_email']); // Remove session
            echo "<script>alert('✅ Password changed successfully!'); window.location='login.php';</script>";
        } else {
            $error = "❌ Error updating password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: url('banner.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 100px auto;
            text-align: center;
        }
        .toggle-icon {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="text-2xl font-bold mb-4">Reset Password</h2>
        <?php if (isset($error)) echo "<p class='text-red-500'>$error</p>"; ?>
        
        <p class="mb-4 text-gray-700"><strong>Email:</strong> <?= htmlspecialchars($email) ?></p> <!-- Show email -->

        <form method="POST">
            <div class="relative mb-4">
                <input type="password" name="password" id="password" placeholder="New Password" required class="w-full p-2 border rounded">
                <span class="toggle-icon" onclick="togglePassword('password')">👁️</span>
            </div>
            
            <div class="relative mb-4">
                <input type="password" name="repeat_password" id="repeat_password" placeholder="Repeat Password" required class="w-full p-2 border rounded">
                <span class="toggle-icon" onclick="togglePassword('repeat_password')">👁️</span>
            </div>

            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Update Password</button>
        </form>
        <p class="mt-4"><a href="login.php" class="text-blue-600">Back to Login</a></p>
    </div>

    <script>
        function togglePassword(fieldId) {
            var field = document.getElementById(fieldId);
            field.type = field.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
