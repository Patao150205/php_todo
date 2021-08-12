<?php
session_start();
require_once("db.php");
$info = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$stt =	$db->prepare("SELECT * FROM USERS WHERE e_mail = :email");
	$stt->bindValue(':email', $_POST['email']);
	$stt->execute();
	$user = $stt->fetch();

	if (isset($user['e_mail']) && password_verify($_POST['password'], $user['password'])) {
		$_SESSION['userId'] = $user['userId'];
		header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . 'index.php');
	} else {
		$info['wrong_authentication_info'] = 'メールアドレスまたは、パスワードが正しくありません。';
	}
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="styles/auth.css">
	<title>ログイン</title>
</head>

<body>
	<h1>ログイン</h1>
	<form action="" method="POST">
		<?php if (isset($info['wrong_authentication_info'])) { ?>
			<p style="color: red"><?php echo $info['wrong_authentication_info']; ?></p>
		<?php } ?>
		<label>
			メールアドレス:
			<input name="email" type="email">
		</label>
		<label>
			パスワード:
			<input name="password" type="password">
		</label>
		<label>
			<input type="submit" value="ログイン">
		</label>
	</form>
	<a href="signup.php">新規登録</a>
</body>

</html>