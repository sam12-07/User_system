<script>
    $(document).ready(function () {
        // Fetch the logged-in user's department ID
        $.ajax({
            url: 'get_user_department.php', // This script returns the department_id of the logged-in user
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status === "success") {
                    let department_id = response.department_id;
                    loadSubjects(department_id);
                } else {
                    alert("❌ Error fetching department information.");
                }
            },
            error: function () {
                alert("❌ Error connecting to the server.");
            }
        });

        // Function to load subjects based on department
        function loadSubjects(department_id) {
            $.ajax({
                url: 'get_subjects.php', // This is your PHP file that fetches subjects
                type: 'POST',
                data: { department_id: department_id },
                dataType: 'json',
                success: function (response) {
                    let subjectDropdown = $('#subject-select');
                    subjectDropdown.html('<option value="">Select Subject</option>');

                    if (response.status === "success" && response.subjects.length > 0) {
                        $.each(response.subjects, function (index, subject) {
                            subjectDropdown.append('<option value="' + subject.id + '">' + subject.subject_name + '</option>');
                        });
                    } else {
                        subjectDropdown.html('<option value="">No subjects available</option>');
                    }
                },
                error: function () {
                    alert("❌ Error loading subjects. Please try again.");
                }
            });
        }

        // Prevent form submission if no subject is selected
        $('#question-form').submit(function (e) {
            if ($('#subject-select').val() === "") {
                alert("❌ Please select a subject before adding a question.");
                e.preventDefault();
            }
        });
    });
</script>
