<?php
include("dbconnection.php");
if (isset($_POST['submit'])) {
    $username = $password = $email = $address = "";
    $usernameErr = $passwordErr = $emailErr = $addressErr = "";
    //Validate username
    if (empty(trim($_POST["username"]))) {
        $usernameErr = "Please enter a username.";
    } elseif (!preg_match('/^(?=.*\d)(?=.*[A-Z]).{2,15}$/', trim($_POST["username"]))) {
        $usernameErr = "Invalid username.";
    } else {
        $sqlGetUser = "SELECT user_id FROM user_info WHERE username = ?";
        if ($statement = mysqli_prepare($connection, $sqlGetUser)) {
            mysqli_stmt_bind_param($statement, "s", $paramUsername);
            $paramUsername = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (mysqli_stmt_execute($statement)) {
                mysqli_stmt_store_result($statement);
                if (mysqli_stmt_num_rows($statement) == 1) {
                    $usernameErr = "This username is already taken.";
                } else {
                    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                }
            } else {
                $userNameErr = "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($statement);
        }
    }
    //Validate first name
    if (empty(trim($_POST["firstName"]))) {
        $firstNameErr = "Please enter a first name";
    } elseif (!preg_match('/^[A-Za-z]{2,15}$/', $_POST['firstName'])) {
        $firstNameErr = "Invalid first name";
    } else {
        $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_SPECIAL_CHARS);
    }
    //Validate last name
    if (empty(trim($_POST["lastName"]))) {
        $lastNameErr = "Please enter a last name";
    } elseif (!preg_match('/^[A-Za-z]{2,25}$/', $_POST['lastName'])) {
        $lastNameErr = "Invalid last name";
    } else {
        $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_SPECIAL_CHARS);
    }
    //Validate password
    if (empty(trim($_POST["password"]))) {
        $passwordErr = "Please enter a password.";
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{4,}$/', $_POST['password'])) {
        $passwordErr = "Invalid password.";
    } elseif ($_POST['password'] != $_POST['retypePassword']) {
        $passwordErr = "Passwords do not match.";
    } else {
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    }
    //Validate email
    if (empty(trim($_POST["email"]))) {
        $emailErr = "Please enter an email";
    } elseif (!preg_match('/^[\w.%+-]+@[A-Za-z0-9.-]+\.(com|ca|net|org)$/', $_POST['email'])) {
        $emailErr = "Only accept .com, .ca, .net and .org.";
    } else {
        $sqlGetEmail = "SELECT user_id FROM user_info WHERE email = ?";
        if ($statement = mysqli_prepare($connection, $sqlGetEmail)) {
            mysqli_stmt_bind_param($statement, "s", $paramEmail);
            $paramEmail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (mysqli_stmt_execute($statement)) {
                mysqli_stmt_store_result($statement);
                if (mysqli_stmt_num_rows($statement) == 1) {
                    $emailErr = "This email is already taken.";
                } else {
                    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                }
            } else {
                $emailErr = "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($statement);
        }
        //Validate address
        if (empty($_POST["address"])) {
            $addressErr = "Please enter an address";
        } elseif (!preg_match('/^[a-zA-Z0-9,-]+(\s[a-zA-Z0-9,-]+){2,}$/', $_POST['address'])) {
            $addressErr = "Invalid address, atleast two spaces needed.";
        } else {
            $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        // Check input errors before inserting in database
        if (empty($usernameErr) && empty($passwordErr) && empty($addressErr) && empty($emailErr) && empty($firstNameErr) && empty($lastNameErr)) {
            // Prepare an insert statement
            $sql = "INSERT INTO user_info (username, password, email, address, first_name, last_name) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($connection, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssssss", $paramUsername, $paramPassword, $paramEmail, $paramAddress, $firstName, $lastName);
                // Set parameters
                $paramUsername = $username;
                $paramPassword = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                $paramEmail = $email;
                $paramAddress = $address;
                $paramFirstName = $firstName;
                $paramLastName = $lastName;
                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Redirect to login page
                    header("location: login.php");
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
        // Close connection
        mysqli_close($connection);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="../CSS/register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body> <?php include('header.php'); ?> <section class="container container-sm section-register">
        <div class="header text-center py-2">
            <h1>Register</h1>
            <p>Please fill in the following form to register.</p>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="row
            py-3">
                <div class="col form-floating"> <input type="text" id="username" class="form-control <?php echo (!empty($usernameErr)) ? 'is-invalid' : ''; ?>" required placeholder="" minlength="2" maxlength="15" name="username"> <label for="username" class="px-4">Username</label> <small <?php echo (!empty($usernameErr)) ? "class='invalid-feedback'" : "" ?>><?php
                                                                                                                                                                                                                                                                                                                                                                        echo (!empty($usernameErr)) ? "{$usernameErr}" : "One cap letter & only letters and one mandatory digit, 2 to 15 character limit." ?></small> </div>
            </div>
            <div class="row py-3 gx-2">
                <div class="col form-floating"> <input type="text" id="firstName" class="form-control <?php echo (!empty($firstNameErr)) ? 'is-invalid' : ''; ?>" required placeholder="" name="firstName" minlength="2" maxlength="15"> <label for="firstName" class="px-4">First Name</label> <small <?php echo (!empty($firstNameErr)) ? "class='invalid-feedback'" : "" ?>><?php
                                                                                                                                                                                                                                                                                                                                                                                echo (!empty($firstNameErr)) ? "{$firstNameErr}" : "Max of 15 characters." ?></small> </div>
                <div class="col form-floating"> <input type="text" id="lastName" class="form-control <?php echo (!empty($lastNameErr)) ? 'is-invalid' : ''; ?>" required placeholder="" name="lastName" minlength="2" maxlength="25"> <label for="retypePassword" class="px-4">Last Name</label> <small <?php echo (!empty($lastNameErr)) ? "class='invalid-feedback'" : "" ?>><?php
                                                                                                                                                                                                                                                                                                                                                                                echo (!empty($lastNameErr)) ? "{$lastNameErr}" : "Max of 25 characters." ?></small> </div>
            </div>
            <div class="row py-3 gx-2">
                <div class="col form-floating"> <input type="password" id="password" class="form-control <?php echo (!empty($passwordErr)) ? 'is-invalid' : ''; ?>" required placeholder="" name="password"> <label for="password" class="px-4">Enter Password</label> <small <?php echo (!empty($passwordErr)) ? "class='invalid-feedback'" : "" ?>><?php
                                                                                                                                                                                                                                                                                                                                                        echo (!empty($passwordErr)) ? "{$passwordErr}" : "One capital letter, and max 3 digits.  Min. 4 Characters." ?></small> </div>
                <div class="col form-floating"> <input type="password" id="retypePassword" class="form-control <?php echo (!empty($passwordErr)) ? 'is-invalid' : ''; ?>" required placeholder="" name="retypePassword"> <label for="retypePassword" class="px-4">Re-enter Password</label> <small <?php echo (!empty($passwordErr)) ? "class='invalid-feedback'" : "" ?>><?php
                                                                                                                                                                                                                                                                                                                                                                            echo (!empty($passwordErr)) ? "{$passwordErr}" : "" ?></small> </div>
            </div>
            <div class="row py-3">
                <div class="col form-floating"> <input type="email" id="email" class="form-control <?php echo (!empty($emailErr)) ? 'is-invalid' : ''; ?>" required placeholder="" name="email"> <label for="email" class="px-4">E-mail</label> <small <?php echo (!empty($emailErr)) ? "class='invalid-feedback'" : "" ?>><?php
                                                                                                                                                                                                                                                                                                                            echo (!empty($emailErr)) ? "{$emailErr}" : "Ex:
                           example@email.com" ?></small> </div>
            </div>
            <div class="row py-3">
                <div class="col form-floating"> <input type="text" id="address" class="form-control <?php echo (!empty($addressErr)) ? 'is-invalid' : ''; ?>" required placeholder="" name="address"> <label for="address" class="px-4">Address</label> <small <?php echo (!empty($addressErr)) ? "class='invalid-feedback'" : "" ?>><?php
                                                                                                                                                                                                                                                                                                                                        echo (!empty($addressErr)) ? "{$addressErr}" : "Ex:
                           XYY Main Street" ?></small> </div>
            </div>
            <div class="row py-2 text-center button-row">
                <div class="col"> <input type="submit" class="btn btn-primary" name="submit" value="Register" id="submit"> </div>
                <div class="col"> <input type="reset" class="btn btn-secondary" value="Reset"> </div>
            </div>
        </form>
        <div class="login-info">
            <p>Already have an account? <span><a href="login.php" class="link-primary text-decoration-none">Click here
                        to login</a></span>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"> </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"> </script> <?php include('../HTML/footer.html'); ?>Collapsehas context menuComposeParagraph