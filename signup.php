<?php
session_start();
require_once('./db.php');
$info = [];
if ($_SERVER['REQUEST_METHOD']  === "POST") {
	// バリデーション
	if ($_POST['username'] === '') {
		$info['username'] = 'ユーザー名を入力してください。';
	}
	define('REG_EMIAL', '/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/');
	if (preg_match(REG_EMIAL, $_POST['email']) === 0) {
		$info['email'] = "正しくメールアドレスを入力してください。";
	}
	if (mb_strlen($_POST['password']) < 6) {
		$info['password'] = 'パスワードは、６文字以上で入力してください。';
	}

	if (!$info) {
		$stt = $db->prepare('SELECT e_mail FROM USERS WHERE e_mail = :email ');
		$stt->bindValue(':email', $_POST['email']);
		$stt->execute();
		$data = $stt->fetch(PDO::FETCH_ASSOC);
		if (isset($data['e_mail'])) {
			$info['existUser'] = 'ユーザが既に登録済みです。';
		} else {
			$stt =	$db->prepare("INSERT INTO USERS VALUES ('USERID', :username, :email, :password)");
			$stt->bindValue(':username', $_POST['username']);
			$stt->bindValue(':email', $_POST['email']);
			$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
			$stt->bindValue(':password', $password);
			$stt->execute();
			$userId = $db->lastInsertId();
			echo $userId;
			$_SESSION['userId'] = $userId;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . 'index.php');
		}
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
	<title>サインアップ</title>
</head>

<body>
	<h1>サインアップ</h1>
	<form action="" method="POST">
		<?php if (isset($info['existUser'])) : ?>
			<p style="color: red;"><?php echo htmlspecialchars($info['existUser'], ENT_QUOTES | ENT_HTML5) ?></p>
		<?php endif; ?>
		<label>
			ユーザー名:
			<input name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES | ENT_HTML5) : '' ?>" type="text">
		</label>
		<?php if (isset($info['username'])) : ?>
			<p style="color: red;"><?php htmlspecialchars($info['username'], ENT_QUOTES | ENT_HTML5) ?></p>
		<?php endif; ?>
		<label>
			メールアドレス:
			<input name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES | ENT_HTML5) : '' ?>" type="email">
		</label>
		<?php if (isset($info['email'])) : ?>
			<p style="color: red;"><?php echo htmlspecialchars($info['email'], ENT_QUOTES | ENT_HTML5) ?></p>
		<?php endif; ?>
		<label>
			パスワード:
			<input name="password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password'], ENT_QUOTES | ENT_HTML5) : '' ?>" type="password">
		</label>
		<?php if (isset($info['password'])) : ?>
			<p style="color: red;"><?php echo htmlspecialchars($info['password'], ENT_QUOTES | ENT_HTML5) ?></p>
		<?php endif; ?>
		<label>
			<input type="submit" value="登録する">
		</label>
	</form>
	<a href="login.php">ログイン</a>
</body>

</html>