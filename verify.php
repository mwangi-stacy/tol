<?php
require 'conf.php';

if (isset($_GET['email'], $_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    try {
        $pdo = new PDO(
            "mysql:host={$conf['db_host']};dbname={$conf['db_name']};charset=utf8",
            $conf['db_user'],
            $conf['db_pass']
        );
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND verification_token = ?");
        $stmt->execute([$email, $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Mark as verified
            $update = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE email = ?");
            $update->execute([$email]);
            echo "Your account has been verified. You can now <a href='signin.php'>sign in</a>.";
        } else {
            echo "Invalid verification link.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}