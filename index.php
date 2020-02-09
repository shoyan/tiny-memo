<?php
ini_set('display_errors', "On");
require('database.php');

/**
 * メモを操作するクラス
 */
class Memo
{
  private $id;
  private $title;
  private $message;
  private $createdAt;
  private $updatedAt;

  /**
   * DBの接続情報を保持
   */
  private static $dbh;

  /**
   * DBの接続情報をセットする
   */
  public static function connect($dbh)
  {
    self::$dbh = $dbh;
  }

  function __construct($id, $title, $message, $createdAt, $updatedAt)
  {
    $this->id = $id;
    $this->title = $title;
    $this->message = $message;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
  }

  public function id()
  {
    return $this->id;
  }

  public function title()
  {
    return $this->title;
  }

  public function setTitle($title)
  {
    return $this->title = $title;
  }

  public function message()
  {
    return $this->message;
  }

  public function setMessage($message)
  {
    return $this->message = $message;
  }

  public function createdAt()
  {
    return $this->createdAt;
  }

  public function updatedAt()
  {
    return $this->updatedAt;
  }

  /**
   * 全てのメモを取得する
   * 
   * @return array 全てのメモ
   */
  public static function all()
  {
    $sql = 'SELECT * FROM memo ORDER BY updatedAt DESC';
    $stmt = self::$dbh->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * メモを1件取得する
   * 
   * @param int メモID
   * @return memo メモ情報
   */
  public static function findById($id)
  {
    $sql = 'SELECT * FROM memo WHERE id = ?';
    $stmt = self::$dbh->prepare($sql);
    $data[] = $id;
    $stmt->execute($data);
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    return new Memo($rec['id'], $rec['title'], $rec['message'], $rec['createdAt'], $rec['updatedAt']);
  }

  /**
   * メモを作成する
   * 
   * @param string タイトル
   * @param string 内容
   * @return Memo メモ情報
   */
  public static function create($title, $message)
  {
    $stmt = self::$dbh->prepare("INSERT INTO memo (title,message) VALUES (?,?)");
    $data[] = $title;
    $data[] = $message;
    $stmt->execute($data);
    return Memo::findById(self::$dbh->lastInsertId());
  }

  /**
   * メモを更新する
   * 
   * @return boolean 成功時はtrue
   */
  public function update()
  {
    $sql = 'UPDATE memo SET title=?,message=? WHERE id=?';
    $stmt = self::$dbh->prepare($sql);
    $data[] = $this->title;
    $data[] = $this->message;
    $data[] = $this->id;
    return $stmt->execute($data);
  }

  /**
   * メモを削除する
   * 
   * @return boolean 成功時はtrue
   */
  public function delete()
  {
    $sql = 'DELETE FROM memo WHERE id=?';
    $stmt = self::$dbh->prepare($sql);
    $data[] = $this->id;
    return $stmt->execute($data);
  }
}

// 接続情報を設定する
Memo::connect($dbh);


if (isset($_POST["junction"])) {
  $row = $_POST["junction"];

  switch ($row) {

    case "送信":
      if (empty($_POST['id'])) {
        $memo = Memo::create($_POST['title'], $_POST['message']);
      } else {
        $memo = Memo::findById($_POST['id']);
        $memo->setTitle($_POST['title']);
        $memo->setMessage($_POST['message']);
        $memo->update();
      }
      break;

    case "削除":
      $memo = Memo::findById($_POST['id']);
      $memo->delete();
      // メモ削除後はメモはリダイレクトする
      header('Location: /tiny_memo/');
      exit;
  }
} elseif (!empty($_GET['linkId'])) {
  $memo = Memo::findById($_GET['linkId']);
}

$id = '';
$title = '';
$message = '';
$createdAt = '';
$updatedAt = '';

if ($memo) {
  $id = $memo->id();
  $title = $memo->title();
  $message = $memo->message();
  $createdAt = $memo->createdAt();
  $updatedAt = $memo->updatedAt();
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>メモ帳</title>
  <link href="style.css" rel="stylesheet">
</head>

<body>
  <form method='post' action='/tiny_memo/'>
    <div class="sidebar">
      <p><a href="/tiny_memo">メモ一覧</a><p>
      <br />
      <a href='/tiny_memo/'><input name='back' type='submit' value='新規作成'></a>

      <?PHP foreach (Memo::all() as $row) : ?>
        <a href='/tiny_memo/?linkId=<?PHP print $row["id"] ?>'>
          <div class="border">
            <p><?PHP print $row['title'] ?></p>
            <p class="overflow-ellipsis"><?PHP print $row['message'] ?></p>
            <!--日付けをスラッシュ区切り-->
            <p><?PHP echo date('Y/n/d',  strtotime($row['createdAt'])); ?></p>
            <br />
          </div>
        </a>
      <?PHP endforeach ?>
    </div>

    <div class="main">
      <input type="hidden" id='id' type='text' name='id' class='id' value='<?PHP print $id ?>' placeholder="id">
      <input id='title' type='text' name='title' class='title' value='<?PHP print $title ?>' placeholder="件名">
      <p>作成日：<?PHP print date('Y/n/d', strtotime($createdAt)); ?> 更新日：<?PHP print date('Y/n/d', strtotime($updatedAt)); ?></p>    
      <div><textarea id='message' name='message' class='message'><?PHP print $message ?></textarea></div>
      <div class="bottun">
        <input type='submit' name='junction' value='送信'>
        <input class="red" type="submit" name="junction" value="削除"><br />
      </div>
      <br />
      <br />
    </div>
  </form>
</body>
</html>
