<?php

$dsn = "mysql:dbname=todo;host=localhost;charset=utf8";
$user = "root";
$dbPasswd = "root";

try {
  $db = new PDO($dsn, $user, $dbPasswd);
  print "接続に成功しました。";
} catch (PDOException $e) {
  die("接続エラー: {$e->getMessage()}");
}
