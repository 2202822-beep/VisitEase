<?php
session_start();
include 'db.php';

// I-require ang ginawa nating Resend helper (Pinalit sa PHPMailer)
require_once 'email_helper.php';

// 1. SECURITY CHECK
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: book.php");
    exit();
}

// 2. CAPTCHA CHECK
if (!isset($_POST['captcha_input']) || !isset($_SESSION['captcha_answer']) || $_POST['captcha_input'] != $_SESSION['captcha_answer']) {
    echo "<script>
            alert('Mali ang Security Code (Math). Subukan ulit.');
            window.history.back();
          </script>";
    exit();
}

// 3. KUNIN AT LINISIN ANG DATA
$name = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$guests = (int)$_POST['guests'];
$slot_id = isset($_POST['slot_id']) ? (int)$_POST['slot_id'] : 0;
$gcash_name = mysqli_real_escape_string($conn, $_POST['gcash_name']);
$gcash_ref = mysqli_real_escape_string($conn, $_POST['gcash_ref']); 
$special_request = mysqli_real_escape_string($conn, $_POST['special_request']);

// VALIDATION: Slot ID check
if ($slot_id == 0) {
    echo "<script>alert('ERROR: Walang napiling slot!'); window.history.back();</script>";
    exit();
}

// 4. DUPLICATE CHECK (Iwas double booking sa Refresh)
$check_duplicate = "SELECT token FROM bookings WHERE gcash_ref = '$gcash_ref' LIMIT 1";
$dup_result = $conn->query($check_duplicate);
if ($dup_result && $dup_result->num_rows > 0) {
    $row = $dup_result->fetch_assoc();
    header("Location: booking_success.php?ref=" . $row['token']); 
    exit();
}

// 5. GENERATE TOKEN
$token = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

// ==========================================
// 5.5 HANDLE IMAGE UPLOAD (GCash Receipt)
// ==========================================
$receipt_filename = ""; // Default empty

// Check kung may file na in-upload at walang error
if (isset($_FILES['gcash_receipt']) && $_FILES['gcash_receipt']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['gcash_receipt']['tmp_name'];
    $fileName = $_FILES['gcash_receipt']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Allowed na mga file types (Mga picture lang)
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

    if (in_array($fileExtension, $allowedExtensions)) {
        // Gawa ng bagong pangalan para hindi mag-conflict (Token + Timestamp)
        $newFileName = $token . '_' . time() . '.' . $fileExtension;
        
        // Piliin kung saan ise-save ang image (Siguraduhin na exist ang folder na 'uploads/receipts/')
        $uploadFileDir = 'uploads/receipts/';
        
        // Gawa ng folder kung wala pa
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        $dest_path = $uploadFileDir . $newFileName;

        // Ilipat ang file papunta sa folder
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $receipt_filename = $newFileName; // I-save ang pangalan para sa database
        } else {
            echo "<script>alert('ERROR: Hindi ma-save ang picture. Pakisubukan ulit.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('ERROR: Bawal ang file type na iyan. JPG, JPEG, o PNG lamang ang pwede.'); window.history.back();</script>";
        exit();
    }
} else {
    echo "<script>alert('ERROR: Pakilagay ang screenshot ng GCash receipt mo.'); window.history.back();</script>";
    exit();
}
// ==========================================

// 6. SLOT AVAILABILITY CHECK & GET DATE DETAILS
$slot_check = "SELECT slots, date, start_time, end_time FROM schedule_settings WHERE id = '$slot_id' LIMIT 1";
$slot_res = $conn->query($slot_check);

if ($slot_res->num_rows == 0) {
    die("Error: Slot ID $slot_id not found in schedule_settings.");
}

$slot_row = $slot_res->fetch_assoc();

// Check kung puno na
if ($slot_row['slots'] < $guests) {
    echo "<script>alert('Sorry, puno na ang slot na ito. Available: " . $slot_row['slots'] . "'); window.history.back();</script>";
    exit();
}

// Prepare Date and Time variables
$saved_date = $slot_row['date'];
$display_date = date('F d, Y', strtotime($saved_date)); // Para maganda sa email (e.g., October 25, 2026)
$saved_time = date('h:i A', strtotime($slot_row['start_time'])) . ' - ' . date('h:i A', strtotime($slot_row['end_time']));

// 7. SAVE TO DATABASE (Isinama na ang $receipt_filename sa SQL)
$sql = "INSERT INTO bookings (token, name, email, phone, guests, schedule_id, visit_date, visit_time, gcash_name, gcash_ref, gcash_receipt, special_request, status) 
        VALUES ('$token', '$name', '$email', '$phone', '$guests', '$slot_id', '$saved_date', '$saved_time', '$gcash_name', '$gcash_ref', '$receipt_filename', '$special_request', 'Pending')";

if ($conn->query($sql) === TRUE) {
    // UPDATE SLOTS (Bawasan ang available slot)
    $conn->query("UPDATE schedule_settings SET slots = slots - $guests WHERE id = '$slot_id'");
    
    // ==========================================
    // 8. SEND EMAIL VIA RESEND API
    // ==========================================
    if (!empty($email)) { // I-check kung naglagay ng email ang visitor
        $subject = 'VisitEase: Your Booking is Pending Review';
        
        // Magandang HTML Email Design mo (isinave natin sa variable)
        $message = "
        <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;'>
            <div style='background-color: #1a2035; color: #c49a45; padding: 20px; text-align: center;'>
                <h2 style='margin: 0;'>VisitEase.</h2>
                <p style='margin: 5px 0 0; color: #fff;'>Museum Booking System</p>
            </div>
            <div style='padding: 20px; background-color: #fafaf9;'>
                <h3 style='color: #1a2035;'>Hello $name,</h3>
                <p>Salamat sa pag-book sa VisitEase! Natanggap na namin ang iyong request kalakip ang iyong payment proof. Kasalukuyan na itong nire-review ng aming admin.</p>
                
                <div style='background-color: #fff; border-left: 4px solid #c49a45; padding: 15px; margin: 20px 0;'>
                    <p style='margin: 0 0 10px; font-size: 14px; color: #777;'>YOUR BOOKING TOKEN / REFERENCE ID</p>
                    <h1 style='margin: 0; color: #1a2035; letter-spacing: 2px;'>$token</h1>
                </div>

                <h4 style='border-bottom: 1px solid #ddd; padding-bottom: 5px;'>Booking Summary:</h4>
                <ul style='list-style: none; padding: 0;'>
                    <li style='margin-bottom: 8px;'><b>Date:</b> $display_date</li>
                    <li style='margin-bottom: 8px;'><b>Time:</b> $saved_time</li>
                    <li style='margin-bottom: 8px;'><b>Guests:</b> $guests Pax</li>
                    <li style='margin-bottom: 8px;'><b>GCash Ref:</b> $gcash_ref</li>
                </ul>

                <p style='margin-top: 20px; font-size: 14px;'>Makakatanggap ka ng panibagong email kapag <b>APPROVED</b> na ang iyong booking. Paki-save ang iyong Token para sa pag-check ng status o pag-pasok sa museum.</p>
            </div>
            <div style='background-color: #eee; text-align: center; padding: 15px; font-size: 12px; color: #666;'>
                &copy; " . date('Y') . " VisitEase Museum. This is an automated message, please do not reply.
            </div>
        </div>";

        // Tatawagin natin yung function mula sa email_helper.php
        sendSystemEmail($email, $subject, $message);
    }
    // ==========================================

    unset($_SESSION['captcha_answer']);
    header("Location: booking_success.php?ref=" . $token);
    exit();
} else {
    // Error handling
    die("<h2>Database Error</h2>" . $conn->error . "<br>SQL: " . $sql);
}
?>