<?php
function check_login() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../public/login.php");
        exit();
    }
}
