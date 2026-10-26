<?php
session_start();

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = '';

$checkGender = pg_query($conn, "SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'gender'");
if ($checkGender === false || pg_num_rows($checkGender) === 0) {
    $alterGender = pg_query($conn, "ALTER TABLE users ADD COLUMN gender VARCHAR(20)");
    if ($alterGender === false) {
        $errors[] = 'Unable to add the gender column.';
    }
}

$checkPhone = pg_query($conn, "SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'phone'");
if ($checkPhone === false || pg_num_rows($checkPhone) === 0) {
    $alterPhone = pg_query($conn, "ALTER TABLE users ADD COLUMN phone VARCHAR(20)");
    if ($alterPhone === false) {
        $errors[] = 'Unable to add the phone column.';
    }
}

$currentUserResult = pg_query_params($conn, 'SELECT gender, phone_number FROM users WHERE id = $1', [$userId]);
$currentUser = ($currentUserResult !== false) ? pg_fetch_assoc($currentUserResult) : null;

$gender = $currentUser['gender'] ?? '';
$phone = $currentUser['phone'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gender = trim($_POST['gender'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($gender === '') {
        $errors[] = 'Gender is required.';
    }

    if ($phone === '') {
        $errors[] = 'Phone number is required.';
    } elseif (!preg_match('/^[0-9+()\-\s]+$/', $phone)) {
        $errors[] = 'Phone number contains invalid characters.';
    }

    if (empty($errors)) {
        pg_query_params(
            $conn,
            'UPDATE users SET gender = $1, phone_number = $2 WHERE id = $3',
            [$gender, $phone, $userId]
        );

        $success = 'Profile updated successfully.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
</head>
<body>
    <h2>Edit Profile</h2>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <p><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="gender">Gender:</label>
        <select name="gender" id="gender">
            <option value="">Select...</option>
            <option value="Male" <?php echo ($gender === 'Male') ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo ($gender === 'Female') ? 'selected' : ''; ?>>Female</option>
        </select>
        <br><br>

        <label for="phone">Phone Number:</label>
        <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>">
        <br><br>

        <button type="submit">Save Profile</button>
    </form>

    <p><a href="loggedin.php">Back to Home</a></p>
</body>
</html>
