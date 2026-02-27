<?php
// email_helper.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function sendSystemEmail($toEmail, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // --- SERVER SETTINGS ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'visitease000@gmail.com'; // <--- PALITAN MO ITO NG GMAIL MO
        $mail->Password   = 'ykqzovruzyilpdbq';      // Naka-set na ang App Password mo dito
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // --- RECIPIENTS ---
        $mail->setFrom('iyong-email@gmail.com', 'VisitEase Museum'); // <--- PALITAN MO DIN ITO NG GMAIL MO
        $mail->addAddress($toEmail);

        // --- CONTENT ---
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Pwedeng mag-log ng error dito kung kailangan
        return false;
    }
}
?>