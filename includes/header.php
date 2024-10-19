<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Employee Time Tracker</title>
</head>
<body>
    <header>
        <nav>
            <a href="../index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../profile/profile.php">Profile</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="../admin/dashboard.php">Admin Dashboard</a>
                <?php elseif ($_SESSION['role'] === 'manager'): ?>
                    <a href="../manager/approvals.php">Manager Approvals</a>
                <?php elseif ($_SESSION['role'] === 'employee'): ?>
                    <a href="../employee/dashboard.php">Employee Dashboard</a>
                <?php endif; ?>
                <a href="../public/logout.php">Logout</a>
            <?php else: ?>
                <a href="../public/login.php">Login</a>
                <a href="../public/signup.php">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>
