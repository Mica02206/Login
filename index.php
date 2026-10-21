<?php

include('db.php');

$errors = [];



if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($username)) {
        $errors[] = "Username is required";
    } else if (strlen($username) < 8) {
        $errors[] = "Username must be 8 characters or more";
    }

    $usernamePattern = '/^(?=.*[\S])(?=.*[\s]).+$/';

    if (preg_match($usernamePattern, $username)) {
        $errors[] = "Username must not have white space";
    }

    // " ynzon salas "

    $result = pg_query_params($conn, 'SELECT username FROM users WHERE username = $1', [$username]);

    if (pg_fetch_assoc($result)) {
        $errors[] = "Username already exist";
    }

    if ($password != $confirmPassword) {
        $errors[] = "Password did not match";
    }

    if (empty($password) || empty($confirmPassword)) {
        $errors[] = "Please provide a passsword";
    }

    $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\W_]).+$/';

    if (!preg_match($pattern, $password)) {
        $errors[] = "password must contain lower, upper case and special characters";
    }

    if (strlen($password) < 8) {
        $errors[] = "password must be 8 or more characters long";
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    if (empty($errors)) {
        pg_query_params(
            $conn,
            'INSERT INTO users (username, password) VALUES ($1, $2)',
            [$username, $passwordHash]
        );

        echo "Registered Successfully";
    }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?php echo (isset($_POST['username'])) ? $_POST['username'] : ""; ?>">
        <br><br>
        <label for="password">Password:[REDACTED]
        <input type="password" name="password">
        <br><br>
        <label for="confirm password">Confirm Password:[REDACTED]
        <input type="password" name="confirm_password">
        <br><br>
        <button type="submit">Register</button>
    </form>
    <br><br>
    <a href="login.php">Login</a>
    <?php

    foreach ($errors as $error) {
        echo $error . "<br>";
    }

    ?>
</body>

</html>