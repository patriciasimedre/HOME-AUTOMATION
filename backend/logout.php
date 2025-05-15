<?php
session_start();
session_unset();
session_destroy();

// Golește și localStorage printr-un mic script injectat
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="refresh" content="0;url=../frontend/login.html">
  <script>
    localStorage.removeItem('prenume');
  </script>
</head>
<body>
  <p>Delogare...</p>
</body>
</html>