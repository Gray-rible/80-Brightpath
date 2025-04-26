<?php
$currentPage = 'manage_scholarships';
include 'header_sidebar.html';

// Start session and include database connection
session_start();
include 'db.php'; // Replace with your actual database connection file


// Process new requirements and labels
if (isset($_POST['new_requirements']) && is_array($_POST['new_requirements'])) {
    foreach ($_POST['new_requirements'] as $index => $requirement_name) {
        $requirement_description = $_POST['new_descriptions'][$index] ?? null;
        $label = $_POST['new_labels'][$index] ?? null;

        // Skip empty fields
        if (empty($requirement_name) || empty($requirement_description)) {
            continue; // Skip this iteration if fields are empty
        }

        // Insert new requirement
        $new_requirement_sql = "INSERT INTO requirements (requirement_name, description) VALUES (?, ?)";
        $new_requirement_stmt = $conn->prepare($new_requirement_sql);
        $new_requirement_stmt->bind_param('ss', $requirement_name, $requirement_description);
        $new_requirement_stmt->execute();
        $new_requirement_id = $conn->insert_id;

        // Link new requirement to scholarship
        $link_sql = "INSERT INTO scholarship_requirements (scholarship_id, requirement_id, label)
                     VALUES (?, ?, ?)";
        $link_stmt = $conn->prepare($link_sql);
        $link_stmt->bind_param('iis', $scholarship_id, $new_requirement_id, $label);
        $link_stmt->execute();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Scholarship</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        main {
            margin: 20px auto;
            padding: 20px;
            max-width: 800px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        .form-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .form-section h3 {
            margin-top: 0;
            color: #555;
        }
        .flex-container {
        display: flex;
        gap: 20px;
        justify-content: space-between;
    }

    .flex-item {
        flex: 1;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .flex-item h3 {
        margin-top: 0;
        color: #555;
    }

    #imagePreview {
        width: 100%;
        height: 200px;
        border: 2px dashed #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f9f9f9;
        margin-bottom: 10px;
    }

    #uploadButton {
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }

    #uploadButton:hover {
        background-color: #0056b3;
    }

    input[type="text"],
    input[type="number"],
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        margin-bottom: 10px;
    }
        .requirement-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 10px;
        }

        .requirement-group input[type="text"],
        .requirement-group textarea {
            width: calc(100% - 20px);
        }

        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #218838;
        }

        .add-more-btn {
            background-color: #17a2b8;
        }

        .add-more-btn:hover {
            background-color: #138496;
        }
        .requirement-group {
    display: flex;
    gap: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    transition: background-color 0.3s ease;
}
#one{

    background-color: rgba(255, 0, 0, 0.21); /* Light background color */
}

.requirement-group input[type="checkbox"] {
    appearance: none;
    width: 20px;
    height: 20px;
    border: 2px solid #ccc;
    border-radius: 5px; /* Square with rounded corners */
    outline: none;
    cursor: pointer;
    background-color: white;
    transition: all 0.3s ease;
    border-radius: 20px; /* Rounded corners */
}

.requirement-group input[type="checkbox"]:checked {
    border-color: #28a745;
    background-color: #28a745; /* Green background */
    position: relative;
    
    border-radius: 20px; /* Rounded corners */
}


.requirement-group input[type="checkbox"]:checked::before {
    content: '✓'; /* Unicode character for check mark */
    display: block;
    color: white; /* Check mark color */
    font-size: 14px;
    text-align: center;
    margin: auto;
}

.requirement-group label {
    margin: 0;
    font-weight: normal;
}

.requirement-group.selected {
    background-color: #d4edda; /* Light green for selected */
}
    </style>
</head>
<body>
<main>
    <h1>Add a New Scholarship</h1>
    <form action="add_scholarship_process.php" method="POST" enctype="multipart/form-data">
       
<div class="flex-container">
    <!-- Image Preview Section -->
    <div class="flex-item">
        <h3>Upload Image</h3>
        <div id="imagePreview">
            <span id="placeholderText">No Image Selected</span>
            <img id="previewImg" src="" alt="Preview" style="display: none; max-width: 100%; max-height: 100%;">
        </div>
        <button type="button" id="uploadButton" onclick="document.getElementById('image').click()">Upload Img</button>
        <input type="file" id="image" name="image" accept="image/*" style="display: none;" onchange="previewImage(event)">
    </div>

    <!-- Slots, Category, and Status Section -->
    <div class="flex-item">
        <h3>Scholarship Details</h3>
        <label for="slots_available">Slots Available:</label>
        <input type="number" id="slots_available" name="slots_available" required>

        <label for="scholarship_category">Category:</label>
        <select id="scholarship_category" name="scholarship_category_id" required>
            <?php
            $category_sql = "SELECT * FROM Scholarship_Categories";
            $category_result = mysqli_query($conn, $category_sql);
            while ($category_row = mysqli_fetch_assoc($category_result)) {
                echo "<option value='{$category_row['scholarship_category_id']}'>{$category_row['category_name']}</option>";
            }
            ?>
            </select>
            <label for="status">Status:</label>
<select id="status" name="status_id" required>
    <?php
    $status_sql = "SELECT * FROM status";
    $status_result = mysqli_query($conn, $status_sql);

    if ($status_result && mysqli_num_rows($status_result) > 0) {
        while ($status_row = mysqli_fetch_assoc($status_result)) {
            echo "<option value='{$status_row['status_id']}'>{$status_row['status_name']}</option>";
        }
    } else {
        echo "<option value=''>No statuses available</option>";
    }
    ?>
</select>
<!-- Other form fields -->

   

    </div>
</div>

<div class="form-section">

<label for="scholarship_name">Scholarship Name:</label>

<input type="text" id="scholarship_name" name="scholarship_name" required>

<label for="Scholarship_general_description_s1">Description:</label>
<textarea id="Scholarship_general_description_s1" name="Scholarship_general_description_s1" required></textarea>
        </div>
        <!-- Additional Details Section -->
        <div class="form-section">
            <h3>Additional Details</h3>

            <label for="scholarship_selection_criteria">Selection Criteria:</label>
            <textarea id="scholarship_selection_criteria" name="scholarship_selection_criteria" required></textarea>
 
            <label for="scholarship_education_details_s2">Education Details:</label>
            <textarea id="scholarship_education_details_s2" name="scholarship_education_details_s2"></textarea>

            <label for="scholarship_financial_assistance_details_s3">Financial Assistance:</label>
            <textarea id="scholarship_financial_assistance_details_s3" name="scholarship_financial_assistance_details_s3"></textarea>

            <label for="scholarship_maintaing_s4">Maintaining Requirements:</label>
            <textarea id="scholarship_maintaing_s4" name="scholarship_maintaing_s4"></textarea>

            <label for="scholarship_effects_for_others_s5">Effects for Others:</label>
            <textarea id="scholarship_effects_for_others_s5" name="scholarship_effects_for_others_s5"></textarea>

            <label for="forfeiture_of_benefit">Forfeiture of Benefit:</label>
            <textarea id="forfeiture_of_benefit" name="forfeiture_of_benefit"></textarea>

            <label for="note_for_submission">Note for Submission:</label>
            <textarea id="note_for_submission" name="note_for_submission"></textarea>

            
        </div>

<!-- Requirements Section -->
<div class="form-section">
    <h3>Select Requirements</h3>
    <div id="selected-count" style="margin-bottom: 10px; font-weight: bold;">No requirements selected yet</div>
    <?php
$requirements_sql = "SELECT * FROM requirements";
$requirements_result = mysqli_query($conn, $requirements_sql);
while ($requirement = mysqli_fetch_assoc($requirements_result)) {
    echo "<div class='requirement-group'id='one'>";
    echo "<input type='checkbox' name='requirements[]' value='{$requirement['requirement_id']}' onchange='updateSelectedCount(this)'>";
    echo "<label>{$requirement['requirement_name']}</label>";
    echo "<input type='text' name='labels[{$requirement['requirement_id']}]' placeholder='Label (Optional)' style='margin-left: 10px;'>";
    echo "</div>";
}
?>

</div>
        
        </div>

        
<!-- Add New Requirements Section -->
<div class="form-section">
    <h3>Add New Requirements (Optional)</h3>
    <div id="new-requirements-container" style="display: none; position: relative;">
    <button type="button" id="hide-requirements-btn" 
    style="
    background-color:rgb(255, 255, 255); 
    color: red; 
    border: none;
    width: 30px;
    margin-left: 90%;
     height: 30px; cursor: pointer; 
     font-size: 
     24px;font-weight: 800 ;
     display: flex; 
     align-items: center; 
     justify-content: center; 
     box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);">
    &times;
</button>
        <div id="new-requirements">
            <div class="requirement-group">
                <input type="text" name="new_requirements[]" placeholder="Requirement Name">
                <textarea name="new_descriptions[]" placeholder="Requirement Description"></textarea>
                <input type="text" name="new_labels[]" placeholder="Label (Optional)">
            </div>
        </div>
        <button type="button" class="add-more-btn" onclick="addRequirementField()">Add More Requirements</button>
    </div>
    <button type="button" id="show-requirements-btn" onclick="showNewRequirements()">+ Add New Requirement</button>
</div>

        <input type="submit" value="Add Scholarship">
    </form>
</main>

<script>
    function previewImage(event) {
        const fileInput = event.target;
        const previewImg = document.getElementById('previewImg');
        const placeholderText = document.getElementById('placeholderText');
        const uploadButton = document.getElementById('uploadButton');

        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                placeholderText.style.display = 'none';
                uploadButton.textContent = 'Change Img';
            };
            reader.readAsDataURL(fileInput.files[0]);
        } else {
            previewImg.src = '';
            previewImg.style.display = 'none';
            placeholderText.style.display = 'block';
            uploadButton.textContent = 'Upload Img';
        }
    }

    function addRequirementField() {
        const container = document.getElementById('new-requirements');
        const requirementGroup = document.createElement('div');
        requirementGroup.className = 'requirement-group';

        requirementGroup.innerHTML = `
            <input type="text" name="new_requirements[]" placeholder="Requirement Name">
            <textarea name="new_descriptions[]" placeholder="Requirement Description"></textarea>
            <input type="text" name="new_labels[]" placeholder="Label (Optional)">
        `;

        container.appendChild(requirementGroup);
    }
</script>
<script>
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('input[name="requirements[]"]');
        const selectedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
        const selectedCountText = document.getElementById('selected-count');

        if (selectedCount > 0) {
            selectedCountText.textContent = `${selectedCount} selected requirements`;
        } else {
            selectedCountText.textContent = 'No requirements selected yet';
        }
    }

    function toggleRequirement(requirementGroup) {
        const checkbox = requirementGroup.querySelector('input[type="checkbox"]');
        if (checkbox.checked) {
            requirementGroup.style.backgroundColor = '#d4edda'; // Light green for selected
        } else {
            requirementGroup.style.backgroundColor = ''; // Reset to default
        }
    }

    document.addEventListener('change', (event) => {
        if (event.target.name === 'requirements[]') {
            const requirementGroup = event.target.closest('.requirement-group');
            toggleRequirement(requirementGroup);
        }
    });
</script>
<script>
    function updateSelectedCount(checkbox) {
        const checkboxes = document.querySelectorAll('input[name="requirements[]"]');
        const selectedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
        const selectedCountText = document.getElementById('selected-count');

        if (selectedCount > 0) {
            selectedCountText.textContent = `${selectedCount} selected requirements`;
        } else {
            selectedCountText.textContent = 'No requirements selected yet';
        }

        // Update background color of the parent requirement group
        const requirementGroup = checkbox.closest('.requirement-group');
        if (checkbox.checked) {
            requirementGroup.classList.add('selected');
        } else {
            requirementGroup.classList.remove('selected');
        }
    }
</script>
<script>
    function showNewRequirements() {
        const container = document.getElementById('new-requirements-container');
        const showButton = document.getElementById('show-requirements-btn');
        container.style.display = 'block'; // Show the container
        showButton.style.display = 'none'; // Hide the "Add New Requirement" button
    }

    function hideNewRequirements() {
        const container = document.getElementById('new-requirements-container');
        const showButton = document.getElementById('show-requirements-btn');
        container.style.display = 'none'; // Hide the container
        showButton.style.display = 'block'; // Show the "Add New Requirement" button
    }

    function addRequirementField() {
        const container = document.getElementById('new-requirements');
        const requirementGroup = document.createElement('div');
        requirementGroup.className = 'requirement-group';

        requirementGroup.innerHTML = `
            <input type="text" name="new_requirements[]" placeholder="Requirement Name">
            <textarea name="new_descriptions[]" placeholder="Requirement Description"></textarea>
            <input type="text" name="new_labels[]" placeholder="Label (Optional)">
        `;

        container.appendChild(requirementGroup);
    }

    // Attach event listener to the "X" button
    document.getElementById('hide-requirements-btn').addEventListener('click', hideNewRequirements);
</script>
<script>
    // Function to dynamically select the status
    function setStatusDropdown(statusId) {
        const statusDropdown = document.getElementById('status');
        statusDropdown.value = statusId; // Set the value of the dropdown
    }

    // Example: Set the status dynamically (replace '2' with the actual status ID)
    document.addEventListener('DOMContentLoaded', () => {
        const statusId = document.getElementById('status').getAttribute('data-status'); // Get the status ID dynamically
        if (statusId) {
            setStatusDropdown(statusId);
        }
    });
</script>
</body>
</html>