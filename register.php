<?php
include 'db.php'; 

$role = isset($_GET['role']) ? $_GET['role'] : 'student';

// Fetch departments
$departmentQuery = "SELECT * FROM departments";
$departmentResult = $conn->query($departmentQuery);
if (!$departmentResult) {
    die("Error fetching departments: " . $conn->error);
}

$years = ["1st Year", "2nd Year", "3rd Year"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                url('banner.jpg') center/cover no-repeat fixed;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', sans-serif;
}

.glass-card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    padding: 35px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    color: white;
}

.glass-card h2 {
    text-align: center;
    margin-bottom: 25px;
    font-weight: 600;
}

.form-control, .form-select {
    background: rgba(255,255,255,0.85) !important;
    border: none;
    border-radius: 12px;
    padding: 10px 15px;
    color: #000 !important;
    font-weight: 500;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 8px rgba(0,123,255,0.5);
}

select option {
    color: #000 !important;
    background: #ffffff !important;
}

.btn-register {
    background: linear-gradient(45deg, #00c6ff, #0072ff);
    border: none;
    border-radius: 30px;
    padding: 10px;
    font-weight: 600;
    transition: 0.3s;
}

.btn-register:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,114,255,0.4);
}

.password-wrapper {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 14px;
    color: #333;
}

.role-specific {
    display: none;
}
</style>
</head>

<body>

<div class="glass-card">
    <h2>Register as <?= ucfirst($role) ?></h2>

    <form method="POST" action="register_process.php">
        <input type="hidden" name="role" value="<?= $role ?>">

        <div class="mb-3">
            <input type="text" class="form-control" name="first_name" placeholder="First Name" required>
        </div>

        <div class="mb-3">
            <input type="text" class="form-control" name="last_name" placeholder="Last Name" required>
        </div>

        <div class="mb-3">
            <input type="email" class="form-control" name="email" placeholder="Email Address" required>
        </div>

        <div class="mb-3 password-wrapper">
            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
            <span class="toggle-password" onclick="togglePassword()">Show</span>
        </div>

        <!-- Phone -->
        <div class="mb-3 role-specific student">
            <input type="text" class="form-control" name="phone" pattern="[0-9]{10}" placeholder="10-digit Phone Number">
        </div>

        <!-- Year -->
        <div class="mb-3 role-specific student lecturer">
            <select class="form-select" name="year" id="year">
                <option value="">Select Year</option>
                <?php foreach ($years as $year) { ?>
                    <option value="<?= $year ?>"><?= $year ?></option>
                <?php } ?>
            </select>
        </div>

        <!-- Department -->
        <div class="mb-3 role-specific student lecturer hod">
            <select class="form-select" name="department" id="department">
                <option value="">Select Department</option>
                <?php while ($dept = $departmentResult->fetch_assoc()) { ?>
                    <option value="<?= $dept['id'] ?>">
                        <?= htmlspecialchars($dept['department_name']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- Subject -->
        <div class="mb-3 role-specific lecturer">
            <select class="form-select" name="subject" id="subject">
                <option value="">Select Subject</option>
            </select>
        </div>

        <button type="submit" class="btn btn-register w-100">Register</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let departmentDropdown = document.getElementById("department");
    let yearDropdown = document.getElementById("year");
    let subjectDropdown = document.getElementById("subject");

    function fetchSubjects() {
        let departmentId = departmentDropdown.value;
        let year = yearDropdown.value;

        if (departmentId && year) {
            fetch("fetch_subjectofregister.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "department_id=" + encodeURIComponent(departmentId) +
                      "&year=" + encodeURIComponent(year)
            })
            .then(response => response.json())
            .then(data => {
                subjectDropdown.innerHTML = '<option value="">Select Subject</option>';

                if (data.status === "success") {
                    data.subjects.forEach(subject => {
                        let option = document.createElement("option");
                        option.value = subject.id;
                        option.textContent = subject.subject_name;
                        subjectDropdown.appendChild(option);
                    });
                }
            })
            .catch(error => console.error("Error fetching subjects:", error));
        } else {
            subjectDropdown.innerHTML = '<option value="">Select Subject</option>';
        }
    }

    departmentDropdown.addEventListener("change", fetchSubjects);
    yearDropdown.addEventListener("change", fetchSubjects);

    function showFields(role) {
        document.querySelectorAll(".role-specific").forEach(field => {
            field.style.display = "none";
        });

        document.querySelectorAll(".role-specific." + role).forEach(field => {
            field.style.display = "block";
        });
    }

    showFields("<?= $role ?>");
});

function togglePassword() {
    let passField = document.getElementById("password");
    let toggleText = document.querySelector(".toggle-password");

    if (passField.type === "password") {
        passField.type = "text";
        toggleText.textContent = "Hide";
    } else {
        passField.type = "password";
        toggleText.textContent = "Show";
    }
}
</script>

</body>
</html>