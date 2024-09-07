<?php
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['email'])) {
    header('location: login.php');
    exit();
}

// Determine the greeting based on the time of day
date_default_timezone_set('Asia/Dhaka'); // Set your timezone
$currentHour = date('H');

if ($currentHour < 12) {
    $greeting = "Good morning";
} elseif ($currentHour < 18) {
    $greeting = "Good afternoon";
} else {
    $greeting = "Good evening";
}

// Include database configuration file
include 'db/config.php';

// Fetch the logged-in user's information
$email = $_SESSION['email'];
$user_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Store user's first name in session
$_SESSION['first_name'] = $user['first_name'];

// Fetch other users' information
$other_users_query = "SELECT * FROM users WHERE email != '$email'";
$other_users_result = mysqli_query($conn, $other_users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style type="text/css">
        body {
            padding-top: 20px;
        }
        .table-custom {
            background-color: #f9f9f9;
            border-radius: 8px;
            overflow: hidden;
        }
        .table-custom th, .table-custom td {
            text-align: center;
            vertical-align: middle;
        }
        .table-custom th {
            background-color: #007bff;
            color: #ffffff;
        }
        .table-custom tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table-custom tr:hover {
            background-color: #ddd;
        }
        .btn-custom {
            padding: 8px 12px;
            font-size: 14px;
            margin-right: 5px;
        }
        .btn-bmi {
            margin-top: 20px;
            font-size: 18px;
            padding: 15px;
            width: 100%;
            background-color: #28a745;
            color: white;
            text-align: center;
        }
        .btn-bmi:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h1><?php echo $greeting . ", " . htmlspecialchars($user['first_name']); ?>!</h1>
            <a href="logout.php" class="btn btn-danger btn-custom">Logout</a>
        </div>

        <!-- User Profile Section -->
        <div class="row">
            <div class="col-12">
                <h2>Your Profile</h2>
                <?php if ($user): ?>
                    <table class="table table-striped table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-custom" title="Update Profile" data-toggle="tooltip">
                                        <span class="fas fa-edit"></span>
                                    </a>
                                </td>
                            </tr>
                        </tbody> 
                    </table> 
                <?php else: ?>
                    <div class="alert alert-danger"><em>No profile information found.</em></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- BMI Button -->
        <div class="row">
            <div class="col-12">
                <a href="bmi_calculator.php" class="btn btn-bmi">Calculate Your BMI</a>
            </div>
        </div>

        <!-- Other Users Section -->
        <div class="row">
            <div class="col-12">
                <h2>Other Users</h2>
                <?php if (mysqli_num_rows($other_users_result) > 0): ?>
                    <table class="table table-striped table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($other_users_result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td>
                                        <a href="show.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-custom" title="View Record" data-toggle="tooltip">
                                            <span class="fa fa-eye"></span>
                                        </a>
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-custom" title="Update Record" data-toggle="tooltip">
                                            <span class="fas fa-edit"></span>
                                        </a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-custom" title="Delete Record" data-toggle="tooltip">
                                            <span class="fa fa-trash"></span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody> 
                    </table> 
                <?php else: ?>
                    <div class="alert alert-danger"><em>No other users found.</em></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Free the result set
mysqli_free_result($user_result);
mysqli_free_result($other_users_result);

// Close the database connection
mysqli_close($conn);
?>
