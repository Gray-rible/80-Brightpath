<?php
$currentPage = 'inquiries';
include 'header_sidebar.html';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scholarship_finder";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body{
            
            background-color: rgb(255, 255, 255);
        }
        main {
            margin-right: 50px;
            margin-top: 60px;
        }

        .enquiries-container {
            width: 100%;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
    
      
        
        }
        #title {
            background-color: #28a745;
            height: 100%;
            width: 100%;
            color: white;padding: 20px;
            font-weight: bolder;
        }
       #first{
            border: solid   #28a745 3px;
            margin-top: -40px;
        }
        .enquiries-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            
        }

        .enquiries-filters {
            display: flex;
            align-items: center;
        }

        .enquiries-filters input,
        .enquiries-filters select {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .enquiries-filters button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .filter-button {
            background-color: #6c757d;
            color: white;
        }

        .reset-button {
            background-color: #dc3545;
            color: white;
        }

        .enquiries-table {
            width: 93%;
            border-collapse: collapse;
            margin-inline: 30px;
        }

        .enquiries-table th,
        .enquiries-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .enquiries-table th {
            background-color: #f2f2f2;
        }

        .view-button {
            background-color: #17a2b8;
            color: white;
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-button {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .popup-content {
            max-width: 500px;
            margin: 0 auto;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
            color: red;
        }

        .action-btns {
            margin-top: 20px;
            display: flex;
            justify-content: space-around;
        }

        .approve-btn {
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .reject-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        #histo{
            padding: 20px;
        }
    </style>
</head>

<body>
<main>
    <!-- Admin Request Section -->
    <div class="enquiries-container" id="first">
        <div class="enquiries-header">
            <div class="enquiries-title" id="title">Pending Admin Requests</div>
        </div>
        <?php
        $pending_sql = "SELECT users.user_id, users.first_name, users.middle_name, users.last_name, users.email, users.phone, users.job_title,
                            admin_organizations.organization_name, admin_organizations.office_address, admin_organizations.province_district, admin_organizations.official_email, admin_organizations.website,
                            admin_authorizations.authorization_letter, admin_authorizations.id_proof
                        FROM users
                        INNER JOIN admin_organizations ON users.user_id = admin_organizations.user_id
                        INNER JOIN admin_authorizations ON users.user_id = admin_authorizations.user_id
                        WHERE users.Aprooved_admin_id = 1"; // Only show pending requests

        $pending_result = $conn->query($pending_sql);

        if ($pending_result->num_rows > 0) {
            echo "<table class='enquiries-table'>";
            echo "<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr></thead>";
            echo "<tbody>";
            while ($row = $pending_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['user_id']}</td>";
                echo "<td>{$row['first_name']} {$row['middle_name']} {$row['last_name']}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$row['phone']}</td>";
                echo "<td>
                        <button class='view-button' data-id='" . json_encode($row) . "' onclick='viewRequest(this)'>VIEW</button>
                      </td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No pending admin requests.</p>";
        }
        ?>
    </div>

    <!-- History Section -->
    <div class="enquiries-container">
        <div class="enquiries-header">
            <div class="enquiries-title" id="histo" >History of Admin Request</div>
            <div class="enquiries-filters">
                <select id="filter-status">
                    <option value="">Select Status</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <button class="filter-button" onclick="filterRequests()">FILTER</button>
                <button class="reset-button" onclick="resetFilters()">RESET</button>
            </div>
        </div>
        <?php
        $history_sql = "SELECT users.user_id, users.first_name, users.middle_name, users.last_name, users.email, users.phone, admin_approval_status.Aprooved_admin_status
                        FROM users
                        INNER JOIN admin_approval_status ON users.Aprooved_admin_id = admin_approval_status.Aprooved_admin_id
                        WHERE users.Aprooved_admin_id IN (2, 3)"; // Show approved or rejected only

        $history_result = $conn->query($history_sql);

        if ($history_result->num_rows > 0) {
            echo "<table class='enquiries-table'>";
            echo "<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th></tr></thead>";
            echo "<tbody>";
            while ($row = $history_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['user_id']}</td>";
                echo "<td>{$row['first_name']} {$row['middle_name']} {$row['last_name']}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$row['phone']}</td>";
                echo "<td>" . ucfirst($row['Aprooved_admin_status']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No completed admin applications found.</p>";
        }
        ?>
    </div>

<div class="popup" id="popup">
    <span class="close-btn" onclick="closePopup()">X</span>
    <div class="popup-content">
        <h2>Request Details</h2>
        <div id="details"></div>
        <div class="action-btns">
            <form method="POST" style="display:inline;">
                <input type="hidden" name="user_id" id="popup-user-id">
                <button type="submit" name="approve" class="approve-btn">Approve</button>
            </form>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="user_id" id="popup-user-id-reject">
                <button type="submit" name="reject" class="reject-btn">Reject</button>
            </form>
            </div>
    </div>
</div>

<script>
    function viewRequest(button) {
        const data = JSON.parse(button.getAttribute('data-id'));
        const detailsDiv = document.getElementById('details');
        detailsDiv.innerHTML = `
            <p><strong>First Name:</strong> ${data.first_name}</p>
            <p><strong>Middle Name:</strong> ${data.middle_name}</p>
            <p><strong>Last Name:</strong> ${data.last_name}</p>
            <p><strong>Email:</strong> ${data.email}</p>
            <p><strong>Phone:</strong> ${data.phone}</p>
            <p><strong>Job Title:</strong> ${data.job_title}</p>
            <p><strong>Organization Name:</strong> ${data.organization_name}</p>
            <p><strong>Office Address:</strong> ${data.office_address}</p>
            <p><strong>Province/District:</strong> ${data.province_district}</p>
            <p><strong>Official Email:</strong> ${data.official_email}</p>
            <p><strong>Website:</strong> ${data.website}</p>
            <p><strong>Authorization Letter:</strong> <a href="${data.authorization_letter}" target="_blank">View</a></p>
            <p><strong>ID Proof:</strong> <a href="${data.id_proof}" target="_blank">View</a></p>
        `;
        document.getElementById('popup-user-id').value = data.user_id;
        document.getElementById('popup-user-id-reject').value = data.user_id;
        document.getElementById('popup').style.display = 'block';
    }

    function closePopup() {
        document.getElementById('popup').style.display = 'none';
    }

    function filterRequests() {
        const status = document.getElementById('filter-status').value;
        alert(`Filtering by status: ${status}`);
        // You can implement AJAX or other methods to dynamically update the table based on filters.
    }

    function resetFilters() {
        document.getElementById('filter-status').selectedIndex = 0;
        alert('Filters reset.');
        // You can implement AJAX or other methods to reset the filtered table.
    }
</script>

<?php
// Handle Approve Action
if (isset($_POST['approve'])) {
    $user_id = $_POST['user_id'];
    $approve_query = "UPDATE users SET Aprooved_admin_id = 2 WHERE user_id = $user_id"; // Change status to approved
    if ($conn->query($approve_query) === TRUE) {
        echo "<script>alert('User approved as admin!'); window.location.reload();</script>";
    } else {
        echo "<script>alert('Error approving user: {$conn->error}');</script>";
    }
}

// Handle Reject Action
if (isset($_POST['reject'])) {
    $user_id = $_POST['user_id'];
    $reject_query = "UPDATE users SET Aprooved_admin_id = 3 WHERE user_id = $user_id"; // Change status to rejected
    if ($conn->query($reject_query) === TRUE) {
        echo "<script>alert('User request rejected!'); window.location.reload();</script>";
    } else {
        echo "<script>alert('Error rejecting user: {$conn->error}');</script>";
    }
}

$conn->close();
?>
</main>
</body>
</html>