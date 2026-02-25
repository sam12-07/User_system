<?php
include 'db.php'; // Include database c

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    header("Location: register.php?role=" . urlencode($role));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Select Role</title>

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
    max-width:500px;
    padding:50px;
    border-radius:25px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(15px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    text-align:center;
    color:white;
    animation: fadeIn 1.2s ease-in-out;
}

.glass-card h2 {
    margin-bottom:30px;
    font-weight:600;
}

/* Role Buttons */
.role-btn {
    padding:15px;
    font-size:1.1rem;
    font-weight:600;
    border-radius:30px;
    border:none;
    margin-bottom:15px;
    transition:0.4s;
    color:white;
}

.role-btn i {
    margin-right:8px;
}

.role-btn:hover {
    transform: translateY(-5px);
    box-shadow:0 10px 20px rgba(0,0,0,0.4);
}

/* Custom Colors */
.principal { background: linear-gradient(45deg,#ff416c,#ff4b2b); }
.hod { background: linear-gradient(45deg,#1d976c,#93f9b9); }
.lecturer { background: linear-gradient(45deg,#f7971e,#ffd200); color:black; }
.student { background: linear-gradient(45deg,#2193b0,#6dd5ed); }

/* Footer Text */
.tagline {
    margin-top:20px;
    font-size:0.9rem;
    opacity:0.8;
}

/* Animation */
@keyframes fadeIn {
    from {opacity:0; transform:translateY(30px);}
    to {opacity:1; transform:translateY(0);}
}
</style>
</head>

<body>

<div class="overlay">

<div class="glass-card">

<h2><i class="fa-solid fa-user-check"></i> Login As</h2>

<div class="d-grid">

<a href="register.php?role=principal" class="role-btn principal">
<i class="fa-solid fa-user-tie"></i> Principal of College
</a>

<a href="register.php?role=hod" class="role-btn hod">
<i class="fa-solid fa-user-gear"></i> Head of Department
</a>

<a href="register.php?role=lecturer" class="role-btn lecturer">
<i class="fa-solid fa-chalkboard-user"></i> Lecturer
</a>

<a href="register.php?role=student" class="role-btn student">
<i class="fa-solid fa-user-graduate"></i> Student
</a>

</div>

<p class="tagline">Choose your role to continue to registration</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>