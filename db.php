<?php

$dsn = "mysql:dbname=todo;host=localhost;charset=utf8";
$user = "root";
$passwd = "root";

try {
  $db = new PDO($dsn, $user, $passwd);
  print "接続に成功しました。";
} catch (PDOException $e) {
  die("接続エラー: {$e->getMessage()}");
}
