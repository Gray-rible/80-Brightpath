<?php
$currentPage = 'manage_applications';
include 'header_sidebar.html';
include 'db.php';
session_start();
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

// Fetch available scholarships
$scholarshipQuery = "SELECT scholarship_id, image_data, scholarship_name, slots_available, status_id FROM scholarships";
$scholarshipResult = $conn->query($scholarshipQuery);

// Fetch applicants
$applicantQuery = "SELECT applicant_id, first_name, middle_name, last_name FROM applicants";
$applicantResult = $conn->query($applicantQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Scholarship</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
    <style>
        /* Horizontal scrolling for scholarships */
        .scholarship-container {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 20px;
        }
        .scholarship-card {
            flex: 0 0 auto;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .scholarship-card img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        .modal.active {
            display: block;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .overlay.active {
            display: block;
        }
        #applicantCard {
    display: none; /* Hidden by default */
    margin-top: 20px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
        .applicant-card img {
            max-width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
        .scholarship-card.selected {
    border: 2px solid green;
    box-shadow: 0 0 10px rgba(0, 128, 0, 0.5);
    
    background: linear-gradient(to bottom, rgb(255, 255, 255), rgb(186, 255, 202)); /* Light to green gradient */
           
}
#applicantDetails {
    display: none; /* Hidden by default */
    margin-top: 20px;
    text-align: left;
}
    </style>
</head>
<body>
    <main>
        <h1>Apply for Scholarship</h1>

        <!-- Applicant Section -->
        <h2>Applicant Information</h2>
        <button onclick="openModal('chooseApplicantModal')">Choose an Existing Account </button>
        <button onclick="openModal('setupBiodataModal')">Create New Applicant Account</button>

<!-- Applicant Card -->
<!-- Applicant Card -->
<div id="applicantCard" class="applicant-card" style="display: none;">
    <img id="applicantImage" src="default_avatar.png" alt="Applicant Image">
    <h3 id="applicantName"></h3>
    <button id="viewButton" onclick="toggleDetails()">View</button>
    <button id="removeButton" onclick="removeApplicant()">Remove</button>

    <!-- Applicant Details Section -->
    <div id="applicantDetails" style="display: none; margin-top: 20px; text-align: left;">
        <p><strong>First Name:</strong> <span id="detailFirstName"></span></p>
        <p><strong>Middle Name:</strong> <span id="detailMiddleName"></span></p>
        <p><strong>Last Name:</strong> <span id="detailLastName"></span></p>
        <p><strong>Birthdate:</strong> <span id="detailBirthdate"></span></p>
        <p><strong>Place of Birth:</strong> <span id="detailPlaceOfBirth"></span></p>
        <p><strong>Mobile Number:</strong> <span id="detailMobileNumber"></span></p>
        <p><strong>Email Address:</strong> <span id="detailEmailAddress"></span></p>
        <p><strong>Street/Barangay:</strong> <span id="detailStreetBarangay"></span></p>
        <p><strong>Town/City/Municipality:</strong> <span id="detailTownCityMunicipality"></span></p>
        <p><strong>Province:</strong> <span id="detailProvince"></span></p>
        <p><strong>Zip Code:</strong> <span id="detailZipCode"></span></p>
        <p><strong>School Attended:</strong> <span id="detailSchoolAttended"></span></p>
        <p><strong>School ID Number:</strong> <span id="detailSchoolIdNumber"></span></p>
        <p><strong>Grade Level:</strong> <span id="detailGradeLevel"></span></p>
        <p><strong>Course:</strong> <span id="detailCourse"></span></p>
        <p><strong>Type of Disability:</strong> <span id="detailTypeOfDisability"></span></p>
    </div>
</div>

        <!-- Choose Applicant Modal -->
        <div id="chooseApplicantModal" class="modal">
            <h3>Choose an Applicant</h3>
            <ul>
    <?php while ($applicant = $applicantResult->fetch_assoc()): ?>
        <li>
            <button onclick="selectApplicant(<?php echo $applicant['applicant_id']; ?>, '<?php echo htmlspecialchars($applicant['first_name'] . ' ' . $applicant['middle_name'] . ' ' . $applicant['last_name'], ENT_QUOTES, 'UTF-8'); ?>')">
                <?php echo htmlspecialchars($applicant['first_name'] . ' ' . $applicant['middle_name'] . ' ' . $applicant['last_name'], ENT_QUOTES, 'UTF-8'); ?>
            </button>
        </li>
    <?php endwhile; ?>
</ul>
            <button onclick="closeModal('chooseApplicantModal')">Close</button>
        </div>

<!-- Set Up Biodata Modal -->
<div id="setupBiodataModal" class="modal">
    <h3>create new applicant</h3>
    <form id="biodataForm" method="POST" action="create_applicant.php" enctype="multipart/form-data">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br>

        <label for="middle_name">Middle Name:</label>
        <input type="text" id="middle_name" name="middle_name"><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br>

        <label for="birthdate">Birthdate:</label>
        <input type="date" id="birthdate" name="birthdate" required><br>

        <label for="place_of_birth">Place of Birth:</label>
        <input type="text" id="place_of_birth" name="place_of_birth"><br>

        <label for="mobile_number">Mobile Number:</label>
        <input type="text" id="mobile_number" name="mobile_number"><br>

        <label for="email_address">Email Address:</label>
        <input type="email" id="email_address" name="email_address" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <div class="profile-picture-upload">
    <label for="profile_picture">Profile Picture:</label>
    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
    <small>Accepted formats: JPG, PNG, GIF</small><br>
</div>
        <label for="street_barangay">Street/Barangay:</label>
        <input type="text" id="street_barangay" name="street_barangay"><br>

        <label for="town_city_municipality">Town/City/Municipality:</label>
        <input type="text" id="town_city_municipality" name="town_city_municipality"><br>

        <label for="province">Province:</label>
        <input type="text" id="province" name="province"><br>

        <label for="zip_code">Zip Code:</label>
        <input type="text" id="zip_code" name="zip_code"><br>

        <label for="school_attended">School Attended:</label>
        <input type="text" id="school_attended" name="school_attended"><br>

        <label for="school_id_number">School ID Number:</label>
        <input type="text" id="school_id_number" name="school_id_number"><br>

        <label for="grade_level">Grade Level:</label>
        <input type="text" id="grade_level" name="grade_level"><br>

        <label for="course">Course:</label>
        <input type="text" id="course" name="course"><br>

        <label for="type_of_disability">Type of Disability:</label>
        <input type="text" id="type_of_disability" name="type_of_disability"><br>

        <button type="submit">Submit</button>
    </form>
    <button onclick="closeModal('setupBiodataModal')">Close</button>
</div>


   <!-- Scholarships Section -->
   <h2>Select a Scholarship</h2>
        <div class="scholarship-container">
    <?php while ($scholarship = $scholarshipResult->fetch_assoc()): ?>
        <div class="scholarship-card" data-id="<?php echo $scholarship['scholarship_id']; ?>">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($scholarship['image_data']); ?>" alt="Scholarship Image">
            <h3><?php echo htmlspecialchars($scholarship['scholarship_name']); ?></h3>
            <p>Slots Available: <?php echo $scholarship['slots_available']; ?></p>
            <p>Status: <?php echo $scholarship['status_id']; ?></p>
            <button onclick="selectScholarship(<?php echo $scholarship['scholarship_id']; ?>)">Select</button>
        </div>
    <?php endwhile; ?>
</div>
   
      <!-- Reguirements -->
<!-- Scholarship Requirements Section -->
<div id="scholarshipRequirements" style="display: none; margin-top: 20px;">
    <h2>Scholarship Requirements</h2>
<form id="requirementsForm" method="POST" action="submit_application1.php" enctype="multipart/form-data">
    <input type="hidden" id="selectedScholarshipId" name="scholarship_id">
    <input type="hidden" id="selectedApplicantId" name="applicant_id">
    <div id="requirementsContainer"></div>
    <button type="submit">Submit Application</button>
</form>
</div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.getElementById('overlay').classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.getElementById('overlay').classList.remove('active');
        }

        function selectScholarship(scholarshipId) {
    console.log('Scholarship selected:', scholarshipId);

    // Remove the "selected" class from all scholarship cards
    const scholarshipCards = document.querySelectorAll('.scholarship-card');
    scholarshipCards.forEach(card => card.classList.remove('selected'));

    // Add the "selected" class to the clicked card
    const selectedCard = document.querySelector(`.scholarship-card[data-id="${scholarshipId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }

    // Fetch scholarship requirements
    fetch(`get_scholarship_requirements.php?scholarship_id=${scholarshipId}`)
        .then(response => response.json())
        .then(data => {
            const requirementsContainer = document.getElementById('requirementsContainer');
            requirementsContainer.innerHTML = ''; // Clear previous requirements

            // Populate requirements
            data.forEach(requirement => {
                const requirementDiv = document.createElement('div');
                requirementDiv.style.marginBottom = '15px';

                requirementDiv.innerHTML = `
                    <label><strong>${requirement.label || requirement.requirement_name}</strong></label>
                    <p>${requirement.requirement_name}</p>
                    <input type="file" name="requirement_${requirement.scholarship_requirement_id}" accept="image/*" required>
                `;

                requirementsContainer.appendChild(requirementDiv);
            });

            // Show the requirements section
            document.getElementById('scholarshipRequirements').style.display = 'block';

            // Set the selected scholarship ID
            document.getElementById('selectedScholarshipId').value = scholarshipId;
        })
        .catch(error => console.error('Error fetching scholarship requirements:', error));
}
function selectApplicant(applicantId, applicantName) {
    console.log('Applicant selected:', applicantId, applicantName);

    // Display the applicant card
    document.getElementById('applicantCard').style.display = 'block';
    document.getElementById('applicantName').textContent = applicantName;

    // Set the selected applicant ID in the hidden input field
    document.getElementById('selectedApplicantId').value = applicantId;

    // Close the modal
    closeModal('chooseApplicantModal');
}
        function removeApplicant() {
    // Hide the applicant card
    const applicantCard = document.getElementById('applicantCard');
    applicantCard.style.display = 'none';

    // Clear the applicant details
    document.getElementById('applicantImage').src = '';
    document.getElementById('applicantName').textContent = '';

    console.log('Applicant removed');
}
    </script>
    <script>
function toggleDetails() {
    const detailsDiv = document.getElementById('applicantDetails');
    const viewButton = document.getElementById('viewButton');

    if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
        detailsDiv.style.display = 'block'; // Show the details section
        viewButton.textContent = 'Minimize'; // Change button text to "Minimize"
    } else {
        detailsDiv.style.display = 'none'; // Hide the details section
        viewButton.textContent = 'View'; // Change button text to "View"
    }
}

function selectApplicant(applicantId, applicantName) {
    console.log('Applicant selected:', applicantId, applicantName); // Debugging: Log the selected applicant

    // Fetch applicant details from the server
    fetch(`get_applicant_details.php?applicant_id=${applicantId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Applicant Details:', data); // Debugging: Log the response

            // Display the applicant card
            document.getElementById('applicantCard').style.display = 'block';
            document.getElementById('applicantName').textContent = applicantName;

            // Check if profile_picture is empty or null
            if (data.profile_picture) {
                document.getElementById('applicantImage').src = `data:image/jpeg;base64,${data.profile_picture}`;
            } else {
                document.getElementById('applicantImage').src = 'default_avatar.png';
            }

            // Populate the details section
            document.getElementById('detailFirstName').textContent = data.first_name;
            document.getElementById('detailMiddleName').textContent = data.middle_name || 'N/A';
            document.getElementById('detailLastName').textContent = data.last_name;
            document.getElementById('detailBirthdate').textContent = data.birthdate;
            document.getElementById('detailPlaceOfBirth').textContent = data.place_of_birth || 'N/A';
            document.getElementById('detailMobileNumber').textContent = data.mobile_number || 'N/A';
            document.getElementById('detailEmailAddress').textContent = data.email_address;
            document.getElementById('detailStreetBarangay').textContent = data.street_barangay || 'N/A';
            document.getElementById('detailTownCityMunicipality').textContent = data.town_city_municipality || 'N/A';
            document.getElementById('detailProvince').textContent = data.province || 'N/A';
            document.getElementById('detailZipCode').textContent = data.zip_code || 'N/A';
            document.getElementById('detailSchoolAttended').textContent = data.school_attended || 'N/A';
            document.getElementById('detailSchoolIdNumber').textContent = data.school_id_number || 'N/A';
            document.getElementById('detailGradeLevel').textContent = data.grade_level || 'N/A';
            document.getElementById('detailCourse').textContent = data.course || 'N/A';
            document.getElementById('detailTypeOfDisability').textContent = data.type_of_disability || 'N/A';

            // Reset the view button
            document.getElementById('viewButton').textContent = 'View';
            document.getElementById('applicantDetails').style.display = 'none';
        })
        .catch(error => console.error('Error fetching applicant details:', error));

    // Close the modal
    closeModal('chooseApplicantModal');
}
    </script>
</body>
</html>