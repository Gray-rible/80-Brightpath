<!DOCTYPE html>
<html>
<head>
    <title>Scholarship Application Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        fieldset {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            flex: 1 1 calc(50% - 20px); /* Two columns */
            min-width: 300px;
        }
        legend {
            font-weight: bold;
            color: #007bff;
            padding: 0 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="date"],
        input[type="file"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .full-width {
            flex: 1 1 100%; /* Full width for submit button */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Scholarship Application Form</h2>
        <form action="process_application.php" method="POST" enctype="multipart/form-data">
            <fieldset>
                <legend>Personal Information</legend>
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>

                <label for="middle_name">Middle Name:</label>
                <input type="text" id="middle_name" name="middle_name">

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>

                <label for="birthdate">Birthdate:</label>
                <input type="date" id="birthdate" name="birthdate" required>

                <label for="place_of_birth">Place of Birth:</label>
                <input type="text" id="place_of_birth" name="place_of_birth" required>
            </fieldset>

            <fieldset>
                <legend>Contact Information</legend>
                <label for="mobile_number">Mobile Number:</label>
                <input type="text" id="mobile_number" name="mobile_number" required pattern="\+639[0-9]{9}">

                <label for="email_address">Email Address:</label>
                <input type="email" id="email_address" name="email_address" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            </fieldset>

            <fieldset>
                <legend>Address Details</legend>
                <label for="street_barangay">Street & Barangay:</label>
                <input type="text" id="street_barangay" name="street_barangay" required>

                <label for="town_city_municipality">Town/City/Municipality:</label>
                <input type="text" id="town_city_municipality" name="town_city_municipality" required>

                <label for="province">Province:</label>
                <input type="text" id="province" name="province" required>

                <label for="zip_code">Zip Code:</label>
                <input type="text" id="zip_code" name="zip_code" required>
            </fieldset>

            <fieldset>
                <legend>School Information</legend>
                <label for="school_attended">School Attended:</label>
                <input type="text" id="school_attended" name="school_attended" required>

                <label for="school_id_number">School ID Number:</label>
                <input type="text" id="school_id_number" name="school_id_number" required>
            </fieldset>

            <fieldset>
                <legend>Document Uploads</legend>
                <label for="application_letter">Application Letter:</label>
                <input type="file" id="application_letter" name="application_letter" accept="image/*">

                <label for="grades_photocopy">Grades Photocopy:</label>
                <input type="file" id="grades_photocopy" name="grades_photocopy" accept="image/*">

                <label for="registration_form">Registration Form:</label>
                <input type="file" id="registration_form" name="registration_form" accept="image/*">

                <label for="supporting_documents">Supporting Documents:</label>
                <input type="file" id="supporting_documents" name="supporting_documents" accept="image/*">
            </fieldset>

            <div class="full-width">
                <input type="submit" value="Submit Application">
            </div>
        </form>
    </div>
</body>
</html>