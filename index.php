<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Exam</title>

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
    background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.6));
}

/* Glass Navbar */
.navbar {
    background: rgba(0,0,0,0.6) !important;
    backdrop-filter: blur(10px);
}

.navbar-brand {
    font-weight: bold;
    font-size: 1.5rem;
    letter-spacing: 1px;
}

.nav-link {
    color: #fff !important;
    transition: 0.3s;
}

.nav-link:hover {
    color: #00d4ff !important;
}

/* Hero Section */
.hero {
    min-height: 90vh;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    color:white;
}

.glass-card {
    padding:60px;
    border-radius:25px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(15px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    animation: fadeIn 1.5s ease-in-out;
}

.hero h1 {
    font-size:3rem;
    font-weight:700;
    margin-bottom:20px;
}

.hero p {
    font-size:1.2rem;
    opacity:0.9;
}

/* Buttons */
.btn-glass {
    padding:12px 35px;
    border-radius:30px;
    font-size:1.1rem;
    font-weight:600;
    border:none;
    background: linear-gradient(45deg,#00d4ff,#0072ff);
    color:white;
    transition:0.4s;
}

.btn-glass:hover {
    transform: translateY(-3px);
    box-shadow:0 10px 20px rgba(0,0,0,0.4);
}

/* Feature Section */
.features {
    padding:80px 0;
    background: rgba(0,0,0,0.7);
    color:white;
}

.feature-box {
    padding:30px;
    border-radius:20px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(10px);
    transition:0.4s;
}

.feature-box:hover {
    transform: translateY(-8px);
    background: rgba(255,255,255,0.15);
}

.feature-box i {
    font-size:2rem;
    margin-bottom:15px;
    color:#00d4ff;
}

/* Footer */
footer {
    background: rgba(0,0,0,0.8);
    backdrop-filter: blur(10px);
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

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
<div class="container">
<a class="navbar-brand" href="#"><i class="fa-solid fa-graduation-cap"></i> Online Exam</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav ms-auto">
<li class="nav-item">
<a class="nav-link" href="login.php">Login</a>
</li>
<li class="nav-item">
<a class="nav-link" href="loginroll.php">Register</a>
</li>
</ul>
</div>
</div>
</nav>

<!-- Hero Section -->
<section class="hero">
<div class="container">
<div class="glass-card">
<h1>Welcome to Online Exam Portal</h1>
<p class="mb-4">Do Your Best, and Success Will Follow!</p>
<a href="loginroll.php" class="btn btn-glass me-3">
<i class="fa-solid fa-right-to-bracket"></i> Sign In
</a>
<a href="login.php" class="btn btn-outline-light btn-lg">
Explore
</a>
</div>
</div>
</section>

<!-- Features Section (No DB Needed) -->
<section class="features text-center">
<div class="container">
<div class="row g-4">

<div class="col-md-4">
<div class="feature-box">
<i class="fa-solid fa-clock"></i>
<h5>Timed Exams</h5>
<p>Auto timer system ensures fair and disciplined examination.</p>
</div>
</div>

<div class="col-md-4">
<div class="feature-box">
<i class="fa-solid fa-shield-halved"></i>
<h5>Secure Access</h5>
<p>Role-based login for Principal, HOD, Teacher and Student.</p>
</div>
</div>

<div class="col-md-4">
<div class="feature-box">
<i class="fa-solid fa-chart-line"></i>
<h5>Instant Results</h5>
<p>View performance reports and analytics instantly.</p>
</div>
</div>

</div>
</div>
</section>

<!-- Footer -->
<footer class="text-white text-center py-3">
<div class="container">
<small>&copy; 2025 Online Exam Portal. All rights reserved.</small>
</div>
</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>