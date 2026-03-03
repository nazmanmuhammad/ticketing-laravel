<?php
$content = file_get_contents(__DIR__ . '/.env');

// Fix all MAIL_ lines
$lines = explode("\n", $content);
$result = [];
foreach ($lines as $line) {
    $trimmed = trim($line);
    if (str_starts_with($trimmed, 'MAIL_MAILER=')) {
        $result[] = 'MAIL_MAILER=smtp';
    } elseif (str_starts_with($trimmed, 'MAIL_HOST=')) {
        $result[] = 'MAIL_HOST=sandbox.smtp.mailtrap.io';
    } elseif (str_starts_with($trimmed, 'MAIL_PORT=')) {
        $result[] = 'MAIL_PORT=2525';
    } elseif (str_starts_with($trimmed, 'MAIL_USERNAME=')) {
        $result[] = 'MAIL_USERNAME=adf6ad316558a3';
    } elseif (str_starts_with($trimmed, 'MAIL_PASSWORD=')) {
        $result[] = 'MAIL_PASSWORD=a42e70d5e57e42';
    } elseif (str_starts_with($trimmed, 'MAIL_FROM_ADDRESS=')) {
        $result[] = 'MAIL_FROM_ADDRESS=noreply@helpdesk.com';
    } elseif (str_starts_with($trimmed, 'MAIL_FROM_NAME=')) {
        $result[] = 'MAIL_FROM_NAME=Helpdesk';
    } else {
        $result[] = $line;
    }
}

file_put_contents(__DIR__ . '/.env', implode("\n", $result));
echo "Done!\n";
