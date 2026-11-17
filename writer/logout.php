<?php
require_once __DIR__ . '/includes/writer_auth.php';

logout();
header('Location: login.php');
exit;