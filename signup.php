<?php
require 'ClassAutoLoad.php';
$name_value = '';
$email_value = '';
$password_value = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
   $name_value = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
   $email_value = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
   $password_value = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate verification token
        $token = bin2hex(random_bytes(32));

        // Store user in database
        try {
            $pdo = new PDO(
                "mysql:host={$conf['db_host']};dbname={$conf['db_name']};charset=utf8",
                $conf['db_user'],
                $conf['db_pass']
            );
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, verification_token) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password, $token]);
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }

        if (!isset($error)) {
            // Prepare verification link
            $verify_link = $conf['site_url'] . "verify.php?email=" . urlencode($email) . "&token=" . $token;

            // Prepare email content
            $mailcontent = [
                'name_from' => $conf['site_name'],
                'mail_from' => $conf['smtp_user'],
                'name_to' => $name,
                'mail_to' => $email,
                'subject' => 'Welcome to ICS 2.2! Account Verification',
                'body' => "
                    Hello {$name},<br><br>
                    You requested an account on ICS 2.2.<br><br>
                    In order to use this account you need to <a href='{$verify_link}'>Click Here</a> to complete the registration process.<br><br>
                    Regards,<br>
                    Systems Admin<br>
                    ICS 2.2
                "
            ];

            $ObjMailSender->send_Mail($conf, $mailcontent);
            $success = "Registration successful! Please check your email for verification.";
        }
    }
}

$ObjLayout->header($conf);
$ObjLayout->navbar($conf);
$ObjLayout->form_content($conf, $ObjForm, $name_value, $email_value, $password_value);
if (isset($error)) {
    echo "<div class='alert alert-danger'>{$error}</div>";
}
if (isset($success)) {
    echo "<div class='alert alert-success'>{$success}</div>";
}
$ObjLayout->footer($conf);
if (isset($success)) : ?>
<script>
    alert("<?php echo addslashes($success); ?>");
</script>
<?php endif; ?>