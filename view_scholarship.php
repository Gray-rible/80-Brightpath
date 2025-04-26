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
            s.Scholarship_selection_criteria, -- Ensure this field is included
            s.status_id, 
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
            CONCAT(u.first_name, ' ', u.middle_name, ' ', u.last_name) AS admin_name,
            c.category_name
        FROM scholarships s
        LEFT JOIN users u ON s.user_id = u.user_id
        LEFT JOIN scholarship_categories c ON s.scholarship_category_id = c.scholarship_category_id
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

$scholarship = $result->fetch_assoc();

// Fetch requirements
$sqlRequirements = "SELECT r.requirement_name, sr.label 
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

// Fetch applicant statistics (approved and pending)
$sqlApplicants = "SELECT 
                    (SELECT COUNT(*) FROM applicants WHERE scholarship_id = ? AND status = 'approved') AS approved_count,
                    (SELECT COUNT(*) FROM applicants WHERE scholarship_id = ? AND status = 'pending') AS pending_count";
$stmt = $conn->prepare($sqlApplicants);
$stmt->bind_param('ii', $scholarshipId, $scholarshipId);
$stmt->execute();
$applicantStats = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $scholarshipId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Scholarship not found.");
}

$scholarship = $result->fetch_assoc();

if (!$scholarship) {
    die("Error: Scholarship details could not be retrieved.");
}
if (!$scholarship['status_name']) {
    echo "Debug: Status name is missing or null.";
}

if (!$scholarship['admin_name']) {
    echo "Debug: Admin name is missing or null.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Scholarship</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: rgb(243, 243, 243);
        }
        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-left: 310px;
            margin-top: 20px;
            margin-right: 10px;
            background-color: white;

        }
        .back-icon {
            top: 20px;
            
            font-size: 20px;
            text-decoration: none;
            color: rgb(209, 72, 72);
            font-weight: bold;
            background: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgb(255, 255, 255);
            width: 2%;
            margin-left: 90%;
            

        }
        .back-icon:hover {
            text-decoration: underline;
        }
        .row {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            height: auto;
        }
        .column {
            flex: 1;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            overflow: hidden;
            
            border: solid rgb(11, 73, 5) 5px;
        }
        .column img {
            width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .requirements-list {
            list-style: none;
            padding: 0;
        }
        .requirements-list li {
            margin-bottom: 10px;
        }
        h1 {
            margin-top: -40px;
            color:rgb(11, 73, 5);
        }
        #hhh{
            border: solid rgb(11, 73, 5) 5px;
            padding: 0px;
            background: linear-gradient(to bottom right, rgb(0, 124, 52), rgb(11, 73, 5));
        }
        .details{
            color: white;
            margin: 0px 0px 0px 0px;
            padding: 15px;
            text-align: center;
           border-top: solid white 3px;
           margin-top: 20px;
           padding-top: 1px;
           margin-inline: 30px;
        }
         .imageco {
            padding: 10px;
            Background-color: white;
         }
         p.status{
            font-weight: bold;
            color: rgb(20, 105, 12);
            font-size: 20px;
            background-color: white;
            padding-block: 5px;
            border-radius: 5px;
            width: 60%;
            margin-left: 20%;
         }
           #two p{
            background: linear-gradient(to bottom right, rgb(255, 255, 255), rgb(255, 255, 255));
            padding: 10px;
            border: solid rgba(11, 87, 4, 0.52) 1px;
            border-radius: 5px;
           }
           #two h3{
            text-align: center;
            color: rgb(21, 73, 5);
            border-bottom: solid rgb(21, 73, 5) 2px;
            padding-bottom: 5px;
           }
           .column{
             background-color: rgb(255, 255, 255);
           }
           table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
            border-radius: 5px;
            border: none;
        }
        th, td {
            padding: 5px; border: none;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
            color: rgb(11, 73, 5);
            font-weight: bold;
            width: 50%;
            border-radius: 25px;
        }
        td{
            height: 70px;
            font-size: 33px;
            border-bottom: solid rgb(255, 255, 255) 2px;
            color: white;
            border-radius: 25px;
            padding: 10px;
            

        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    

<body>
    <div class="container">
        <a href="manage_scholarships.php" class="back-icon">✖ </a>
        <h1><?php echo htmlspecialchars($scholarship['scholarship_name']); ?></h1>
        <div class="row">
            <div class="column" id="hhh">
                <div class="imageco">
                    <img src="<?php echo !empty($scholarship['image_data']) ? 'data:image/jpeg;base64,' . base64_encode($scholarship['image_data']) : 'default-image.jpg'; ?>" alt="Scholarship Image">
                </div>  
                <div class="details">
                    <h3>Management Account:</h3>
                    <p><?php echo htmlspecialchars($scholarship['admin_name']); ?></p>
                    <p class="status"> Status: <?php echo htmlspecialchars($scholarship['status_name']); ?></p>
                    <p>Category: <?php echo htmlspecialchars($scholarship['category_name']); ?></p>
                    <table>
                        <thead>
                        <tr>
                            <th>Approved</th>
                            <th>Pending</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo $applicantStats['approved_count']; ?></td>
                            <td><?php echo $applicantStats['pending_count']; ?></td>
                        </tr>
                        </tbody>
                    </table>
                    <p>Slots: <?php echo $scholarship['slots_available']; ?></p>
                </div>
            </div>
            <div class="column" id="two">
            <h3>Description:</h3>
<p><?php echo nl2br(htmlspecialchars($scholarship['Scholarship_general_description_s1'])); ?></p>

<h3>Selection Criteria:</h3>
<p>
    <?php 
   echo nl2br(htmlspecialchars($scholarship['Scholarship_selection_criteria']));

    ?>
</p>

            </div>

            <div class="column" id="two">
                <h3>Requirements:</h3>
                <ul class="requirements-list">
                    <?php foreach ($requirements as $requirement): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($requirement['requirement_name']); ?></strong>
                            <p><?php echo htmlspecialchars($requirement['label']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="row">
    <div class="column" id="two">
        <h3>Education Details:</h3>
        <p><?php echo nl2br(htmlspecialchars($scholarship['Scholarship_education_details_s2'])); ?></p>
        
        <h3>Financial Assistance:</h3>
        <p><?php echo nl2br(htmlspecialchars($scholarship['Scholarship_financial_assistance_details_s3'])); ?></p>
    </div>

    <div class="column" id="two">
        <h3>Note for Submission:</h3>
        <p><?php echo nl2br(htmlspecialchars($scholarship['note_for_submission'])); ?></p>
    </div>
</div>

<div class="row">
    <div class="column" id="two">
        <h3>Maintaining Requirements:</h3>
        <p><?php echo nl2br(htmlspecialchars($scholarship['Scholarship_maintaing_s4'])); ?></p>
    </div>

    <div class="column" id="two">
        <h3>Effects for other scholarship grants/assistance:</h3>
        <p><?php echo nl2br(htmlspecialchars($scholarship['Scholarship_effects_for_others_s5'])); ?></p>
    </div>

    <div class="column" id="two">
        <h3>Forfeiture of Benefit:</h3>
        <p><?php echo nl2br(htmlspecialchars($scholarship['forfeiture_of_benefit'])); ?></p>
    </div>
</div>

    </div>
</body>
</html>
