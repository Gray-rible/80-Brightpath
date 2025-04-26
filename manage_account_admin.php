<?php
$currentPage = 'admin';
session_start();

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
    <title>Admin Panel</title>
    
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
</head>

<style>
    /* Your original styles remain unchanged */
    body {
        font-family: Arial, sans-serif;
        background-color:rgb(238, 238, 238);
        margin: 0;
        padding: 0;
    }
  
    .container {
        width: 80%;
        margin: 40px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        
    }
    h1 {
        text-align: center;
        color: #333;
    }
    .toolbar { margin-top:3px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-left: 0px;
        margin-right: 0px;
        padding-top: 13px;
        background-color: white;
        padding-inline: 30px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);
        border-radius: 20px;
    }
    .toolbar button {
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        background-color: #4CAF50;
        color: #fff;
        cursor: pointer;
        transition: background-color 0.3s;
        font-size: 14px;
        
    }
    .toolbar button:hover {
        background-color: #3e8e41;
    }
    .search-form, .filter-form, .sort-form {
        display: inline-block;
        margin-right: 10px;
    }
    .search-form input[type="text"],
    .filter-form select,
    .sort-form select {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-right: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
    }
    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    th {
        background-color:  rgb(43, 95, 46);
        color: rgb(245, 245, 245);
        border-color: rgb(43, 95, 46);
        
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    tr:hover {
        background-color:rgb(196, 231, 255); 
        
    transition: background-color 0.3s ease; /* Smooth transition for background color */

    }
    .popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        padding: 20px;
        border: 1px solid #ddd;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.6);
        display: none;
        width: 500px;
    }
    .popup-content {
        max-width: 700px;
        margin: 0 auto;
        padding-right: 25px;
    }
    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 20px;
        cursor: pointer;
    }
    .close-btn:hover {
        color: #ccc;
    }
    label {
        display: block;
        margin-bottom: 10px;
    }
    input[type="text"], input[type="email"], select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
    }
    input[type="submit"] {
        background-color: #4CAF50;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    input[type="submit"]:hover {
        background-color: #3e8e41;
    }
 
    .tooltip-container{
        margin-top: 10px;
        
    }
    .tooltip-container span{
        margin-right:  10px;
    }
    .add-new-buttonplus{
        padding: 80px;
        background-color: red;
    }
    .toolbar a {
        padding: 10px 15px;
        border: none; 
        box-shadow:  4px 4px 10px 3px rgba(0, 0, 0, 0.3); 
        border-radius: 25px;
        background-color: #4CAF50;
        color: #fff;
        cursor: pointer;
        transition: background-color 0.3s;
        font-size: 20px;
        font-weight: bolder;
        
    }
    
    .toolbar a:hover {
        background-color: #3e8e41;
        box-shadow: none;
    }
    .actions{
        background-color: rgb(255, 255, 255);
    }
    .actions i{
        color: rgb(42, 88, 56);
    }
    .search-form {
            display: flex;
            justify-content: center;
            margin-top: 0px;
        }

        .search-form input[type='text'] {
            padding: 10px;
            width: 200px;
     
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .search-form button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 5px;
        }

        .search-form button:hover {
            background-color: #0056b3;
        }
</style>

<body>
<?php
include 'header_sidebar.html';
?>

<main>
    
<?php

// Initialize SQL query
$sql = "SELECT users.*, user_types.User_type FROM users
        JOIN user_types ON users.User_type_id = user_types.User_type_id";

// Search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
    $sql .= " WHERE first_name LIKE '%$searchTerm%' OR last_name LIKE '%$searchTerm%'";
}

// Filter functionality
if (isset($_GET['filter']) && !empty($_GET['filter'])) {
    $filter = mysqli_real_escape_string($conn, $_GET['filter']);
    $sql .= (strpos($sql, 'WHERE') !== false ? " AND" : " WHERE") . " user_types.User_type = '$filter'";
}

// Sort functionality
if (isset($_GET['sort']) && !empty($_GET['sort'])) {
    $sort = mysqli_real_escape_string($conn, $_GET['sort']);
    $sql .= " ORDER BY $sort";
}
// Fetch distinct places (addresses) from the database
$placesQuery = "SELECT DISTINCT address FROM users WHERE address IS NOT NULL AND address != '' ORDER BY address ASC";
$placesResult = $conn->query($placesQuery);

// Get selected places from the filter
$selectedPlaces = isset($_GET['places']) ? $_GET['places'] : []; 

// Initialize SQL query
$sql = "SELECT users.*, user_types.User_type FROM users
        JOIN user_types ON users.User_type_id = user_types.User_type_id";

// Search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
    $sql .= " WHERE (first_name LIKE '%$searchTerm%' OR last_name LIKE '%$searchTerm%')";
}

// Filter by role
if (isset($_GET['filter']) && !empty($_GET['filter'])) {
    $filter = mysqli_real_escape_string($conn, $_GET['filter']);
    $sql .= (strpos($sql, 'WHERE') !== false ? " AND" : " WHERE") . " user_types.User_type = '$filter'";
}

// Filter by places
if (!empty($selectedPlaces)) {
    $placeConditions = array_map(function ($place) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $place) . "'";
    }, $selectedPlaces);
    $sql .= (strpos($sql, 'WHERE') !== false ? " AND" : " WHERE") . " address IN (" . implode(',', $placeConditions) . ")";
}

// Sort functionality
if (isset($_GET['sort']) && !empty($_GET['sort'])) {
    $sort = mysqli_real_escape_string($conn, $_GET['sort']);
    $sql .= " ORDER BY $sort";
}
// Execute query
$result = $conn->query($sql);

// Display toolbar with search, filter, and sort options
echo "<div class='toolbar'>";
echo "<div class='search-form'>
        <form method='GET' style='display: inline;'>
            <input type='text' name='search' placeholder='Search...'>
            <button type='submit'>Search</button>
        </form>
      </div>";
echo "<div class='filter-form'>
        <form method='GET' style='display: inline;'>
            <select name='filter'>
                <option value=''>All Roles</option>
                <option value='admin'>Admin</option>
                <option value='user'>User</option>
            </select>
            <button type='submit'>Filter</button>
        </form>
      </div>";
echo "<div class='sort-form'>
        <form method='GET' style='display: inline;'>
            <select name='sort'>
                <option value=''>Sort by...</option>
                <option value='first_name'>First Name</option>
                <option value='last_name'>Last Name</option>
                <option value='email'>Email</option>
            </select>
            <button type='submit'>Sort</button>
        </form>
      </div>";
      echo "<div class='tooltip-container'>
       <span class='tooltip-text'>Add new account</span>
        <a class='add-new-buttonplus' onclick=\"window.location.href='register.php'\">+</a>
       
      </div>";



echo "</div>" ; 




 // Display the table
 if ($result->num_rows > 0) {
    echo "<div style='display: flex; align-items: flex-start; gap: 20px;'>"; // Flexbox for side-by-side layout

// Table Section
echo "<div style='width: 80%;'>
    <table>
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Role</th><th>Actions</th></tr>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["user_id"] . "</td>";
        echo "<td>" . htmlspecialchars($row["first_name"]) . " " . htmlspecialchars($row["middle_name"]) . " " . htmlspecialchars($row["last_name"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["phone"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["address"]) . "</td>";  
        echo "<td>" . htmlspecialchars($row["User_type"]) . "</td>";
        echo "<td class='actions'>
                <button class='edit-btn' 
                        data-id='" . $row["user_id"] . "' 
                        data-first_name='" . htmlspecialchars($row["first_name"]) . "' 
                        data-middle_name='" . htmlspecialchars($row["middle_name"]) . "' 
                        data-last_name='" . htmlspecialchars($row["last_name"]) . "' 
                        data-email='" . htmlspecialchars($row["email"]) . "' 
                        data-phone='" . htmlspecialchars($row["phone"]) . "' 
                        data-role='" . htmlspecialchars($row["User_type"]) . "'>
                    <i class='fas fa-edit'></i>
                </button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No results found.</td></tr>";
}
echo "</table>
    </div>";

 } else {
    echo "<p>No results found.</p>";}


// Place Filters Section
echo "<div style='width: 20%; background-color: #f9f9f9; padding: 10px; border-radius: 8px;box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);'>
    <h3>Filter by Places</h3>
    <form method='GET' action=''>";

// Loop through the places result set
while ($placeRow = $placesResult->fetch_assoc()) {
    $isChecked = in_array($placeRow['address'], $selectedPlaces) ? "checked" : "";
    echo "<div style='display: flex; align-items: center; margin-bottom: 5px;'>
    <input type='checkbox' name='places[]' value='" . htmlspecialchars($placeRow['address']) . "' $isChecked style='margin-right: 5px;width: 20px;height: 20px;'>
    <label style='margin: 0;'>" . htmlspecialchars($placeRow['address']) . "</label>
  </div>";
}

echo "    <div style='margin-top: 10px;'>
            
            <a href='manage_account_admin.php' style='padding: 5px 10px; background-color:rgb(180, 76, 76); color: white; border: none; border-radius: 5px; text-decoration: none; cursor: pointer;'>Reset</a>
        </div>
    </form>
    </div>";

echo "<script>
    // Automatically submit the form when a checkbox is clicked
    document.querySelectorAll('input[name=\"places[]\"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            checkbox.closest('form').submit();
        });
    });
</script>";

echo "</div>"; // Close the flex container

// Popup editing form
echo "<div class='popup' id='popup' style='display: none;'>";
echo "<div class='popup-content'>";
echo "<span class='close-btn'>&times;</span>";
echo "<h2>Edit Account</h2>";
echo "<form action='' method='post'>";
echo "<label for='first_name'>First Name:</label>";
echo "<input type='text' id='first_name' name='first_name'>";
echo "<label for='middle_name'>Middle Name:</label>";
echo "<input type='text' id='middle_name' name='middle_name'>";
echo "<label for='last_name'>Last Name:</label>";
echo "<input type='text' id='last_name' name='last_name'>";
echo "<label for='email'>Email:</label>";
echo "<input type='email' id='email' name='email'>";
echo "<label for='phone'>Phone Number:</label>";
echo "<input type='text' id='phone' name='phone'>";
echo "<label for='role'>Role:</label>";
echo "<select id='role' name='role'>";
echo "  <option value='admin'>Admin</option>";
echo "  <option value='user'>User</option>";
echo "</select>";
echo "<input type='hidden' name='user_id' id='user_id'>";
echo "<input type='submit' name='update' value='Update'>";
echo "</form>";
echo "</div>";
echo "</div>";

// JavaScript code for popup
echo "<script>";
echo "const editBtns = document.querySelectorAll('.edit-btn');";
echo "const popup = document.getElementById('popup');";
echo "const closeBtn = document.querySelector('.close-btn');";
echo "editBtns.forEach(btn => {";
echo "btn.addEventListener('click', () => {";
echo "const userId = btn.getAttribute('data-id');";
echo "const first_name = btn.getAttribute('data-first_name');";
echo "const middle_name = btn.getAttribute('data-middle_name');";
echo "const last_name = btn.getAttribute('data-last_name');";
echo "const email = btn.getAttribute('data-email');";
echo "const phone = btn.getAttribute('data-phone');";
echo "const role = btn.getAttribute('data-role');";
echo "document.getElementById('user_id').value = userId;";
echo "document.getElementById('first_name').value = first_name;";
echo "document.getElementById('middle_name').value = middle_name;";
echo "document.getElementById('last_name').value = last_name;";
echo "document.getElementById('email').value = email;";
echo "document.getElementById('phone').value = phone;";
echo "document.getElementById('role').value = role;";
echo "popup.style.display = 'block';";
echo "});";
echo "});";
echo "closeBtn.addEventListener('click', () => {";
echo "popup.style.display = 'none';";
echo "});";
echo "</script>";

// Update user data
if (isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Get the corresponding User_type_id for the selected role
    $role_query = "SELECT User_type_id FROM user_types WHERE User_type = '$role'";
    $role_result = $conn->query($role_query);
    $role_row = $role_result->fetch_assoc();
    $role_id = $role_row['User_type_id'];

    $update_sql = "UPDATE users SET 
        first_name = '$first_name', 
        middle_name = '$middle_name', 
        last_name = '$last_name', 
        email = '$email', 
        phone = '$phone', 
        User_type_id = '$role_id' 
        WHERE user_id = '$user_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('User updated successfully!');</script>";
        echo "<script>window.location.href='manage_account_admin.php';</script>";
    } else {
        echo "<script>alert('Error updating user: " . $conn->error . "');</script>";
    }
}

$conn->close();
?>
</main>
</body>
</html>
