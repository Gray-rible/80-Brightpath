<?php
include 'db.php';
session_start();

// Define the target directory for uploaded files
$targetDir = "uploads/"; // Ensure this directory exists

// Check if the uploads directory exists, if not create it
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}


if (isset($_POST['register'])) {
    // Personal Information
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $job_title = $_POST['job_title'];
    $address = $_POST['address']; // Capture the address field
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Organization Details
    $organization_name = $_POST['organization_name'];
    $organization_type = $_POST['organization_type'];
    $office_address = $_POST['office_address'];
    $province_district = $_POST['province_district'];
    $official_email = $_POST['official_email'];
    $website = $_POST['website'];

    // Authorization & Verification
    $authLetterName = basename($_FILES["auth_letter"]["name"]);
    $authLetterPath = $targetDir . $authLetterName;
    $idProofName = basename($_FILES["id_proof"]["name"]);
    $idProofPath = $targetDir . $idProofName;

    // Move uploaded files
    if (!move_uploaded_file($_FILES["auth_letter"]["tmp_name"], $authLetterPath) || 
        !move_uploaded_file($_FILES["id_proof"]["tmp_name"], $idProofPath)) {
        echo "Error uploading files.";
        exit;
    }

    // Check if email exists
    $check = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($check);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email already registered. Try another one.";
    } else {
        // Get the User_type_id for 'user' (default role)
        $sql_user_type = "SELECT User_type_id FROM user_types WHERE User_type = 'user'";
        $result_user_type = $conn->query($sql_user_type);

        if ($result_user_type->num_rows > 0) {
            $user_type_row = $result_user_type->fetch_assoc();
            $user_type_id = $user_type_row['User_type_id'];
        } else {
            // If 'user' user type is not found, create it
            $sql_create_user_type = "INSERT INTO user_types (User_type) VALUES ('user')";
            $conn->query($sql_create_user_type);
            $user_type_id = $conn->insert_id;
        }

        // Insert into Users Table (includes address and job_title)
        $sql_user = "INSERT INTO users (first_name, middle_name, last_name, email, phone, job_title, address, password, User_type_id, Aprooved_admin_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($sql_user);
        $Aprooved_admin_id = 1; // Automatically set to 'pending'
        $stmt->bind_param("ssssssssii", $first_name, $middle_name, $last_name, $email, $phone, $job_title, $address, $password, $user_type_id, $Aprooved_admin_id);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Insert into Admin Organizations Table
            $sql_org = "INSERT INTO admin_organizations (user_id, organization_name, organization_type, office_address, province_district, official_email, website) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_org);
            $stmt->bind_param("issssss", $user_id, $organization_name, $organization_type, $office_address, $province_district, $official_email, $website);
            $stmt->execute();

            // Insert into Admin Authorizations Table
            $sql_auth = "INSERT INTO admin_authorizations (user_id, authorization_letter, id_proof) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql_auth);
            $stmt->bind_param("iss", $user_id, $authLetterPath, $idProofPath);
            $stmt->execute();

            // Reserve a slot in the user_profiles table
            $sql_profile = "INSERT INTO user_profiles (user_id) VALUES (?)";
            $stmt = $conn->prepare($sql_profile);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            echo "<script>alert('Your account has been submitted for approval.');</script>";
        } else {
            echo "Error registering user.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
    <link rel="stylesheet" href="register_style.css">
</head>
<body>
    <form method="POST" action="register.php" enctype="multipart/form-data" id="register-form">
        <!-- Personal Information -->
        <div class="slide active" id="slide-1">
            <h2>1/3 Personal Information</h2>
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" placeholder="First Name" required>
            <label for="middle_name">Middle Name</label>
            <input type="text" name="middle_name" placeholder="Middle Name" required>
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Email" required>
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <label for="address">Address</label>
<select name="address" required>
    <option value="" disabled selected>Select Address</option>
    
        <option value="Agno">Agno</option>
        <option value="Alaminos City">Alaminos City</option>
        <option value="Anda">Anda</option>
        <option value="Bani">Bani</option>
        <option value="Bolinao">Bolinao</option>
        <option value="Burgos">Burgos</option>
        <option value="Dasol">Dasol</option>
        <option value="Infanta">Infanta</option>
        <option value="Mabini">Mabini</option>
        <option value="Sual">Sual</option>
   
   
        <option value="Aguilar">Aguilar</option>
        <option value="Basista">Basista</option>
        <option value="Binmaley">Binmaley</option>
        <option value="Bugallon">Bugallon</option>
        <option value="Labrador">Labrador</option>
        <option value="Lingayen">Lingayen</option>
        <option value="Mangatarem">Mangatarem</option>
        <option value="Urbiztondo">Urbiztondo</option>
   
        <option value="Bayambang">Bayambang</option>
        <option value="Calasiao">Calasiao</option>
        <option value="Malasiqui">Malasiqui</option>
        <option value="Mapandan">Mapandan</option>
        <option value="San Carlos City">San Carlos City</option>
        <option value="Santa Barbara">Santa Barbara</option>
    
        <option value="Dagupan City">Dagupan City</option>
        <option value="Manaoag">Manaoag</option>
        <option value="Mangaldan">Mangaldan</option>
        <option value="San Fabian">San Fabian</option>
        <option value="San Jacinto">San Jacinto</option>
    
        <option value="Alcala">Alcala</option>
        <option value="Bautista">Bautista</option>
        <option value="Binalonan">Binalonan</option>
        <option value="Laoac">Laoac</option>
        <option value="Pozorrubio">Pozorrubio</option>
        <option value="Santo Tomas">Santo Tomas</option>
        <option value="Sison">Sison</option>
        <option value="Urdaneta City">Urdaneta City</option>
        <option value="Villasis">Villasis</option>

        <option value="Asingan">Asingan</option>
        <option value="Balungao">Balungao</option>
        <option value="Natividad">Natividad</option>
        <option value="Rosales">Rosales</option>
        <option value="San Manuel">San Manuel</option>
        <option value="San Nicolas">San Nicolas</option>
        <option value="San Quintin">San Quintin</option>
        <option value="Santa Maria">Santa Maria</option>
        <option value="Tayug">Tayug</option>
        <option value="Umingan">Umingan</option>

</select>
            
            <label for="job_title">Job Title</label>
            <input type="text" name="job_title" placeholder="Job Title/Position" required>
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Password" required>
            <button class="next-btn" type="button" onclick="showNextSlide()">Next</button><br>
        </div>

        <!-- Organization Details -->
        <div class="slide" id="slide-2">
            <h2>2/3 Organization Details</h2>
            <label for="organization_name">Organization Name</label>
            <input type="text" name="organization_name" placeholder="Organization Name" required>
            <label for="organization_type">Organization Type</label>
            <select name="organization_type" required>
                <option value="Government">Government</option>
                <option value="Private">Private</option>
                <option value="NGO">NGO</option>
                <option value="Other">Other</option>
            </select>
            <label for="office_address">Office Address</label>
            <textarea name="office_address" placeholder="Office Address" required></textarea>
            <label for="province_district">Province/District</label>
            <input type="text" name="province_district" placeholder="Province/District" required>
            <label for="official_email">Official Email</label>
            <input type="email" name="official_email" placeholder="Official Email" required>
            <label for="website">Website</label>
            <input type="text" name="website" placeholder="Website (if applicable)">
            <button class="prev-btn" type="button" onclick="showPrevSlide()">Previous</button>
            <button class="next-btn" type="button" onclick="showNextSlide()">Next</button>
        </div>

        <!-- Authorization & Verification -->
        <div class="slide" id="slide-3">
            <h2>3/3 Authorization & Verification</h2>
            <label for="auth_letter">Upload Authorization Letter (PDF/Image)</label>
            <input type="file" name="auth_letter" required>
            <label for="id_proof">Upload ID Proof (PDF/Image)</label>
            <input type="file" name="id_proof" required>
            <button class="prev-btn" type="button" onclick="showPrevSlide()">Previous</button>
            <button class="submit-btn" type="submit" name="register">Register</button>
        </div>
    </form>

    <script>
        var currentSlide = 1;
        var totalSlides = 3;

        function showNextSlide() {
            if (currentSlide < totalSlides) {
                document.querySelector('.slide.active').classList.remove('active');
                currentSlide++;
                document.getElementById('slide-' + currentSlide).classList.add('active');
            }
        }

        function showPrevSlide() {
            if (currentSlide > 1) {
                document.querySelector('.slide.active').classList.remove('active');
                currentSlide--;
                document.getElementById('slide-' + currentSlide).classList.add('active');
            }
        }
    </script>
    <p style="color: grey; margin-top: -20px;">Already have an account? <a href="login.php">Login</a></p>
</body>
</html>
