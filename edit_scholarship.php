<?php
include 'db.php'; // Replace with your actual database connection file
session_start();
$currentPage = 'manage_scholarships';
include 'header_sidebar.html';

// Validate the scholarship_id
if (!isset($_GET['scholarship_id']) || empty($_GET['scholarship_id'])) {
    die("Scholarship ID is missing.");
}

$scholarshipId = intval($_GET['scholarship_id']);

// Fetch scholarship details

$sql = "SELECT 
    s.scholarship_name, 
    s.Scholarship_general_description_s1, 
    s.Scholarship_selection_criteria, 
    s.status_id AS status_id, 
    st.status_name, 
    s.image_data, 
    s.user_id, 
    s.slots_available, 
    s.Scholarship_education_details_s2, 
    s.Scholarship_financial_assistance_details_s3, 
    s.Scholarship_maintaing_s4, 
    s.Scholarship_effects_for_others_s5, 
    s.forfeiture_of_benefit, 
    s.note_for_submission, 
    CONCAT(u.first_name, ' ', u.middle_name, ' ', u.last_name) AS admin_name
FROM scholarships s
LEFT JOIN users u ON s.user_id = u.user_id
LEFT JOIN status st ON s.status_id = st.status_id
WHERE s.scholarship_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $scholarshipId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Scholarship not found.");
}

$scholarship = $result->fetch_assoc();

// Handle form submissions
if (isset($_POST['update_scholarship'])) {
    $scholarship_name = $_POST['scholarship_name'];
    $Scholarship_general_description_s1 = $_POST['Scholarship_general_description_s1'];
    $Scholarship_selection_criteria = $_POST['Scholarship_selection_criteria'];
    $status = $_POST['status'];
    $slots_available = intval($_POST['slots_available']);
    $Scholarship_education_details_s2 = $_POST['Scholarship_education_details_s2'];
    $Scholarship_financial_assistance_details_s3 = $_POST['Scholarship_financial_assistance_details_s3'];
    $Scholarship_maintaing_s4 = $_POST['Scholarship_maintaing_s4'];
    $Scholarship_effects_for_others_s5 = $_POST['Scholarship_effects_for_others_s5'];
    $forfeiture_of_benefit = $_POST['forfeiture_of_benefit'];
    $note_for_submission = $_POST['note_for_submission'];
    

    // Handle image upload
    $imageData = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    } else {
        // Retain the existing image if no new image is uploaded
        $imageData = $scholarship['image_data'];
    }

    $updateSql = "UPDATE scholarships 
        SET scholarship_name = ?, Scholarship_general_description_s1 = ?, Scholarship_selection_criteria = ?, 
            status_id = ?, slots_available = ?, Scholarship_education_details_s2 = ?, 
            Scholarship_financial_assistance_details_s3 = ?, Scholarship_maintaing_s4 = ?, 
            Scholarship_effects_for_others_s5 = ?, forfeiture_of_benefit = ?, note_for_submission = ?, 
            image_data = ? 
        WHERE scholarship_id = ?";
    $updateStmt = $conn->prepare($updateSql);

    if (!$updateStmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $updateStmt->bind_param('ssssisssssssi', $scholarship_name, $Scholarship_general_description_s1, 
              $Scholarship_selection_criteria, $status, $slots_available, $Scholarship_education_details_s2, 
              $Scholarship_financial_assistance_details_s3, $Scholarship_maintaing_s4, 
              $Scholarship_effects_for_others_s5, $forfeiture_of_benefit, $note_for_submission, 
              $imageData, $scholarshipId);

    if ($updateStmt->execute()) {
        echo "<p style='color: green;'>Scholarship updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>Failed to update scholarship: " . $updateStmt->error . "</p>";
    }
}

    // Update requirements
    if (isset($_POST['update_labels'])) {
        foreach ($_POST['labels'] as $scholarshipRequirementId => $label) {
            $updateSql = "UPDATE scholarship_requirements SET label = ? WHERE scholarship_requirement_id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param('si', $label, $scholarshipRequirementId);
            $stmt->execute();
        }
        echo "<p style='color: green;'>Labels updated successfully!</p>";
    }

    // Add new requirements
    if (isset($_POST['add_requirement'])) {
        $requirementName = $_POST['requirement_name'];
        $requirementDescription = $_POST['requirement_description'];

        $insertRequirementSql = "INSERT INTO requirements (requirement_name, description) VALUES (?, ?)";
        $stmt = $conn->prepare($insertRequirementSql);
        $stmt->bind_param('ss', $requirementName, $requirementDescription);
        $stmt->execute();

        $newRequirementId = $conn->insert_id; // Get new requirement ID
        $linkRequirementSql = "INSERT INTO scholarship_requirements (scholarship_id, requirement_id) VALUES (?, ?)";
        $stmt = $conn->prepare($linkRequirementSql);
        $stmt->bind_param('ii', $scholarshipId, $newRequirementId);
        $stmt->execute();
        echo "<p style='color: green;'>New requirement added successfully!</p>";
    }

    // Delete requirements
    if (isset($_POST['delete_requirement'])) {
        $scholarshipRequirementId = $_POST['scholarship_requirement_id'];
        $deleteSql = "DELETE FROM scholarship_requirements WHERE scholarship_requirement_id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param('i', $scholarshipRequirementId);
        $stmt->execute();
        echo "<p style='color: green;'>Requirement removed successfully!</p>";
    }


// Fetch linked requirements
$sqlRequirements = "SELECT sr.scholarship_requirement_id, sr.label, r.requirement_name, r.description
                    FROM scholarship_requirements sr
                    JOIN requirements r ON sr.requirement_id = r.requirement_id
                    WHERE sr.scholarship_id = ?";
$stmt = $conn->prepare($sqlRequirements);
$stmt->bind_param('i', $scholarshipId);
$stmt->execute();
$requirementsResult = $stmt->get_result();
$requirements = [];
while ($row = $requirementsResult->fetch_assoc()) {
    $requirements[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit_scholarship.css">
    <title>Edit Scholarship</title>
    <style>

    </style>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('imagePreview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>
<body>
    <main>
    <div class="toolbar">
    <h2><?php echo htmlspecialchars($scholarship['scholarship_name']); ?></h2>
    <div class="toolbar-buttons">
        <button id="detailsButton" class="active">Details</button>
        <button id="requirementsButton">Requirements</button>
        <button id="categoryButton">Category</button>
    </div>
</div>
<div class="details-form">
<div class="container">
    <h1>Edit Scholarship Details</h1>
    <!-- Scholarship Details Form -->

    
    <!-- Popup Modal -->
    <div id="confirmationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000;">
    <div style="background-color: white; width: 400px; margin: 100px auto; padding: 20px; border-radius: 8px; text-align: center;">
        <h2>Save Changes?</h2>
        <p id="editedFieldsCount">Edited fields: (0/7)</p>
        <ul id="editedFieldsList"></ul> <!-- Add this if missing -->
        <div style="margin: 20px 0;">
            <img id="imagePreviewModal" src="" alt="Edited Image" style="max-width: 100%; max-height: 150px; display: none;">
        </div>
        <button id="saveChanges" style="background-color: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Save</button>
        <button id="discardChanges" style="background-color: red; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Discard</button>
    </div>
</div>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="update_scholarship" value="1">
      



        <!-- Image Upload Section -->
         <label for="image">Scholarship Image:</label>
        <div class="image-preview">
            <img id="imagePreview" src="<?php echo !empty($scholarship['image_data']) ? 'data:image/jpeg;base64,' . base64_encode($scholarship['image_data']) : 'image-default.jpg'; ?>" alt="Scholarship Image">
        </div>
        <label for="image" class="custom-file-label">Change Image</label>
        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">

        <label for="scholarship_name">Scholarship Name:</label>
        <input type="text" id="scholarship_name" name="scholarship_name" value="<?php echo htmlspecialchars($scholarship['scholarship_name']); ?>" required>

        <label for="Scholarship_general_description_s1">General Description:</label>
<textarea id="Scholarship_general_description_s1" name="Scholarship_general_description_s1" required><?php echo htmlspecialchars($scholarship['Scholarship_general_description_s1']); ?></textarea>

<label for="Scholarship_selection_criteria">Selection Criteria:</label>
<textarea id="Scholarship_selection_criteria" name="Scholarship_selection_criteria" required><?php echo htmlspecialchars($scholarship['Scholarship_selection_criteria']); ?></textarea>
<?php
// Fetch statuses from the database
$statusSql = "SELECT status_id, status_name FROM status";
$statusResult = $conn->query($statusSql);
if (!$statusResult) {
    die("Error fetching statuses: " . $conn->error);
}
?>

<label for="status">Status:</label>
<select id="status" name="status" required>
    <?php
    $statusSql = "SELECT status_id, status_name FROM status";
    $statusResult = $conn->query($statusSql);
    if (!$statusResult) {
        die("Error fetching statuses: " . $conn->error);
    }
    while ($statusRow = $statusResult->fetch_assoc()) {
        $selected = $scholarship['status_id'] == $statusRow['status_id'] ? 'selected' : '';
        echo "<option value='{$statusRow['status_id']}' $selected>{$statusRow['status_name']}</option>";
    }
    ?>
</select>


<label for="slots_available">Slots Available:</label>
<input type="number" id="slots_available" name="slots_available" value="<?php echo htmlspecialchars($scholarship['slots_available']); ?>" required>

<label for="Scholarship_education_details_s2">Education Details:</label>
<textarea id="Scholarship_education_details_s2" name="Scholarship_education_details_s2" required><?php echo htmlspecialchars($scholarship['Scholarship_education_details_s2']); ?></textarea>

<label for="Scholarship_financial_assistance_details_s3">Financial Assistance:</label>
<textarea id="Scholarship_financial_assistance_details_s3" name="Scholarship_financial_assistance_details_s3" required><?php echo htmlspecialchars($scholarship['Scholarship_financial_assistance_details_s3']); ?></textarea>

<label for="Scholarship_maintaing_s4">Maintaining Requirements:</label>
<textarea id="Scholarship_maintaing_s4" name="Scholarship_maintaing_s4" required><?php echo htmlspecialchars($scholarship['Scholarship_maintaing_s4']); ?></textarea>

<label for="Scholarship_effects_for_others_s5">Effects for Other Scholarship Grants/Assistance:</label>
<textarea id="Scholarship_effects_for_others_s5" name="Scholarship_effects_for_others_s5" required><?php echo htmlspecialchars($scholarship['Scholarship_effects_for_others_s5']); ?></textarea>

<label for="forfeiture_of_benefit">Forfeiture of Benefit:</label>
<textarea id="forfeiture_of_benefit" name="forfeiture_of_benefit" required><?php echo htmlspecialchars($scholarship['forfeiture_of_benefit']); ?></textarea>

<label for="note_for_submission">Note for Submission:</label>
<textarea id="note_for_submission" name="note_for_submission" required><?php echo htmlspecialchars($scholarship['note_for_submission']); ?></textarea>



        <button type="submit">Update Scholarship</button>
    </form>
</div>
</div>
<div class="category-form">
<div class="container" id="categoryForm">
    <h1>Edit Scholarship Category</h1>
    <!-- Category Selection Form -->
<?php
// Fetch the scholarship details including category name
$scholarshipId = $_GET['scholarship_id']; // Assuming scholarship_id is passed via GET
$scholarshipQuery = "
    SELECT sc.category_name 
    FROM scholarships s
    INNER JOIN scholarship_categories sc 
    ON s.scholarship_category_id = sc.scholarship_category_id
    WHERE s.scholarship_id = ?";
$stmt = $conn->prepare($scholarshipQuery);
$stmt->bind_param("i", $scholarshipId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $categoryName = $row['category_name'];
} else {
    die("Error fetching scholarship details: " . $conn->error);
}
?>
    <form method="POST" action="process_edit_category.php">
        <input type="hidden" name="scholarship_id" value="<?php echo $scholarshipId; ?>">

        <label class="category" for="scholarship_category_id"> <span class="cat"> Category:</span> <?php echo htmlspecialchars($categoryName); ?></label>

        <select id="scholarship_category_id" name="scholarship_category_id" required>
            <option value="">-- Change Category --</option>
            <?php
            // Fetch categories from the scholarship_categories table
            $categorySql = "SELECT scholarship_category_id, category_name FROM scholarship_categories";
            $categoryResult = $conn->query($categorySql);
            if (!$categoryResult) {
                die("Error fetching categories: " . $conn->error);
            }
            while ($categoryRow = $categoryResult->fetch_assoc()) {
                $selected = $scholarship['scholarship_category_id'] == $categoryRow['scholarship_category_id'] ? 'selected' : '';
                echo "<option value='{$categoryRow['scholarship_category_id']}' $selected>{$categoryRow['category_name']}</option>";
            }
            ?>
        </select>

        <button class="catbut" type="submit">Update Category</button> 
   
    </form>
</div>
</div>

<div class="requirements-form">
<div class="container">
    <h1>Edit Requirements</h1>
    <!-- Requirements Labels Form -->
    <form method="POST" action="">
        <input type="hidden" name="update_labels" value="1">

        <table>
            <thead>
                <tr>
                    <th>Requirement Name</th>
                    <th>Description</th>
                    <th style="width: 300px;">Label</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requirements as $requirement): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($requirement['requirement_name']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($requirement['description'])); ?></td>
                        <td>
                        <input type="text" name="labels[<?php echo $requirement['scholarship_requirement_id']; ?>]" 
    value="<?php echo htmlspecialchars($requirement['label']); ?>" 
    style="width: 90%;">
     
                       </td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="scholarship_requirement_id" value="<?php echo $requirement['scholarship_requirement_id']; ?>">
                                <button class="delete" type="submit" name="delete_requirement">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Update Labels</button>
    </form>

    <!-- Add New Requirement Form -->
    <h2>Add New Requirement</h2>
    <form method="POST" action="">
        <input type="hidden" name="add_requirement" value="1">

        <label for="requirement_name">Requirement Name:</label>
        <input type="text" id="requirement_name" name="requirement_name" required><br>

        <label for="requirement_description">Requirement Description:</label>
        <textarea id="requirement_description" name="requirement_description" required></textarea><br>

        <button type="submit">Add Requirement</button>
    </form>
</div>
</div>
</main>
</body>
<script>document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const modal = document.getElementById('confirmationModal');
    const editedFieldsCount = document.getElementById('editedFieldsCount');
    const editedFieldsList = document.createElement('ul'); // Create a <ul> for the edited fields
    const saveButton = document.getElementById('saveChanges');
    const discardButton = document.getElementById('discardChanges');
    const imagePreviewModal = document.getElementById('imagePreviewModal');

    let editedFields = new Set(); // Use a Set to track unique edited fields

    // Append the <ul> to the modal below the count
    editedFieldsCount.insertAdjacentElement('afterend', editedFieldsList);

    // Track changes in form fields
    const originalValues = {};
    form.querySelectorAll('input, textarea, select').forEach(field => {
        originalValues[field.name] = field.value;

        field.addEventListener('input', () => {
            if (field.value !== originalValues[field.name]) {
                editedFields.add(field.name); // Add the field to the set if edited
            } else {
                editedFields.delete(field.name); // Remove the field from the set if reverted
            }

            // Update the edited fields count and names
            const editedFieldNames = Array.from(editedFields);
            editedFieldsCount.textContent = `Edited fields: (${editedFields.size}/${Object.keys(originalValues).length})`;

            // Update the bulleted list of edited fields
            editedFieldsList.innerHTML = ''; // Clear the list
            editedFieldNames.forEach(fieldName => {
                const listItem = document.createElement('li');
                listItem.textContent = fieldName; // Add the field name as a list item
                editedFieldsList.appendChild(listItem);
            });
        });
    });

    // Show the modal when the form is submitted
    form.addEventListener('submit', (e) => {
        e.preventDefault(); // Prevent form submission

        // Update the edited fields count and names in the modal
        const editedFieldNames = Array.from(editedFields);
        editedFieldsCount.textContent = `Edited fields: (${editedFields.size}/${Object.keys(originalValues).length})`;

        // Update the bulleted list of edited fields
        editedFieldsList.innerHTML = ''; // Clear the list
        editedFieldNames.forEach(fieldName => {
            const listItem = document.createElement('li');
            listItem.textContent = fieldName; // Add the field name as a list item
            editedFieldsList.appendChild(listItem);
        });

        // Show the image preview in the modal if the image is edited
        const imageInput = document.getElementById('image');
        if (imageInput.files && imageInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreviewModal.src = e.target.result;
                imagePreviewModal.style.display = 'block';
            };
            reader.readAsDataURL(imageInput.files[0]);
        } else {
            imagePreviewModal.style.display = 'none';
        }

        modal.style.display = 'block';
    });

    // Handle Save button click
    saveButton.addEventListener('click', () => {
    modal.style.display = 'none';
    form.submit(); // Submit the form programmatically
    setTimeout(() => {
        window.location.href = edit_scholarship.php; // Redirect to the same page
    }, 2000); // Add a delay to ensure the form submission completes
});
    // Handle Discard button click
    discardButton.addEventListener('click', () => {
        modal.style.display = 'none';
    });
});</script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const detailsForm = document.querySelector('.details-form');
    const requirementsForm = document.querySelector('.requirements-form');
    const categoryForm = document.querySelector('.category-form');

    const detailsButton = document.getElementById('detailsButton');
    const requirementsButton = document.getElementById('requirementsButton');
    const categoryButton = document.getElementById('categoryButton');

    // Initially show only the details form
    detailsForm.style.display = 'block';
    requirementsForm.style.display = 'none';
    categoryForm.style.display = 'none';

    // Add event listeners to buttons
    detailsButton.addEventListener('click', () => {
        detailsForm.style.display = 'block';
        requirementsForm.style.display = 'none';
        categoryForm.style.display = 'none';

        setActiveButton(detailsButton);
    });

    requirementsButton.addEventListener('click', () => {
        detailsForm.style.display = 'none';
        requirementsForm.style.display = 'block';
        categoryForm.style.display = 'none';

        setActiveButton(requirementsButton);
    });

    categoryButton.addEventListener('click', () => {
        detailsForm.style.display = 'none';
        requirementsForm.style.display = 'none';
        categoryForm.style.display = 'block';

        setActiveButton(categoryButton);
    });

    // Function to set the active button and reset others
    function setActiveButton(activeButton) {
    [detailsButton, requirementsButton, categoryButton].forEach(button => {
        if (button === activeButton) {
            button.classList.add('active'); // Add the active class
            button.style.backgroundColor = '#0056b3'; // Active background color
            button.style.color = 'white'; // Active text color
            button.style.fontWeight = 'bold'; // Optional: Make the active button text bold
        } else {
            button.classList.remove('active'); // Remove the active class
            button.style.backgroundColor = '#007bff'; // Default background color
            button.style.color = 'white'; // Default text color
            button.style.fontWeight = 'normal'; // Reset font weight
        }
    });
}

    // Add hover effects to buttons
    [detailsButton, requirementsButton, categoryButton].forEach(button => {
        button.addEventListener('mouseover', () => {
            if (!button.classList.contains('active')) {
                button.style.backgroundColor = '#004494'; // Hover background color
            }
        });

        button.addEventListener('mouseout', () => {
            if (!button.classList.contains('active')) {
                button.style.backgroundColor = '#007bff'; // Default background color
            }
        });
    });

    // Set the initial active button
    setActiveButton(detailsButton);
});
  </script>
</html>
