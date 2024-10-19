<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../public/login.php");
    exit();
}

require '../config/database.php';

$query = "SELECT time_logs.*, users.username FROM time_logs 
          JOIN users ON time_logs.user_id = users.id 
          WHERE time_logs.status = 'pending'";
$stmt = $conn->query($query);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $log_id = $_POST['log_id'];
    $status = $_POST['status'];
    
    $query = "UPDATE time_logs SET status = :status WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $log_id);
    $stmt->execute();
    
    header("Location: approvals.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager Approvals</title>
</head>
<body>
    <h1>Approve Timesheets</h1>
    <table>
        <tr>
            <th>User</th>
            <th>Date</th>
            <th>Hours Worked</th>
            <th>Action</th>
        </tr>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td><?php echo $log['username']; ?></td>
            <td><?php echo $log['date']; ?></td>
            <td><?php echo $log['hours_worked']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="log_id" value="<?php echo $log['id']; ?>">
                    <button name="status" value="approved">Approve</button>
                    <button name="status" value="rejected">Reject</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
