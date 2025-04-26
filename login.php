<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL query to retrieve user details
    $sql = "SELECT users.*, admin_approval_status.Aprooved_admin_status 
            FROM users 
            INNER JOIN admin_approval_status 
            ON users.Aprooved_admin_id = admin_approval_status.Aprooved_admin_id 
            WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Check approval status
            if ($row['Aprooved_admin_status'] == 'pending') {
                echo "<script>alert('Your account is not approved yet. Please wait for approval.');</script>";
            } elseif ($row['Aprooved_admin_status'] == 'rejected') {
                echo "<script>alert('Your account request to be an admin has been rejected.');</script>";
            } else {
                // Successful login for approved admins or regular users
                $_SESSION['user_id'] = $row['user_id']; // Store user_id in session
                $_SESSION['full_name'] = $row['first_name'] . " " . $row['last_name'];

                // Redirect based on user type
                if ($row['User_type_id'] == 2) { // User type ID for admin
                    header("Location: dashboard.php");
                } else {
                    header("Location: user_dashboard.php"); // Redirect non-admin users to their dashboard
                }
                exit();
            }
        } else {
            echo "<script>alert('Invalid email or password.');</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<style>
   body {
    height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(to bottom right, 
    rgb(51, 45, 97) 0%, 
    rgb(27, 27, 48) 40%, 
        rgb(19, 19, 24) 100%);
       
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative; /* Needed for positioning pseudo-elements */
    overflow: hidden; /* Ensures shapes don't overflow the viewport */
}

body::before,
body::after {
    content: '';
    position: absolute;
    border-radius: 50%; /* Makes the shapes circular */
    background: linear-gradient(45deg, #f15338, #fbaa40); /* Gradient for the shapes */
    z-index: -1; /* Places the shapes behind the content */
    opacity: 0.8; /* Makes the shapes slightly transparent */
}

body::before {
    width: 200px;
    height: 200px;
    top: -50px;
    left: -50px;
}

body::after {
    width: 500px;
    height: 400px;
    bottom: -100px;
    right: -100px;
}

.container::before {
    content: '';
    position: absolute;
    width: 150px; /* Width of the nut shape */
    height: 100px; /* Height of the nut shape */
    background: linear-gradient(60deg, #f15338, #fbaa40); /* Gradient for the nut shape */
    border-radius: 50%; /* Makes it circular */
    clip-path: polygon(50% 0%, 85% 75%, 100% 50%, 85% 85%, 50% 100%, 15% 85%, 0% 50%, 15% 15%); /* Creates a nut-like shape */
    top: 45%; /* Vertically centers the shape relative to the container */
    left: 30%; /* Positions the shape slightly outside the left edge */
    transform: translateY(-50%); /* Adjusts for vertical centering */
    z-index: -1; /* Places the shape behind the form */
    opacity: 0.8; /* Makes the shape slightly transparent */
}

h1 {
    font-size: 50px;
    text-align: center;
    font-family: sans-serif;
    font-weight: bold;
}

h5 {
    margin-top: -20px;
    font-family: sans-serif;
    font-weight: 100;
    text-align: center;
    font-size: 20px;
}

h2 {
    text-align: center;
    font-family: sans-serif;
}

.outer {
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 0px;
}

.container {
    
    background-color: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
    width: 500px;
}

form {
    width: 100%;
}

.form-group {
    margin-bottom: 20px;
    position: relative;
    margin-inline: 25px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    font-weight: bold;
    margin-left: 30px;
}

.form-group input {
    width: 80%;
    margin-inline: 20px;
    padding: 13px 10px 13px 45px; /* Add padding to accommodate the icon */
    border: solid lightgray 2px;
    border-radius: 5px;
    background-color: rgba(182, 182, 182, 0.2);
    box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
}

.form-group i {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    color: rgb(78, 76, 175);
}

button {
    display: block;
    width: 60%;
    margin-left: 95px;
    padding: 10px;
    border: none;
    border-radius: 25px;
    font-weight: bold;
    background-color: rgb(78, 76, 175);
    color: white;
    margin-top: 10px;
}

button:hover {
    background-color: rgb(49, 48, 105);
    cursor: pointer;
}

p {
    text-align: center;
    margin-top: 10px;
}
a {
    font-weight: 800;
}

.logowhole {
    display: block;
    margin: 0px auto 0;
    max-width: 100%;
    height: 140px;
    filter: drop-shadow(5px 5px 10px rgba(0, 0, 0, 0.5));
}
i{
    border: solid lightgray 3px;
    border-radius: 25px;
    padding: 7px;
    color: rgb(78, 76, 175);
    margin-left: 20px;
    margin-top: 13px;
}
</style>
<body>
    <img src="logowhole.png" alt="" class="logowhole">
    <div class="outer">
    <div class="container">
    <form method="POST">
        <h2 style="text-align: center;">Login</h2>
        <div class="form-group">
            <label for="email">Email</label>
            <i class="fas fa-envelope"></i>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" name="login">Login</button>
        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    </form>
</div>
    </div>
</body>
</html>
