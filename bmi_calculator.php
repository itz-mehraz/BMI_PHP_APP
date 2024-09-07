<?php
session_start();
include 'db/config.php';

// Initialize variables
$bmi = '';
$resultMessage = '';
$bmiUserID = 0;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $weight = $_POST['weight'];
    $height = $_POST['height'];

    // Validate input
    if ($name && $age && $gender && $weight && $height) {
        $heightInMeters = $height / 100; // Convert height to meters
        $bmi = $weight / ($heightInMeters * $heightInMeters);

        // BMI classification
        if ($bmi < 18.5) {
            $resultMessage = "You are underweight.";
        } elseif ($bmi >= 18.5 && $bmi <= 24.9) {
            $resultMessage = "You have a normal weight.";
        } elseif ($bmi >= 25 && $bmi <= 29.9) {
            $resultMessage = "You are overweight.";
        } else {
            $resultMessage = "You are obese.";
        }

        // Insert user data into BMIUsers table
        $sql = "INSERT INTO BMIUsers (Name, Age, Gender) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sis", $name, $age, $gender);
            if ($stmt->execute()) {
                $bmiUserID = $stmt->insert_id; // Get the newly inserted user's ID
            } else {
                $resultMessage = "Failed to add user. Please try again.";
            }
            $stmt->close();
        }

        // Insert BMI record into BMIRecords table
        if ($bmiUserID) {
            $sql = "INSERT INTO BMIRecords (BMIUserID, Height, Weight, BMI) VALUES (?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("iddd", $bmiUserID, $height, $weight, $bmi);
                if ($stmt->execute()) {
                    $resultMessage .= " BMI record added successfully.";
                } else {
                    $resultMessage .= " Failed to add BMI record. Please try again.";
                }
                $stmt->close();
            }
        }
    } else {
        $resultMessage = "Please enter all required information.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BMI Calculator</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            color: #555;
        }
        .form-control {
            padding: 10px;
            font-size: 16px;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            width: 100%;
            padding: 10px;
            font-size: 18px;
        }
        .result {
            margin-top: 20px;
            font-size: 18px;
            padding: 15px;
            border-radius: 5px;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }
        .btn-primary {
            margin-top: 20px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>BMI Calculator</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" name="age" id="age" class="form-control" required min="1">
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="weight">Weight (in kg):</label>
                <input type="number" name="weight" id="weight" class="form-control" required step="0.1" min="1">
            </div>
            <div class="form-group">
                <label for="height">Height (in cm):</label>
                <input type="number" name="height" id="height" class="form-control" required step="0.1" min="1">
            </div>
            <button type="submit" class="btn btn-success">Calculate BMI</button>
        </form>
        
        <?php if ($bmi): ?>
            <div class="result alert alert-info">
                Your BMI is: <?php echo round($bmi, 2); ?><br>
                <?php echo $resultMessage; ?>
            </div>
        <?php endif; ?>
        
        <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
