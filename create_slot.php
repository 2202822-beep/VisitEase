<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Kunin ang data galing sa bagong form
    $date = $_POST['slot_date'];
    $start_raw = $_POST['slot_start_time'];
    $end_raw = $_POST['slot_end_time'];
    $capacity = $_POST['slot_capacity'];

    // 2. Validation: Check kung may laman lahat
    if (empty($date) || empty($start_raw) || empty($end_raw) || empty($capacity)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: admin.php");
        exit();
    }

    // 3. Validation: Check kung mas maaga ang End Time sa Start Time (Bawal yun)
    if ($end_raw <= $start_raw) {
        $_SESSION['error'] = "End time must be later than start time.";
        header("Location: admin.php");
        exit();
    }

    // Format times to H:i:s (24-hour format for database)
    $start_time = date("H:i:s", strtotime($start_raw));
    $end_time = date("H:i:s", strtotime($end_raw));

    // 4. I-save sa 'schedule_settings' table
    $sql = "INSERT INTO schedule_settings (date, start_time, end_time, slots, max_slots) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // "sssii" -> String, String, String, Int, Int
        $stmt->bind_param("sssii", $date, $start_time, $end_time, $capacity, $capacity);

        if ($stmt->execute()) {
            $_SESSION['success'] = "New schedule slot ($start_raw - $end_raw) created successfully!";
        } else {
            $_SESSION['error'] = "Error saving slot: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: " . $conn->error;
    }

    // Balik sa Admin
    header("Location: admin.php");
    exit();

} else {
    header("Location: admin.php");
    exit();
}
?>