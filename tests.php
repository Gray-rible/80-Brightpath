<?php
include 'db.php';
session_start();
$currentPage = 'test';
include 'header_sidebar.html';
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['user_id'];
$user_profile_data = null;
$user_data = null;

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


// Fetch user data (names) from the `users` table
$query = "SELECT first_name, middle_name, last_name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Your Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Styling remains the same as before */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        main {
            padding: 20px;
            max-width: 70%;
            margin-left:  350px ;
           
            background-color:  rgb(248, 248, 248);
            margin-top: 0px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
  
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: rgb(255, 255, 255);
            font-family: 'Arial', sans-serif;
            font-size: 2em;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5); /* Dark gray shadow with blur */
            
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color:  rgb(45, 88, 45);
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }

        .submit-btn {
            background-color:rgb(52, 119, 61);
            color: white;
            border: none;
            padding: 15px 30px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
            border-radius: 25px;
            font-size: 1em;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .submit-btn:hover {
           
            transform: translateY(-3px);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            border: solid 1px #ddd;
            border_radius: 12px;
            padding: 20px;
            background: radial-gradient(circle,rgb(110, 197, 107),rgb(63, 112, 70));
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
        }
          .layer2 {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            animation: fadeIn 1s ease-in-out;
            border: solid 3px green
          }
        .picture-upload {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
    margin-inline: 60px;
}

.picture-label {
    font-weight: bold;
    margin-bottom: 10px;
    color: white;
    text-align: center;
}

.avatar-preview {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 12px;
    border: 2px solid #ddd;
    margin-bottom: 10px;
    transition: transform 0.3s ease;
}

.avatar-preview:hover {
    transform: scale(1.05);
}

.file-input {
    display: none; /* Hide the default file input */
}

.custom-file-label {
    display: inline-block;
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
        .name-fields {
            flex: 1;
            border-left: 1px solid #ddd;
        }
      input{

      }
        .name-fields .form-group {
            margin-bottom: 15px;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            margin-left: 20px;
            border-radius: 12px;
            border: solid 3px rgb(209, 223, 205);
            padding-inline: 30px;
            padding-top: 10px;
            padding-bottom: 10px;
            font-family: monospace; /* Makes the font monospace */
    text-transform: uppercase; /* Converts text to all caps */
    letter-spacing: 0.1em;
        }
        .name-fields .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            transition: all 0.3s ease;
            font-family: monospace; /* Makes the font monospace */
    text-transform: uppercase; /* Converts text to all caps */
    letter-spacing: 0.1em;
    font-weight: bolder;
    color: rgb(31, 71, 34);    
        }
        .section-title {
            font-size: 1.5em;
            margin-top: 30px;
            margin-bottom: 15px;
            color:rgb(36, 83, 40);
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                align-items: center;
            }

            .avatar-preview {
                margin-right: 0;
                margin-bottom: 20px;
            }

            .grid-container {
                grid-template-columns: 1fr;
            }
        }
        label.nnn{
            color: white;
            font-weight: bold;
        }
        .info-container {
    display: flex;
    gap: 20px; /* Adds space between the two sections */
    margin-bottom: 20px;
}

.personal-info {
    flex: 7; /* Takes 70% of the width */
}

.academic-info {
    flex: 3; /* Takes 30% of the width */
}

.grid-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

@media (max-width: 768px) {
    .info-container {
        flex-direction: column; /* Stacks sections vertically on smaller screens */
    }
}
    </style>
</head>
<body>
    <main>
        <h1>Set Up Your Profile</h1>
        <form method="POST" action="save_profile.php" enctype="multipart/form-data">
            <!-- Profile Header -->
             
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

                <!-- Name Fields -->
                <div class="name-fields">
                    <div class="form-group">
                        <label class="nnn" for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label  class="nnn" for="middle_name">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($user_data['middle_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label  class="nnn" for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name'] ?? ''); ?>" required>
                    </div>
                </div>
            </div>
            <div class="info-container">
    <!-- Personal Information -->
    <div class="personal-info layer2">
        <div class="section-title">Personal Information</div>
        <div class="grid-container">
            <div class="form-group">
                <label for="age">Age</label>
                <input list="age-options" id="age" name="age" value="<?php echo htmlspecialchars($user_profile_data['Age'] ?? ''); ?>" required>
                <datalist id="age-options">
                    <?php for ($i = 18; $i <= 60; $i++): ?>
                        <option value="<?php echo $i; ?>"></option>
                    <?php endfor; ?>
                </datalist>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="male" <?php echo (isset($user_profile_data['Gender']) && $user_profile_data['Gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo (isset($user_profile_data['Gender']) && $user_profile_data['Gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="religion">Religion</label>
                <input list="religion-options" id="religion" name="religion" value="<?php echo htmlspecialchars($user_profile_data['Religion'] ?? ''); ?>" required>
                <datalist id="religion-options">
                    <option value="Christian"></option>
                    <option value="Muslim"></option>
                    <option value="Catholic"></option>
                    <option value="Born Again"></option>
                    <option value="Other"></option>
                </datalist>
            </div>
            <div class="form-group">
                <label for="nationality">Nationality</label>
                <input list="nationality-options" id="nationality" name="nationality" value="<?php echo htmlspecialchars($user_profile_data['Nationality'] ?? ''); ?>" required>
                <datalist id="nationality-options">
                    <option value="Filipino"></option>
                    <option value="American"></option>
                    <option value="Canadian"></option>
                    <option value="Japanese"></option>
                    <option value="Other"></option>
                </datalist>
            </div>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user_profile_data['Address'] ?? ''); ?>" required>
        </div>
    </div>

    <!-- Academic Information -->
    <div class="academic-info layer2">
        <div class="section-title">Academic Information</div>
        <div class="form-group">
            <label for="current_school">Current School</label>
            <input type="text" id="current_school" name="current_school" value="<?php echo htmlspecialchars($user_profile_data['Current_School'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="current_school_address">Current School Address</label>
            <input type="text" id="current_school_address" name="current_school_address" value="<?php echo htmlspecialchars($user_profile_data['Current_School_address'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="grade_level">Grade Level</label>
            <input type="text" id="grade_level" name="grade_level" value="<?php echo htmlspecialchars($user_profile_data['Grade_level'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="student_id">Student ID</label>
            <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($user_profile_data['Student_id'] ?? ''); ?>" required>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="scholarship_id">Select Scholarship Program</label>
    <select id="scholarship_id" name="scholarship_id" onchange="showRequirements()" required>
        <option value="">-- Select a Scholarship --</option>
        <?php while ($scholarship = $scholarshipResult->fetch_assoc()): ?>
            <option value="<?php echo $scholarship['scholarship_id']; ?>">
                <?php echo htmlspecialchars($scholarship['scholarship_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

<!-- Requirements Section -->
<div id="requirements-section" style="display: none;">
    <h3>Requirements</h3>
    <div class="form-group">
        <label for="scholarship_application_form_1">Scholarship Application Form 1</label>
        <input type="file" id="scholarship_application_form_1" name="scholarship_application_form_1">
    </div>
    <div class="form-group">
        <label for="scholarship_application_form_2">Scholarship Application Form 2</label>
        <input type="file" id="scholarship_application_form_2" name="scholarship_application_form_2">
    </div>
    <div class="form-group">
        <label for="report_card_form_138">Report Card (Form 138)</label>
        <input type="file" id="report_card_form_138" name="report_card_form_138">
    </div>
    <div class="form-group">
        <label for="enrollment_form">Enrollment Form</label>
        <input type="file" id="enrollment_form" name="enrollment_form">
    </div>
    <div class="form-group">
        <label for="indigency_certification">Indigency Certification</label>
        <input type="file" id="indigency_certification" name="indigency_certification">
    </div>
    <div class="form-group">
        <label for="supporting_documents">Supporting Documents</label>
        <input type="file" id="supporting_documents" name="supporting_documents">
    </div>
</div>       
            <!-- Submit Button -->
            <button type="submit" class="submit-btn">Submit changes</button>
        </form>

    </main>
    
    <script>
    function showRequirements() {
        const scholarshipDropdown = document.getElementById('scholarship_id');
        const requirementsSection = document.getElementById('requirements-section');
        if (scholarshipDropdown.value) {
            requirementsSection.style.display = 'block';
        } else {
            requirementsSection.style.display = 'none';
        }
    }
</script>
</body>
</html>
