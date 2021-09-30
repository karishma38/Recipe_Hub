<?php
$pdo = new PDO('mysql:host=HOST_NAME;dbname=DATABASE_NAME','USERNAME', 'PASSWORD');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>
