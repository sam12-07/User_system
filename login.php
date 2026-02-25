<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query the user by email
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $user['PASSWORD'])) {
            // Store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];
            $_SESSION['role'] = $user['role']; // 'principal' or 'lecturer'
            session_start();

// Assuming `$user` contains fetched user data after successful login
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role_id']; // Ensure role_id is mapped correctly
$_SESSION['department'] = $user['department']; // Save department for HOD access

            // If user is principal, go to view_question.php
            if ($user['role_id'] === '1') {
                header("Location: principal_dashboard.php");
                exit();
            } else if ($user['role_id'] === '2') {
                header("Location: add_questions.php");
                exit();
            } else if ($user['role_id'] === '3') {
                header("Location: hod_dashboard.php");
                exit();
            } else if ($user['role_id'] === '4') {
                header("Location: student_dashboard.php");
                exit();
            } else {
                echo "Invalid role!";
                exit();
            }
        } else {
            $error_message = "Invalid credentials!";
        }
    } else {
        $error_message = "No user found!";
    }
}
?>
<!-- HTML form code below... -->


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
body {
    margin:0;
    font-family: 'Poppins', sans-serif;
    background: url('./banner.jpg') no-repeat center center fixed;
    background-size: cover;
}

/* Dark overlay */
.overlay {
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.6));
}

/* Glass Card */
.glass-card {
    width:100%;
    max-width:400px;
    padding:45px;
    border-radius:25px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(15px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    color:white;
    animation: fadeIn 1s ease-in-out;
}

/* Heading */
.glass-card h2 {
    text-align:center;
    margin-bottom:30px;
    font-weight:600;
}

/* Input fields */
.form-control {
    background: rgba(255,255,255,0.15);
    border:none;
    border-radius:30px;
    padding:12px 20px;
    color:white;
}

.form-control::placeholder {
    color:#ddd;
}

.form-control:focus {
    background: rgba(255,255,255,0.25);
    box-shadow:none;
    color:white;
}

/* Login button */
.btn-glass {
    background: linear-gradient(45deg,#00d4ff,#0072ff);
    border:none;
    border-radius:30px;
    padding:12px;
    font-weight:600;
    transition:0.4s;
}

.btn-glass:hover {
    transform: translateY(-4px);
    box-shadow:0 10px 20px rgba(0,0,0,0.4);
}

/* Links */
.link-text a {
    color:#00d4ff;
    text-decoration:none;
}

.link-text a:hover {
    text-decoration:underline;
}

/* Error message */
.alert {
    background: rgba(255,0,0,0.2);
    border:none;
    color:white;
}

/* Show password icon */
.password-wrapper {
    position:relative;
}

.toggle-password {
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#ddd;
}

/* Animation */
@keyframes fadeIn {
    from {opacity:0; transform:translateY(20px);}
    to {opacity:1; transform:translateY(0);}
}
</style>
</head>

<body>

<div class="overlay">
<div class="glass-card">

<h2><i class="fa-solid fa-right-to-bracket"></i> Login</h2>

<?php if (isset($error_message)): ?>
<div class="alert text-center">
<?php echo $error_message; ?>
</div>
<?php endif; ?>

<form method="POST" action="">

<div class="mb-3">
<input type="email" class="form-control" name="email" placeholder="Email" required>
</div>

<div class="mb-3 password-wrapper">
<input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
<i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
</div>

<div class="d-grid">
<button type="submit" class="btn btn-glass">
Login
</button>
</div>

</form>

<div class="text-center mt-4 link-text">
<small>
Don't have an account? <a href="register.php">Register here</a>
</small>
</div>

<div class="text-center mt-2 link-text">
<small>
<a href="forgot_password.php">Forgot Password?</a>
</small>
</div>

</div>
</div>

<script>
function togglePassword() {
    const password = document.getElementById("password");
    const icon = document.querySelector(".toggle-password");

    if (password.type === "password") {
        password.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        password.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>