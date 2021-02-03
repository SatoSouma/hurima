<?php
  function pass($pass,$salt,$cnt){
    $i = 0;
    while($i < $cnt){
        $pass = md5($salt . $pass);
        $i++;
    }
    return $pass;
  }
//セッション
session_start();
$_SESSION['member_id'];

//データベースに接続
$link = mysqli_connect(  'localhost'  , 'root' , '' , 'ih22');
mysqli_set_charset($link, 'utf8');

// プレミア会員であるかどうかを確認
$sql = "SELECT member_id,delete_date FROM subscription WHERE member_id = " . $_SESSION['member_id'] ; //本当はsession
$result = mysqli_query($link,$sql);
$row = mysqli_fetch_assoc($result);
if($row['member_id'] == NULL || $row['delete_date'] !== NULL){ // プレミア会員でない時
  $premiere = "enter_premium.php";
  $msg = 'プレミア会員登録';
}else{ //既にプレミア会員であるとき。
  $premiere = "exit_premium.php";
  $msg = 'プレミア会員退会';
}

//個人情報編集：アップデート文
if(isset($_SESSION['member_name']) || isset($_SESSION['user_name']) || isset($_SESSION['telephon_num']) || isset($_SESSION['postal_num']) || isset($_SESSION['address']) || isset($_SESSION['bank_num']) || isset($_SESSION['card_num']) || isset($_SESSION['card_name']) || isset($_SESSION['card_date']) || isset($_SESSION['pass'])){
  if($_SESSION['card_num'] == ''){
    $card_num = 'NULL';
  }else{
    $card_num = $_SESSION['card_num'];
  }
  if($_SESSION['card_name'] == ''){
    $card_name = 'NULL';
  }else{
    $card_name = $_SESSION['card_name'];
  }
  if($_SESSION['card_date'] == ''){
    $card_date = 'NULL';
  }else{
    $card_date = $_SESSION['card_date'];
  }

  $member_name = $_SESSION['member_name'];
  $user_name = $_SESSION['user_name'];
  $telephon_num = $_SESSION['telephon_num'];
  $postal_num = $_SESSION['postal_num'];
  $address = $_SESSION['address'];
  $pass = $_SESSION['pass'];
  $check_pass = $_SESSION['check_pass'];
  $bank_num = $_SESSION['bank_num'];

  if($card_num == 'NULL' || $card_name == 'NULL' || $card_date == 'NULL'){
    $update1 = "UPDATE member SET member_name = '" .$member_name. "' , user_name = '" .$user_name. "' , telephon_num = '" .$telephon_num. "' , postal_num = '" .$postal_num. "' , address = '" .$address. "' , bank_num = '" .$bank_num. "' , card_num = " .$card_num. " , card_name = " .$card_name. " , card_date = " .$card_date. " WHERE id=".$_SESSION['member_id'];
  }else{
    $update1 = "UPDATE member SET member_name = '" .$member_name. "' , user_name = '" .$user_name. "' , telephon_num = '" .$telephon_num. "' , postal_num = '" .$postal_num. "' , address = '" .$address. "' , bank_num = '" .$bank_num. "' , card_num = '" .$card_num. "' , card_name = '" .$card_name. "' , card_date = '" .$card_date. "' WHERE id=".$_SESSION['member_id'];
  }
  //パスワードのアップデート文 + パスワードのテーブルから情報を取る
  $sql1 = "SELECT * FROM password WHERE pass_member_id=".$_SESSION['member_id'];
  $data03 = mysqli_query($link,$sql1);
  //データベースの会員テーブルから住所を取得する
  $sql2 = "SELECT postal_num FROM member WHERE id=".$_SESSION['member_id'];
  $data04 = mysqli_query($link,$sql2);
  $row03 = mysqli_fetch_array($data03);
  $row04 = mysqli_fetch_array($data04);

  $hash_cnt = $row03['hash_cnt'];
  $salt = $row04['postal_num'];

  //ハッシュ化
  $hash_pass = pass($pass , $salt , $hash_cnt );

  //ハッシュ化したパスワードを登録
  $update2 = "UPDATE password SET pass = '" .$hash_pass."' WHERE pass_member_id=".$_SESSION['member_id'];
  $result1 = mysqli_query($link,$update1);
  $result2 = mysqli_query($link,$update2);

  unset($_SESSION['member_name']);
  unset($_SESSION['user_name']);
  unset($_SESSION['telephon_num']);
  unset($_SESSION['postal_num']);
  unset($_SESSION['address']);
  unset($_SESSION['pass']);
  unset($_SESSION['check_pass']);
  unset($_SESSION['bank_num']);
  unset($_SESSION['card_num']);
  unset($_SESSION['card_name']);
  unset($_SESSION['card_date']);
}

//データベースから　ユーザ名　＋　郵便番号　＋　住所　を取得
$data01 = mysqli_query($link,"SELECT member_name, postal_num, address FROM member WHERE id=".$_SESSION['member_id']);
//データベースからメールアドレスを取得
$data02 = mysqli_query($link,"SELECT mail FROM password WHERE pass_member_id=".$_SESSION['member_id']);
$row01 = mysqli_fetch_array($data01);
$row02 = mysqli_fetch_array($data02);

//データベースの接続を切断
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ユーザーページ</title>
<meta content="タイトル" name="title">
</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/user.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <header>
    <p><img class="logo_img" src="./logo/satou.jpg"></p>
  </header>
  <main class="container">
    <div class="main">
      <table class="table table-bordered">
        <tr class="d-flex"><th class="text-center col-3">ユーザー名</th><td class="col-9"><?php echo $row01['member_name'];?></td></tr>
        <tr class="d-flex"><th class="text-center col-3">メールアドレス</th><td class="col-9"><?php echo $row02['mail'];?></td></tr>
        <tr class="d-flex"><th class="text-center col-3">郵便番号</th><td class="col-9"><?php echo $row01['postal_num'];?></td></tr>
        <tr class="d-flex"><th class="text-center col-3">住所</th><td class="col-9"><?php echo $row01['address'];?></td></tr>
      </table>

      <a href="fix_personal.php"><button class="btn btn-secondary">個人情報修正</button></a>
      <a href="listing_history.php"><button class="btn btn-secondary">出品履歴</button></a>
      <a href="purchase_history.php"><button class="btn btn-secondary">購入履歴</button></a>
      <a href='<?php echo $premiere ?>'><button class="btn btn-secondary"><?php echo $msg ?></button></a>
      <a href="money.php"><button class="btn btn-secondary">各種料金</button></a>
      <br>
      <a href="./top.php"><button class="btn btn-secondary back">戻る</button></a>
    </div>
  </main>
</body>
</html>
