<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $name = "User";
    $role = (strtolower($email) == "admin@gmail.com") ? "admin" : "customer";

    if ($con) {
        $res = mysqli_query($con, "SELECT Full_Name FROM users WHERE Email='" . mysqli_real_escape_string($con, $email) . "'");
        if ($res && mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            if (!empty($row['Full_Name'])) {
                $name = $row['Full_Name'];
            }
        }
    }

    echo json_encode([
        'loggedIn' => true,
        'email' => $email,
        'name' => $name,
        'role' => $role
    ]);
} else {
    echo json_encode([
        'loggedIn' => false
    ]);
}
?>
