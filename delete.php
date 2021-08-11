<?php
require_once('./db.php');
$stt = $db->prepare('DELETE FROM todo_list WHERE todo_id = :id');
$stt->bindValue(':id', $_POST['deletebtn']);
$stt->execute();
header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . 'index.php');
