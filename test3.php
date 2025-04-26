<?php
$currentPage = 'manage_applications';
include 'header_sidebar.html';
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch existing data from the database for user_profiles
$query = "SELECT * FROM user_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$existingData = $result->fetch_assoc();

// Fetch user profile data
$query = "SELECT * FROM user_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_profile_data = $result->fetch_assoc();
}

// Fetch scholarships for the dropdown
$scholarshipQuery = "SELECT scholarship_id, scholarship_name FROM scholarships";
$scholarshipResult = $conn->query($scholarshipQuery);

// Fetch `first_name`, `middle_name`, and `last_name` from the `users` table
$userQuery = "SELECT first_name, middle_name, last_name FROM users WHERE user_id = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Scholarship Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        main {
            padding: 20px;
            margin-left: 330px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: rgb(26, 87, 46);
        }

        .application-form-container {
            display: none;
            background-color: #fff;
            padding-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 20px auto;
            background: radial-gradient(circle,rgb(110, 197, 107),rgb(63, 112, 70));
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 70%;
            margin-inline:15%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-family: monospace; /* Makes the font monospace */
    text-transform: uppercase; /* Converts text to all caps */
    letter-spacing: 0.1em;
    font-weight: bolder;
    color: rgb(31, 71, 34);
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-container {
            margin-top: 20px;
            text-align: center;
        }

        .form-row {
            display: flex;
            gap: 20px;
           
            margin-block : 20px;
        }

        .form-column {
            flex: 1;
        }
        .picture-upload {
    
    margin-bottom: 20px;
    
}

.picture-label {
    font-weight: bold;
    margin-bottom: 10px;
    color: white;
    
}

.avatar-preview {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 12px;
    border: 2px solid #ddd;
    margin-bottom: 10px;
    transition: transform 0.3s ease;
    margin-left: 20px;
    margin-right: 26px;
}

.avatar-preview:hover {
    transform: scale(1.05);
}
.file-input {
    display: none; /* Hide the default file input */
}

.custom-file-label {
   margin-left: 15px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: bold;
    color: white;
    background-color:rgb(39, 117, 52);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    text-align: center;
    width: 120px;
}

.custom-file-label:hover {
    background-color:rgb(247, 247, 247);
    transform: translateY(-3px);
    color: rgb(39, 117, 52);
}

.custom-file-label:active {
    background-color:rgb(26, 87, 46);
    transform: translateY(0);
}
.firstinfo{
    border-right: solid rgb(255, 255, 255) 1px;
    padding-inline: 20px;
    margin-inline: 20px;
}
.forms{
    background-color: white;
    padding: 20px;
    margin: 20px;
    border-radius: 20px;
     
}
label{
    margin-left: 30px;
    color: rgb(26, 87, 46);
}
    </style>
</head>

<body>
    <main>
        <h1>Apply For Scholarship</h1>
        <button onclick="showApplicationForm()">Send an Application</button>

        <!-- Application Form -->
        <div class="application-form-container" id="applicationForm">
            <h2 style="color: white;text-align: center;padding-bottom: 10px; padding-top: 20px; border-bottom: solid white 2px">Application Form</h2>
            <form method="POST" action="submit_application.php" enctype="multipart/form-data">
                <div class="form-row">

                     <div class="firstinfo">
                    <!-- Left Column -->
                    <div class="form-column">
                        <div class="form-group">
                           <div class="profile-header">
                           <!-- Picture Upload Section -->
                            <div class="picture-upload">
                              <label for="profile_avatar" class="picture-label">Upload 2x2 Photo</label>
                              <!-- Image preview -->
                              <img id="avatar-preview" 
                                src="<?php echo !empty($user_profile_data['profile_avatar']) ? htmlspecialchars($user_profile_data['profile_avatar']) : 'default_avatar.png'; ?>" 
                                  alt="Avatar Preview" 
                                 class="avatar-preview">
                                              <!-- Custom file input -->
                                 <label for="profile_avatar" class="custom-file-label">
                                 <?php echo empty($user_profile_data['profile_avatar']) ? 'Upload New Photo' : 'Change Avatar'; ?>
                                  </label>
                                 <input type="file" id="profile_avatar" name="profile_avatar" accept="image/*" class="file-input" onchange="previewImage(event)">


<script>
    function previewImage(event) {
        const file = event.target.files[0]; // Get the selected file
        const preview = document.getElementById('avatar-preview'); // Get the preview image element
        const label = document.querySelector('.custom-file-label'); // Get the custom file label

        if (file) {
            const reader = new FileReader(); // Create a FileReader to read the file
            reader.onload = function(e) {
                preview.src = e.target.result; // Set the preview image source to the selected file
                label.textContent = 'Change Photo'; // Update the label text
                label.style.backgroundColor = 'rgb(145, 51, 51)'; // Change background color on file selection
            };
            reader.readAsDataURL(file); // Read the file as a data URL
        }
    }
</script>
</div>
</div>
</div>
</div>
</div>
<div style="backgroung-color; pink;" class="form-column">
                    <div class="form-group">
                            <label style="color: white" for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($userData['first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                            <label style="color: white" for="middle_name">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" value="<?php echo htmlspecialchars($userData['middle_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                            <label style="color: white" for="last_name">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($userData['last_name'] ?? ''); ?>" required>
                    </div>
</div> </div>         

                   <div class="forms">
                    <h1>Personal Informatons</h1>
                                 <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($existingData['Address'] ?? ''); ?>" required>
                        </div>
                   <div class="form-row">
                     
                    <!-- Left Column -->
                    <div class="form-column">
                        <div class="form-group">
                            <label for="religion">Religion</label>
                            <input type="text" name="religion" id="religion" value="<?php echo htmlspecialchars($existingData['Religion'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nationality">Nationality</label>
                            <input type="text" name="nationality" id="nationality" value="<?php echo htmlspecialchars($existingData['Nationality'] ?? ''); ?>" required>
                        </div>
                    </div>
                        
                   <!-- right Column -->
                   <div class="form-column">
                    <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" name="age" id="age" value="<?php echo htmlspecialchars($existingData['Age'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender" required>
                                <option value="male" <?php echo (isset($existingData['Gender']) && $existingData['Gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo (isset($existingData['Gender']) && $existingData['Gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                    </div>
                </div>
             </div>
             <div class="forms">
                     <h1> Academic Informations</h1>
                       <div class="form-group">
                            <label for="current_school">Current School</label>
                            <input type="text" name="current_school" id="current_school" value="<?php echo htmlspecialchars($existingData['Current_School'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="current_school_address">Current School Address</label>
                            <input type="text" name="current_school_address" id="current_school_address" value="<?php echo htmlspecialchars($existingData['Current_School_address'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="grade_level">Grade Level</label>
                            <input type="text" name="grade_level" id="grade_level" value="<?php echo htmlspecialchars($existingData['Grade_level'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="student_id">Student ID</label>
                            <input type="text" name="student_id" id="student_id" value="<?php echo htmlspecialchars($existingData['Student_id'] ?? ''); ?>" required>
                        </div>
                    </div>

                        
                <!-- Scholarship Dropdown -->
                <div class="form-group">
                    <label for="scholarship_id">Select Scholarship</label>
                    <select name="scholarship_id" id="scholarship_id" required>
                        <option value="">-- Select Scholarship --</option>
                        <?php while ($row = $scholarshipResult->fetch_assoc()) { ?>
                            <option value="<?php echo $row['scholarship_id']; ?>" <?php echo (isset($existingData['Scholarship_id']) && $existingData['Scholarship_id'] == $row['scholarship_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['scholarship_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- File Upload Fields -->
                <div class="form-group">
                    <label for="scholarship_application_form_1">Scholarship Application Form 1</label>
                    <input type="file" name="scholarship_application_form_1" id="scholarship_application_form_1">
                </div>
                <div class="form-group">
                    <label for="scholarship_application_form_2">Scholarship Application Form 2</label>
                    <input type="file" name="scholarship_application_form_2" id="scholarship_application_form_2">
                </div>
                <div class="form-group">
                    <label for="report_card_form_138">Report Card (Form 138)</label>
                    <input type="file" name="report_card_form_138" id="report_card_form_138">
                </div>
                <div class="form-group">
                    <label for="enrollment_form">Enrollment Form</label>
                    <input type="file" name="enrollment_form" id="enrollment_form">
                </div>
                <div class="form-group">
                    <label for="indigency_certification">Indigency Certification</label>
                    <input type="file" name="indigency_certification" id="indigency_certification">
                </div>
                <div class="form-group">
                    <label for="supporting_documents">Supporting Documents</label>
                    <input type="file" name="supporting_documents" id="supporting_documents">
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="btn-container">
                    <button type="submit">Submit Application</button>
                    <button type="button" onclick="hideApplicationForm()">Cancel</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function showApplicationForm() {
            document.getElementById('applicationForm').style.display = 'block';
        }

        function hideApplicationForm() {
            document.getElementById('applicationForm').style.display = 'none';
        }
    </script>
</body>

</html>