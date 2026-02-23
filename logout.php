<?php
session_start();
session_unset();
session_destroy();

// Pagkatapos sirain ang session, balik sa login page
header("Location: login.php");
exit();
?>