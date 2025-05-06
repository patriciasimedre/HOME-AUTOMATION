<?php
session_start();
session_unset();
session_destroy();

// adăugăm mesaj în URL
header("Location: login.html?status=Ai+fost+delogat+cu+succes!", true, 302);
exit;
?>