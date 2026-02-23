<?php
function sendSystemEmail($to_email, $subject, $body) {
    // DITO DAPAT NAKALAGAY ANG API KEY MO:
    $api_key = 're_NzPfQAE4_FyfVPvipiNwxCB2NLLTAAf9f'; 
    
    // ITO ANG URL NG RESEND (Huwag babaguhin)
    $ch = curl_init('https://api.resend.com/emails');
    
    $data = [
        'from' => 'onboarding@resend.dev', 
        'to' => $to_email,                 
        'subject' => $subject,
        'html' => $body
    ];

    $payload = json_encode($data);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    
    if ($err) {
        error_log("Resend Email Error: " . $err);
        return false; 
    } else {
        return true; 
    }
}
?>