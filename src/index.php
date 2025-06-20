<?php
require 'functions.php';

$message = "";
$showCodeInput = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $code = $_POST['verification_code'] ?? '';

    if (!empty($email) && empty($code)) {
        $verificationCode = generateVerificationCode();
        if (!is_dir("codes")) mkdir("codes");
        file_put_contents("codes/" . md5($email) . ".txt", $verificationCode);
        sendVerificationEmail($email, $verificationCode);
        $message = "✅ Verification code sent to <strong>$email</strong>. Please check your inbox or spam folder.";
        $showCodeInput = true;
    } elseif (!empty($email) && !empty($code)) {
        if (verifyCode($email, $code)) {
            registerEmail($email);
            unlink("codes/" . md5($email) . ".txt");
            $message = "✅ Email verified and registered successfully!";
        } else {
            $message = "❌ Invalid verification code. Please try again.";
            $showCodeInput = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>XKCD Email Subscription</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            color: #555;
            font-weight: bold;
        }
        input[type="email"],
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color:rgb(235, 9, 212);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #1565c0;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #e3f2fd;
            border-left: 5px solid #2196f3;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>XKCD Email Subscription</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Email Address:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <?php if ($showCodeInput): ?>
                <label>Enter Verification Code:</label>
                <input type="text" name="verification_code" maxlength="6" placeholder="e.g. 123456" required>
                <button type="submit">Verify</button>
            <?php else: ?>
                <button type="submit">Send Verification Code</button>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
