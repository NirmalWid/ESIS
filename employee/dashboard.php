<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../public/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard</title>
</head>
<body>
    <h1>Welcome, Employee!</h1>
    <!-- Time tracking form -->
    <form method="POST" action="../api/time.php">
        <input type="date" name="date" required>
        <input type="number" name="hours_worked" placeholder="Hours Worked" required>
        <button type="submit">Submit Time</button>
    </form>
</body>
</html>
