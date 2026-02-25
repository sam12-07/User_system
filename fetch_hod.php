<?php
include 'db.php';

if (isset($_POST['year']) && isset($_POST['department_id'])) {
    $year = intval($_POST['year']);
    $department_id = intval($_POST['department_id']);

    $query = "SELECT id, subject_name FROM subjects WHERE year = ? AND department_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ii", $year, $department_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }

        echo json_encode($subjects);
        $stmt->close();
    } else {
        echo json_encode([]);
    }
}
?>
