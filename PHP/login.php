<?php
session_start();
include("dbconnection.php");

if (isset($_POST['submit'])) {

    $email = strtolower(trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS)));
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    // Prepare the SQL statement
    $query = mysqli_prepare($connection, 'SELECT * FROM user_info WHERE email = ?');

    // Bind the email parameter
    mysqli_stmt_bind_param($query, 's', $email);

    // Execute the statement
    mysqli_stmt_execute($query);

    // Get the result
    $result = mysqli_stmt_get_result($query);

    // Check if the email exists in the database
    if (mysqli_num_rows($result) == 1) {
        // Fetch the row
        $row = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Set session variables for user data
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['address'] = $row['address'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['loggedin'] = true;

            // Redirect to index.php
            header('Location: portfolio.php');
            exit;
        } else {
            // Password doesn't match
            $passwordError = 'Incorrect password.';
        }
    } else {
        // Email not found in the database
        $emailError = 'Email not found.';
    }

    // Close the statement and database connection
    mysqli_stmt_close($query);
    mysqli_close($connection);
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="../CSS/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <?php include("header.php") ?>
    <section class="container col-3 section-login">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="header text-center py-2">
                <h1>Login</h1>
                <p>Please fill in the following form to login.</p>
            </div>
            <div class="row py-3">
                <div class="col form-floating">
                    <input type="text" id="email"
                        class="form-control <?php echo (!empty($emailError)) ? 'is-invalid' : ''; ?>" required
                        placeholder="" maxlength="255" name="email">
                    <label for="email" class="px-4">Email</label>
                    <small <?php echo (!empty($emailError)) ? "class='invalid-feedback'" : "" ?>><?php
                           echo (!empty($emailError)) ? "{$emailError}" : "" ?></small>
                </div>
            </div>
            <div class="row py-3">
                <div class="col form-floating">
                    <input type="password" id="password"
                        class="form-control <?php echo (!empty($passwordError)) ? 'is-invalid' : ''; ?>" required
                        placeholder="" minlength="2" maxlength="15" name="password">
                    <label for="password" class="px-4">Password</label>
                    <small <?php echo (!empty($passwordError)) ? "class='invalid-feedback'" : "" ?>><?php
                           echo (!empty($passwordError)) ? "{$passwordError}" : "" ?></small>
                </div>
            </div>
            <div class="row py-2 text-center button-row">
                <div class="col">
                    <input type="submit" class="btn btn-primary" name="submit" value="Login" id="submit">
                </div>
            </div>
        </form>
        <div>
            <p>Don't have an account? <span><a href="register.php" class="link-primary text-decoration-none">Click here
                        to register</a></span>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
    </script>
    <?php include("../HTML/footer.html"); ?>
</body>

</html>