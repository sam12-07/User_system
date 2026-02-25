<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department_id = isset($_POST['department_id']) ? $_POST['department_id'] : '';
    $year = isset($_POST['year']) ? $_POST['year'] : '';

    if (!empty($department_id) && !empty($year)) {
        // Fetch subjects based on department and selected year
        $subject_query = "SELECT id, subject_name FROM subjects WHERE department_id = ? AND year = ?";
        $stmt = $conn->prepare($subject_query);
        $stmt->bind_param("is", $department_id, $year);
        $stmt->execute();
        $subject_result = $stmt->get_result();

        $subjects = [];
        while ($subject = $subject_result->fetch_assoc()) {
            $subjects[] = [
                "id" => $subject['id'],
                "subject_name" => $subject['subject_name']
            ];
        }

        echo json_encode(["status" => "success", "subjects" => $subjects]);
    } else {
        echo json_encode(["status" => "error", "message" => "Department and Year are required."]);
    }
}
?>
