<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db/config.php';

session_start();

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Query to check if user exists
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $_SESSION['email'] = $email;
        header('location: index.php');
    } else {
        $error = "Invalid email or password.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="login">
        <div class="account-login">
            <h1>Login</h1>
            <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
            <form action="login.php" method="POST" class="login-form">
                <div class="form-group">
                    <input type="email" placeholder="Email" class="form-control" name="email" required="">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Password" class="form-control" name="password" required="">
                </div>
                <button type="submit" class="btn btn-primary" name="login">Login</button>
            </form>
            <div style="margin-top: 20px;">
                <a href="create.php" class="btn btn-success">Create New Account</a>
            </div>
        </div>
    </div>
</body>
</html>
