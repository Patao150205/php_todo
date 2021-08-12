<?php
session_start();
session_regenerate_id();
if (!isset($_SESSION['userId'])) {
  header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . 'login.php');
}

require_once("./db.php");
$res = null;
$info  = null;
$editTarget = null;

if ($_SERVER["REQUEST_METHOD"] === "GET") {
  if (isset($_GET['edit_todo_id'])) {
    // 編集
    $stt = $db->prepare('SELECT * FROM todo_list WHERE todo_id = :id AND userId = :userId;');
    $stt->bindValue(':id', $_GET['edit_todo_id']);
    $stt->bindValue(':userId',  $_SESSION['userId']);
    $stt->execute();
    $editTarget = $stt->fetch(PDO::FETCH_ASSOC);
    $editTarget['link'] = true;
  };
  $stt = $db->prepare("SELECT * FROM todo_list  WHERE userId = :userId ORDER BY expected_date;");
  $stt->bindValue(':userId', $_SESSION['userId']);
  $stt->execute();
  $list = $stt;
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if ($_POST['date'] === '' || $_POST['content'] === '') {
    $stt = $db->prepare("SELECT * FROM todo_list WHERE userId = :userId  ORDER BY expected_date;");
    $stt->bindValue(':userId', $_SESSION['userId']);
    $stt->execute();
    $list = $stt;
    $info = "正しい値を入力してください。";
    $editTarget['expected_date'] = $_POST['date'];
    $editTarget['content'] = $_POST['content'];
  } else if (isset($_GET['edit_todo_id'])) {
    echo $_GET['edit_todo_id'];
    $stt = $db->prepare("UPDATE todo_list SET  content= :content, expected_date= :date WHERE todo_id = :id AND userId = :userId;");
    $stt->bindValue(':content', $_POST['content']);
    $stt->bindValue(':date', $_POST['date']);
    $stt->bindValue(':id', $_GET['edit_todo_id']);
    $stt->bindValue(':userId', $_SESSION['userId']);
    $stt->execute();
    header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . 'index.php');
  } else {
    $stt = $db->prepare("INSERT INTO todo_list VALUES ('Primary key', :content, :date, :userId)");
    $stt->bindValue(':content', $_POST['content']);
    $stt->bindValue(':date', $_POST['date']);
    $stt->bindValue(':userId', $_SESSION['userId']);
    $stt->execute();
    $stt = $db->prepare("SELECT * FROM todo_list  WHERE userId = :userId ORDER BY expected_date;");
    $stt->bindValue(':userId', $_SESSION['userId']);
    $stt->execute();
    $list = $stt;
    header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . 'index.php');
  }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/index.css">
  <title>PHP_TODO</title>
</head>

<body>
  <div class="root">
    <div class="container">
      <h1>TODOリスト</h1>
      <a href="logout.php">ログアウト</a>
      <div class="module-spacer--sm"></div>
      <form action="" method="POST">
        <?php if (isset($info)) { ?>
          <p style="color: red"><?php echo $info; ?></p>
        <?php } ?>
        <label>
          日付
          <input name="date" type="date" value="<?php echo $editTarget ? htmlspecialchars($editTarget['expected_date'], ENT_QUOTES | ENT_HTML5) : ''; ?>" />
        </label>
        <label>
          項目
          <input name="content" type="text" value="<?php echo $editTarget ? htmlspecialchars($editTarget['content'], ENT_QUOTES | ENT_HTML5) : ''; ?>" />
        </label>
        <input type="submit" value="登録" />
      </form>
      <?php isset($editTarget['link']) && print '<a href="/">登録画面に移動する。</a>'; ?>
      <div class="module-spacer--sm"></div>
      <table class="table" border="5" bordercolor="orange">
        <tr>
          <th>日付</th>
          <th>項目</th>
          <th>削除</th>
          <th>編集</th>
        </tr>
        <?php
        while ($data = $list->fetch(PDO::FETCH_ASSOC)) :
        ?>
          <tr>
            <td>
              <?php echo htmlspecialchars($data["expected_date"],  ENT_QUOTES | ENT_HTML5) ?>
            </td>
            <td>
              <?php echo htmlspecialchars($data["content"], ENT_QUOTES | ENT_HTML5)  ?>
            </td>
            <form>
              <td align="center">
                <button type="submit" name="deletebtn" formmethod="POST" formaction="./delete.php" value="<?php echo $data['todo_id']; ?>">削除</button>
              </td>
              <td align="center">
                <button type="submit" name="edit_todo_id" formmethod="GET" value="<?php echo $data['todo_id']; ?>" formaction="./index.php">編集</button>
            </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>
</body>

</html>