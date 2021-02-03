<?php

// ダイレクトアクセス禁止
if(!isset($_GET['id'])){
  header('Location:./top.php');
}

// // プレミア会員登録
if(isset($_POST['sub_entry'])){
  $link = mysqli_connect('localhost','root','','ih22');
  mysqli_set_charset($link,'utf8');
  $sql = "SELECT member_id,delete_date FROM subscription WHERE member_id = " . $_POST['sub_entry'];
  $result = mysqli_query($link,$sql);
  $row = mysqli_fetch_assoc($result);
  mysqli_close($link);

  if($row['member_id'] == NULL){
    $date = date('Y/m/d');
    $date = explode('/',$date);
    $updata_date = $date[0].$date[1].$date[2];
    $updata_date = intval($updata_date);
    //var_dump($updata_date);
    $link = mysqli_connect('localhost','root','','ih22');
    mysqli_set_charset($link,'utf8');
    $sql = "INSERT INTO subscription(member_id,sb_flug,regist_date)
    VALUES (" . $_POST['sub_entry'] . ",0," . $updata_date . ")" ;
    $result = mysqli_query($link,$sql);
    mysqli_close($link);
    echo 'プレミア会員の登録処理が完了しました。';
  }elseif($row['delete_date'] !== NULL){
    $date = date('Y/m/d');
    $date = explode('/',$date);
    $updata_date = $date[0].$date[1].$date[2];
    $updata_date = intval($updata_date);
    $link = mysqli_connect('localhost','root','','ih22');
    mysqli_set_charset($link,'utf8');
    $sql = "UPDATE subscription SET regist_date = ". $updata_date ." , delete_date = NULL WHERE member_id = " . $_POST['sub_entry'] ;
    $result = mysqli_query($link,$sql);
    mysqli_close($link);
    echo 'プレミア会員の登録処理が完了しました。';
  }else{
    echo 'プレミア会員登録は既に完了しています。';
  }

}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>プレミア会員登録確認</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/conf_premium.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <!-- <header>
    <p><img></p>
  </header> -->

  <main>
    <div class="mainbox">
      <div class="main">
        <p class="text-center">本当にプレミア会員登録しますか？</p>

        <div class="text-center">
          <form action="" method="POST">
             <button class="btn btn-primary" type="submit" name="sub_entry" value=<?php echo $_GET['id'] ?>>登録</button>
          </form>
          <a href="./top.php">TOPページへ</a>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
