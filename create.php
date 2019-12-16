<?php 
define('DB_HOST', 'localhost');
define('DB_NAME', 'memopad');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_PORT', '43306');

// 文字化け対策
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8'");

// PHPのエラーを表示するように設定
error_reporting(E_ALL & ~E_NOTICE);

// データベースの接続
try {
     $dbh = new PDO('mysql:host='.DB_HOST.';port=' . DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASSWORD, $options);
     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
     echo $e->getMessage();
     exit;
}

?>


<html>
<head>
  <meta charset="utf-8">
  <title>メモ帳</title>
  <link href="style.css" rel="stylesheet">
</head>

<body>
<?php
if (isset($_POST["junction"])) {
    $row = $_POST["junction"];

    switch ($row) {
        case "更新": 
          if (empty($_POST['id'])) {
            print 'idを入力してください!';
          } else {
          $id = $_POST['id'];
          $title = $_POST['title'];
          $message = $_POST['message'];
          
          $sql = 'UPDATE memo SET title=?,message=? WHERE id=?';
          $stmt = $dbh->prepare($sql);
          $data[] = $title;
          $data[] = $message;
          $data[] = $id;
          $stmt->execute($data);
          }
        break;

        case "送信":
          $title = $_POST['title'];
          $message = $_POST['message'];
    
          $stmt = $dbh->prepare("INSERT INTO memo (title,message) VALUES (?,?)");
          $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR);
          $stmt->bindParam(':title', $_POST['title'], PDO::PARAM_STR);
          $stmt->bindParam(':message', $_POST['message'], PDO::PARAM_STR);
          $data[] = $title;
          $data[] = $message;
    
          $stmt->execute($data);
        break;

        case "削除": 
          $id = $_POST['id'];
          $sql = 'DELETE FROM memo WHERE id=?';
          $stmt = $dbh->prepare($sql);
          $data[] = $id;
          $stmt->execute($data);
        break;
    }
}
?>

<form method='post' action='/memopad/create.php'>
        
        <div class="sidebar">
          <p>メモ一覧<p>
          <br/>
          <a href='http://localhost/memopad/create.php'><input name='back' type='submit' value='新規作成'></a>
          <?php
            $sql = 'SELECT id,title,message FROM memo WHERE 1';
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
          ?>
            
    
          <?PHP  while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
              <p class='dot'>id:</p>
              <a href='http://localhost/memopad/create.php?linkId=<?PHP print $row["id"] ?>'><input id='linkId' class='linkId' type='button' name='linkId' value='<?PHP print $row["id"] ?>'></a>
              <br>
              <p><?PHP print $row['title'] ?></p>
              <p class="overflow-ellipsis"><?PHP print $row['message'] ?></p>
              <br/>
          <?PHP endwhile ?>
          
          <?php
            if (!empty($_GET['linkId'])) {

            $id = $_GET['linkId'];
      
            $sql = 'SELECT title,message FROM memo WHERE id=?';
            $stmt = $dbh->prepare($sql);
            $data[] = $id;
            $stmt->execute($data);

            $rec=$stmt->fetch(PDO::FETCH_ASSOC);
            $title=$rec['title'];
            $message=$rec['message'];
            }

          ?>

        </div>

      <div class="main">
        <input id='id' type='text' name='id' class='id' value='<?PHP print $id ?>' placeholder="id">
        <input id='title' type='text' name='title' class='title' value='<?PHP print $title?>' placeholder="件名">
        <div><textarea id='message' name='message' class='message'><?PHP print $message ?></textarea></div>
        <div class= "bottun">
          <input type='submit' name='junction' value='送信'>
          <input type="submit" name="junction" value="更新">
          <input class="red" type="submit" name="junction" value="削除"><br/>
        </div>
        <br/>
        <br/>
      </div>
    </form>




    <?php  $dbh = null; ?>



</body>
</html>