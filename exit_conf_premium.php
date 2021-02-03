<?php

// ダイレクトアクセス禁止
if(!isset($_GET['id'])){
  header('Location:./top.php');
}

// プレミア会員登録
if(isset($_POST['sub_exit'])){
  $link = mysqli_connect('localhost','root','','IH22');
  mysqli_set_charset($link,'utf8');
  $sql = "SELECT member_id,delete_date FROM subscription WHERE member_id = " . $_POST['sub_exit'];
  $result = mysqli_query($link,$sql);
  $row = mysqli_fetch_assoc($result);
  mysqli_close($link);

  if($row['member_id'] !== NULL && $row['delete_date'] == NULL){
    $date = date('Y/m/d');
    $date = explode('/',$date);
    $updata_date = $date[0].$date[1].$date[2];
    $updata_date = intval($updata_date);
    $link = mysqli_connect('localhost','root','','IH22');
    mysqli_set_charset($link,'utf8');
    $sql = "UPDATE subscription SET delete_date = ". $updata_date ." WHERE member_id = " . $_POST['sub_exit'] ;
    $result = mysqli_query($link,$sql);
    mysqli_close($link);
    echo 'プレミア会員の退会処理が完了しました。';
  }else{
    echo 'プレミア会員退会は既に完了しています。';
  }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>プレミア会員退会確認</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/exit_conf_premium.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <!-- <header>
    <p><img></p>
  </header> -->

  <main>
    <div class="mainbox">
      <div class="main">
        <p class="text-center">本当にプレミア会員退会しますか？</p>

        <div class="text-center">
          <form action="" method="POST">
             <button class="btn btn-danger" type="submit" name="sub_exit" value=<?php echo $_GET['id'] ?>>退会</button>
          </form>
          <a href="./top.php">TOPページへ</a>
        </div>
      </div>
    </div>
  </main>

</body>
</html>
