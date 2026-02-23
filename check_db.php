<?php
// check_db.php
include 'db.php';

// Kunin ang huling 10 bookings, kahit anong status o petsa
$sql = "SELECT * FROM bookings ORDER BY id DESC LIMIT 10";
$result = $conn->query($sql);

echo "<h1>DIRECT DATABASE CHECKER</h1>";
echo "<a href='admin.php'>Back to Admin</a><br><br>";

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #eee;'>
            <th>ID</th>
            <th>TOKEN</th>
            <th>NAME</th>
            <th>GCASH REF</th>
            <th>STATUS</th>
          </tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td style='color:blue; font-weight:bold;'>" . $row['token'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['gcash_ref'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<h2 style='color:red;'>WALANG LAMAN ANG DATABASE TABLE</h2>";
    echo "Error: " . $conn->error;
}
?>