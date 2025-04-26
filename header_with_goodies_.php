<?php
// filepath: c:\xampp\htdocs\admin\header_sidebar.html
include 'db.php';
session_start();

// Verify session user ID
if (!isset($_SESSION['user_id'])) {
    die("User is not logged in. Please log in.");
}
$user_id = $_SESSION['user_id'];

// Fetch user data
$query = "SELECT first_name, last_name, profile_avatar FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Error: No user data found for the given user ID.");
}

// Set profile avatar and name
$profileAvatar = !empty($user['profile_avatar']) 
    ? 'data:image/jpeg;base64,' . base64_encode($user['profile_avatar']) 
    : 'default_avatar.png';
$fullName = htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css?family=Poster+Gothic" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
 
    <style>
        
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0; background-color:  rgb(255, 255, 255);
}
header {
    padding: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: bolder;
   
    height: 50px;
    margin-left: 300px;
    margin-top: -15px;
    padding-top: 15px;
    background-color: rgb(25, 23, 43);
    position: sticky;
    top: 0;
    padding-bottom: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
}
.logo {
    display: flex;
    height: 38px;
    width: 38px;
    border: solid lightgrey 2px;
}

.logo:hover {
    border: solid lightblue 3px;
}

.logoimg {
    height: 55px;
    width: 55px;
    border-radius: 50%;
   
    object-fit: cover;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.lofg {
   
    margin-right: 20px;
}

.profile span {
    font-weight: bolder;
    color: rgb(52, 41, 54);
    font-size: 25px;
    font-family: 'Poster Gothic';
    src: url('path-to-font/poster-gothic.woff2') format('woff2'),
         url('path-to-font/poster-gothic.woff') format('woff');
    font-weight: 25px;
    font-style: 25px;
}

.lofg img {
    width: max-content;
    height: 50px;
    margin-left: 30px;
    margin-top: 0px;
    filter: drop-shadow(2px 2px 80px rgb(255, 255, 255));
   
}
.avatar-container {
    margin-bottom: 10px;
    flex-shrink: 0; 
}
.name-container {
    text-align: center;
}
.logout {
    display: flex; /* Enables flexbox for horizontal alignment */
    align-items: center; /* Vertically centers items */
    gap: 20px; /* Adds spacing between elements */
    
    align-items: center;
    justify-content: center;
    border-bottom: solid rgb(255, 255, 255) 3px;
    position: fixed;
    top: 0;
    left: 0;
    height: 80px;
    width: 300px;
    background-color: rgb(25, 23, 43);
    line-height: 1.5;
    padding-top: 20px;
}


.logout span.lo {
    color: rgb(255, 255, 255);
    font-family: 'Roboto', sans-serif;
    font-size: 18px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: capitalize;
}

.logout a.lo:hover {
   color: rgb(255, 255, 255) ;
}

aside { 
    width: 300px;
    height: 100vh;
    padding-right: 0px;
    padding-left: 40px;
    box-sizing: border-box;
}
.navss{
    position: fixed;
    top: 100px;
    left: 0;
    width: 300px;
    height: 100vh;
  
   
    padding-right: 0px;
    padding-left: 40px;
    box-sizing: border-box;
    background: linear-gradient(135deg, rgb(46, 42, 77), rgb(23, 23, 31));
}
nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    margin-top: 10px;
}
.fas{
    color: rgb(100, 100, 100);
    width: 30px;
}
.fas.active{
    color:  #1e183f;
}
main {
    margin-left: 300px;
    padding: 20px;
}
.manage{
   
    padding-bottom: 10px;
  
    padding-bottom: 100px;
   }
   

nav a {
    color: #a3bcf1;
    text-decoration: none;
    
}

nav .mes li {
    padding-left: 20px;
    margin-bottom: 10px;
    padding-block: 15px;
    border-radius: 20px;
    MARGIN-RIGHT: 30PX;
}

nav li {
    margin-bottom: 10px;
    padding-block: 8px;
}
nav li.submenu {
    margin-left: 20px;
    margin-right: 20px;
    margin-block: 0px;
    padding-left: 50px;
    
}
nav li.submenu a.ad{
    margin-top: -10px;
}
nav li.submenu:hover {
    background-color: rgb(54, 37, 129);
}

nav li.submenu.active {
    background-color: #ff0000;
   color: rgb(32, 30, 36);
}

nav li:not(.submenu) {
    
}
nav li:not(.submenu):hover {
    color:  #fd009c;
    font-weight: bolder;
   
}

nav li:not(.submenu).active {
    background-color:  rgb(80, 84, 109);
    color:  #ffffff;
}

.mes{
    padding-top: 10px;
}
li.active {
    font-weight: bolder;
    color:  #ffffff;
}

li.active a {
    color: #ffffff; /* Ensure the link text is also white */
    
    font-weight: bolder;
    
}
li.active .fas{
    color:  #ffffff;
}
li:hover .fas{
    color: #ffffff;
}
li:hover .a{
    color: #f7f7f7;
}
nav li a:not(.submenu):hover {
   
    color: #ffffff;
}
    </style>
</head>
<body>
    <header>
        <div class="lofg">
            <img src="logobright.png" alt="Logo">
        </div>
   
        <div class="profile">
            <img class="logoimg" src="<?php echo $profileAvatar; ?>" width="33" height="33" alt="Profile Avatar">
             <span><?php echo $fullName; ?></span>
        </div>
    </header>
    <aside>
        <div class="logout">
            <div class="avatar-container">
                <img class="logoimg" src="<?php echo $profileAvatar; ?>" alt="Profile Avatar">
            </div>
            <div class="name-container">
                <span class="lo"><?php echo $fullName; ?></span>
            </div>
        </div>
        <div class="navss">
            <nav>
                <ul>
                    <div class="manage">
                        <div class="mes">
                            <li <?php if ($currentPage == 'dashboard') echo 'class="active"'; ?>>
                                <i class="fas fa-tachometer-alt"></i> <a href="dashboard.php">Dashboard</a>
                            </li>
                            <li <?php if ($currentPage == 'admin') echo 'class="active"'; ?>>
                                <i class="fas fa-users"></i> <a href="manage_account_admin.php">People</a>
                            </li>
                            <li <?php if ($currentPage == 'manage_applications') echo 'class="active"'; ?>>
                                <i class="fas fa-file-alt"></i><a href="manage_applications.php">Applicants</a>
                            </li>
                            <li <?php if ($currentPage == 'manage_scholarships') echo 'class="active"'; ?>>
                                <i class="fas fa-graduation-cap"></i><a href="manage_scholarships.php">Scholarships</a>
                            </li>
                            <li <?php if ($currentPage == 'inquiries') echo 'class="active"'; ?>>
                                <i class="fas fa-envelope"></i> <a href="inquiries.php">Inquiries</a>
                            </li>
                            <li <?php if ($currentPage == 'settings') echo 'class="active"'; ?>>
                                <i class="fas fa-cog"></i> <a href="settings.php">Settings</a>
                            </li>
                            <li <?php if ($currentPage == 'profile') echo 'class="active"'; ?>>
                                <i class="fas fa-user"></i> <a href="profile.php">Profile</a>
                            </li>
                            <li <?php if ($currentPage == 'test') echo 'class="active"'; ?>>
                                <i class="fas fa-user"></i> <a href="test.php">Test</a>
                            </li>
                        </div>
                    </div>
                </ul>
            </nav>
        </div>
    </aside>
</body>
</html>