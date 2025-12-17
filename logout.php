<?php
require 'config/database.php';

session_destroy();

header('Location: ' . BASE_URL . '/login.php');
exit();
?>