<?php
session_start();
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $date = $_POST['date'];
        $hours_worked = $_POST['hours_worked'];
        
        $query = "INSERT INTO time_logs (user_id, date, hours_worked, status) 
                  VALUES (:user_id, :date, :hours_worked, 'pending')";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':hours_worked', $hours_worked);
        $stmt->execute();
        
        echo "Time logged successfully!";
    } else {
        echo "You must be logged in!";
    }
}
?>
