<?php
// filepath: c:\xampp\htdocs\admin\users_landingpage.php
session_start();
include 'db.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: applicant_log_in_site.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile
$profileQuery = "SELECT first_name, last_name, email_address FROM applicants WHERE applicant_id = ?";
$stmt = $conn->prepare($profileQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profileResult = $stmt->get_result();
$user = $profileResult->fetch_assoc();

// Fetch scholarships
$scholarshipQuery = "SELECT scholarship_id, scholarship_name, Scholarship_description FROM scholarships";
$scholarshipResult = $conn->query($scholarshipQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Landing Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            margin-bottom: 15px;
            color: #007bff;
        }
        .scholarship {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .scholarship h3 {
            margin: 0 0 10px;
        }
        .scholarship p {
            margin: 0 0 10px;
        }
        .scholarship button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .scholarship button:hover {
            background-color: #218838;
        }
        .profile {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .profile p {
            margin: 5px 0;
        }
        .logout {
            text-align: center;
            margin-top: 20px;
        }
        .logout a {
            color: #007bff;
            text-decoration: none;
        }
        .logout a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>!</h1>
    </div>

    <div class="container">
        <!-- Profile Section -->
        <div class="section profile">
            <h2>Your Profile</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email_address']); ?></p>
            <p><a href="edit_profile.php">Edit Profile</a></p>
        </div>

        <!-- Scholarships Section -->
        <div class="section">
            <h2>Available Scholarships</h2>
            <?php if ($scholarshipResult->num_rows > 0): ?>
                <?php while ($scholarship = $scholarshipResult->fetch_assoc()): ?>
                    <div class="scholarship">
                        <h3><?php echo htmlspecialchars($scholarship['scholarship_name']); ?></h3>
                        <p><?php echo htmlspecialchars($scholarship['Scholarship_description']); ?></p>
                        <form method="POST" action="apply_scholarship.php">
                            <input type="hidden" name="scholarship_id" value="<?php echo $scholarship['scholarship_id']; ?>">
                            <button type="submit">Apply Now</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No scholarships available at the moment.</p>
            <?php endif; ?>
        </div>

        <!-- Logout Section -->
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>