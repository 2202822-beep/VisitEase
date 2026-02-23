<?php
session_start();
include 'db.php'; // Siguraduhin na tama ang connection file mo

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $max_slots = $_POST['max_slots'];

    // Validation: Bawal ang past date
    if ($date < date('Y-m-d')) {
        echo "<script>alert('Error: Cannot add past dates.'); window.location.href='admin_dashboard.php';</script>";
        exit();
    }

    // Validation: Bawal ang End Time na mas maaga sa Start Time
    if ($end_time <= $start_time) {
        echo "<script>alert('Error: End time must be later than start time.'); window.location.href='admin_dashboard.php';</script>";
        exit();
    }

    // Insert sa Database
    $sql = "INSERT INTO schedule_settings (date, start_time, end_time, slots, max_slots) 
            VALUES ('$date', '$start_time', '$end_time', '$max_slots', '$max_slots')";

    if ($conn->query($sql) === TRUE) {
        // Success! Balik sa dashboard
        echo "<script>
            alert('New schedule added successfully!'); 
            window.location.href='admin_dashboard.php';
        </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>