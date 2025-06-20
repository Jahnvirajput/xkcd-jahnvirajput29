<?php
require 'functions.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = "";
$showCodeInput = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $code = $_POST['verification_code'] ?? '';

    if (!empty($email) && empty($code)) {
        $verificationCode = generateVerificationCode();
        if (!is_dir("codes")) {
            mkdir("codes");
        }
        file_put_contents("codes/" . md5($email) . ".txt", $verificationCode);
        sendVerificationEmail($email, $verificationCode);
        $message = "📩 Verification code sent to <strong>$email</strong>. Please check your inbox or spam folder.";
        $showCodeInput = true;
    } elseif (!empty($email) && !empty($code)) {
        if (verifyCode($email, $code)) {
            unsubscribeEmail($email);
            unlink("codes/" . md5($email) . ".txt");
            $message = "✅ You have been unsubscribed successfully.";
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
    <title>Unsubscribe from XKCD Comics</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fdfdfd;
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
            color: #c62828;
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
            background-color: #c62828;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #b71c1c;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #ffecec;
            border-left: 5px solid #f44336;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Unsubscribe from XKCD</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Email Address:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <?php if ($showCodeInput): ?>
                <label>Enter Verification Code:</label>
                <input type="text" name="verification_code" maxlength="6" placeholder="e.g. 123456" required>
                <button type="submit">Unsubscribe</button>
            <?php else: ?>
                <button type="submit">Send Verification Code</button>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
