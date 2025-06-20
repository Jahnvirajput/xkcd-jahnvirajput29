<?php
require_once 'functions.php';


file_put_contents("cron_log.txt", date("Y-m-d H:i:s") . " - cron.php started\n", FILE_APPEND);


$comic = fetchAndFormatXKCDData();


$file = __DIR__ . '/registered_emails.txt';


if (!file_exists($file)) {
    file_put_contents("cron_log.txt", "No registered_emails.txt found\n", FILE_APPEND);
    exit;
}

$emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$subject = "Your XKCD Comic of the Day";


$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: no-reply@example.com\r\n";

foreach ($emails as $email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sent = mail($email, $subject, $comic, $headers);
        $status = $sent ? "✅ Sent" : "❌ Failed";
        file_put_contents("cron_log.txt", "$status to $email\n", FILE_APPEND);
    } else {
        file_put_contents("cron_log.txt", "⚠️ Invalid email: $email\n", FILE_APPEND);
    }
}


file_put_contents("cron_log.txt", date("Y-m-d H:i:s") . " - cron.php ended\n", FILE_APPEND);
?>
