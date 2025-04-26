<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit the form.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        "address", "age", "gender", "religion", "nationality",
        "current_school", "current_school_address", "grade_level", "student_id",
        "father_last_name", "father_first_name", "father_middle_name", "father_birthday",
        "father_occupation", "father_monthly_salary",
        "mother_last_name", "mother_first_name", "mother_middle_name", "mother_birthday",
        "mother_occupation", "mother_monthly_salary"
    ];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? null;
    }

    // Check if the user already has a profile
    $checkQuery = "SELECT user_profile_id FROM user_profiles WHERE user_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing profile
        $updateQuery = "
            UPDATE user_profiles SET
                Address = ?, Age = ?, Gender = ?, Religion = ?, Nationality = ?,
                Current_School = ?, Current_School_address = ?, Grade_level = ?, Student_id = ?,
                Father_last_name = ?, Father_first_name = ?, Father_middle_name = ?, Father_birthday = ?,
                Father_Occupation = ?, Father_monthly_salary = ?,
                Mother_last_name = ?, Mother_first_name = ?, Mother_middle_name = ?, Mother_birthday = ?,
                Mother_occupation = ?, Mother_monthly_salary = ?
            WHERE user_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param(
            "sisssssssssssdssssssi",
            $data['address'], $data['age'], $data['gender'], $data['religion'], $data['nationality'],
            $data['current_school'], $data['current_school_address'], $data['grade_level'], $data['student_id'],
            $data['father_last_name'], $data['father_first_name'], $data['father_middle_name'], $data['father_birthday'],
            $data['father_occupation'], $data['father_monthly_salary'],
            $data['mother_last_name'], $data['mother_first_name'], $data['mother_middle_name'], $data['mother_birthday'],
            $data['mother_occupation'], $data['mother_monthly_salary'], $user_id
        );
    } else {
        // Insert new profile
        $insertQuery = "
            INSERT INTO user_profiles (
                user_id, Address, Age, Gender, Religion, Nationality,
                Current_School, Current_School_address, Grade_level, Student_id,
                Father_last_name, Father_first_name, Father_middle_name, Father_birthday,
                Father_Occupation, Father_monthly_salary,
                Mother_last_name, Mother_first_name, Mother_middle_name, Mother_birthday,
                Mother_occupation, Mother_monthly_salary
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param(
            "isisssssssssssdssssss",
            $user_id, $data['address'], $data['age'], $data['gender'], $data['religion'], $data['nationality'],
            $data['current_school'], $data['current_school_address'], $data['grade_level'], $data['student_id'],
            $data['father_last_name'], $data['father_first_name'], $data['father_middle_name'], $data['father_birthday'],
            $data['father_occupation'], $data['father_monthly_salary'],
            $data['mother_last_name'], $data['mother_first_name'], $data['mother_middle_name'], $data["mother_middle_name"], $data["mother_birthday"],
            $data["mother_occupation"], $data["mother_monthly_salary"]
        );
    }

    // Execute the query
    if ($stmt->execute()) {
        echo "Profile saved successfully!";
        header("Location: test2.php"); // Redirect back to the form
        exit();
    } else {
        echo "Error: " . $stmt->error; // Display error for debugging
    }
}
?>
