<?php

function generateVerificationCode() {
    return rand(100000, 999999);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $email = trim($email);

    $emails = [];
    if (file_exists($file)) {
        $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $emails = array_map('trim', $emails); 
    }

    if (!in_array($email, $emails)) {
        $emails[] = $email;
        file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $email = trim($email);
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_map('trim', $emails);
    $emails = array_filter($emails, fn($e) => $e !== $email);

    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
}

function sendVerificationEmail($email, $code) {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    mail($email, $subject, $message, $headers);
}

function verifyCode($email, $code) {
    $file = __DIR__ . "/codes/" . md5($email) . ".txt";
    return file_exists($file) && trim(file_get_contents($file)) === trim($code);
}

function fetchAndFormatXKCDData() {
    $random = rand(1, 2800);
    $url = "https://xkcd.com/$random/info.0.json";
    $json = @file_get_contents($url);

    if (!$json) {
        return "<p>❌ Failed to fetch XKCD comic. Try again later.</p>";
    }

    $data = json_decode($json, true);

    return "<h2>XKCD Comic</h2>
            <img src='{$data['img']}' alt='XKCD Comic'>
            <p><a href='http://localhost/xkcd-jahnvirajput29/src/unsubscribe.php' id='unsubscribe-button'>Unsubscribe</a></p>";
}


function sendXKCDUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_unique(array_map('trim', $emails));
    $comic = fetchAndFormatXKCDData();

    $subject = "Your XKCD Comic of the Day";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    foreach ($emails as $email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            mail($email, $subject, $comic, $headers);
        }
    }
}
?>
