<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['user_id'];
$user_profile_data = null;

// Fetch user profile data
$query = "SELECT * FROM user_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_profile_data = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        main {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        h2 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <main>
        <h1>User Profile Form</h1>
        <form method="POST" action="save_user_profile.php">
            <!-- Personal Information -->
            <?php
            function displayField($label, $id, $value, $type = 'text') {
                echo "<div class='form-group'>
                        <label for='$id'>$label</label>
                        <input type='$type' id='$id' name='$id' value='" . htmlspecialchars($value ?? '') . "' required>
                      </div>";
            }

            displayField("Address", "address", $user_profile_data['Address']);
            displayField("Age", "age", $user_profile_data['Age'], "number");
            displayField("Gender", "gender", $user_profile_data['Gender']);
            displayField("Religion", "religion", $user_profile_data['Religion']);
            displayField("Nationality", "nationality", $user_profile_data['Nationality']);
            displayField("Current School", "current_school", $user_profile_data['Current_School']);
            displayField("Current School Address", "current_school_address", $user_profile_data['Current_School_address']);
            displayField("Grade Level", "grade_level", $user_profile_data['Grade_level']);
            displayField("Student ID", "student_id", $user_profile_data['Student_id']);
            ?>

            <!-- Father's Information -->
            <h2>Father's Information</h2>
            <?php
            displayField("Father's Last Name", "father_last_name", $user_profile_data['Father_last_name']);
            displayField("Father's First Name", "father_first_name", $user_profile_data['Father_first_name']);
            displayField("Father's Middle Name", "father_middle_name", $user_profile_data['Father_middle_name']);
            displayField("Father's Birthday", "father_birthday", $user_profile_data['Father_birthday'], "date");
            displayField("Father's Occupation", "father_occupation", $user_profile_data['Father_Occupation']);
            displayField("Father's Monthly Salary", "father_monthly_salary", $user_profile_data['Father_monthly_salary'], "number");
            ?>

            <!-- Mother's Information -->
            <h2>Mother's Information</h2>
            <?php
            displayField("Mother's Last Name", "mother_last_name", $user_profile_data['Mother_last_name']);
            displayField("Mother's First Name", "mother_first_name", $user_profile_data['Mother_first_name']);
            displayField("Mother's Middle Name", "mother_middle_name", $user_profile_data['Mother_middle_name']);
            displayField("Mother's Birthday", "mother_birthday", $user_profile_data['Mother_birthday'], "date");
            displayField("Mother's Occupation", "mother_occupation", $user_profile_data['Mother_occupation']);
            displayField("Mother's Monthly Salary", "mother_monthly_salary", $user_profile_data['Mother_monthly_salary'], "number");
            ?>
            <!-- Submit -->
            <button type="submit" class="submit-btn">Submit</button>
        </form>
    </main>
</body>
</html>
