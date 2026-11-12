<?php
require 'config/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $sql = "DELETE FROM users WHERE user_ID = $id";
    mysqli_query($mysqli, $sql);
}

header("Location: index.php");
exit;
?>