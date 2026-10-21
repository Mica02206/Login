<?php

$errors =[];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$username = $_POST['username'];
$password = $_POST['password'];

if(strlen($username) <= 8){
$errors[] = "Username must be at least 8 characters long.";
}

$sql = "";

$pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\W_].{8,})$/';

// Check if the password matches the pattern
if($password != $password) {
$errors[] = "Passwords do not match.";
}
if (!preg_match($pattern, $password)) {
$errors[] = "Password must be at least 8 characters long,
contain at least one uppercase letter, one lowercase letter,
and one special character.";
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
<h3> Login System</h3>
<form method="POST">
<label for="username">Username:
<input type="text" name="username" id="username">
</label>
<br><br>
<label for="password">Password:
<input type="password" name="password" id="password">
</label>
<br><br>
<input type="submit" value="Login">
</form>
</body>
</html>