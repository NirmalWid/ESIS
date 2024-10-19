

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Employee Time Tracker</title>
    <style>
        body {
            background-color: #f8f9fa; /* Light background for contrast */
        }

        header {
            background-color: #343a40; /* Dark background for header */
            padding: 10px 20px; /* Padding for the header */
        }

        nav a {
            color: #ffffff; /* White text color for links */
            margin-right: 15px; /* Space between links */
            text-decoration: none; /* Remove underline */
            font-weight: 500; /* Slightly bolder text */
        }

        nav a:hover {
            color: #ffc107; /* Yellow color on hover */
            text-decoration: underline; /* Underline on hover */
        }

        .navbar-brand {
            font-size: 1.5rem; /* Larger brand name */
            color: #ffffff; /* Brand name color */
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <a class="navbar-brand" href="../index.php">Employee Time Tracker</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../profile/profile.php">Profile</a>
                        </li>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../admin/dashboard.php">Admin Dashboard</a>
                            </li>
                        <?php elseif ($_SESSION['role'] === 'manager'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../manager/approvals.php">Manager Approvals</a>
                            </li>
                        <?php elseif ($_SESSION['role'] === 'employee'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../employee/dashboard.php">Employee Dashboard</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../public/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../public/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../public/signup.php">Sign Up</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Additional Content Goes Here -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
