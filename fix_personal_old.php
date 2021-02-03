<?php
//セッション
session_start();
$_SESSION['member_id'];

//データベースに接続
$link = mysqli_connect(  'localhost'  , 'root' , '' , 'ih22');
mysqli_set_charset($link, 'utf8');

//データベースから会員情報を取得
$data01 = mysqli_query($link,"SELECT * FROM member WHERE id=".$_SESSION['member_id']);
$row01 = mysqli_fetch_array($data01);

//データベースの接続を切断
mysqli_close($link);

$member_name = '';
$user_name = '';
$telephon_num = '';
$postal_num = '';
$address = '';
$phone = '';
$bank = '';
$err = [];

// 編集ボタンが押されたとき
if(isset($_POST['regist'])){

//名前の入力値チェック
  $member_name = $_POST['member_name'];
  if(mb_strlen($_POST['member_name']) > 10){ //文字数チェック
    array_push($err,'10文字以下入力してください');
  }elseif(mb_strlen($_POST['member_name']) == 0){
    array_push($err,'名前が入力されていません');
  }
  if(preg_match("/[a-zA-Z0-9]/",$_POST['member_name'])){ //a~z,A~Z,0~9があるか確認
    array_push($err,'使用できる文字は漢字、ひらがな、カタカナです');
  }

//ユーザー名チェック
  $user_name = $_POST['user_name'];
  if(mb_strlen($_POST['user_name']) > 10){
    array_push($err,'ユーザー名は10文字以下で入力してください');
  }elseif(mb_strlen($_POST['user_name']) == 0){
    array_push($err,'ユーザー名が入力されていません');
  }

  //電話番号チェック
  $telephon_num = $_POST['telephon_num'];
  if(strlen($_POST['telephon_num']) == 0){
    array_push($err,'電話番号が入力されていません');
  }elseif(strlen($_POST['telephon_num']) != 10 && strlen($_POST['telephon_num']) != 11){
    array_push($err, '電話番号の桁数が違います');
  }

//郵便番号チェック
  $postal_num = $_POST['postal_num'];
  if(mb_strlen($_POST['postal_num']) != 7){
    array_push($err,'郵便番号は7字です');
  }elseif(mb_strlen($_POST['postal_num']) == 0){
    array_push($err,'郵便番号が入力されていません');
  }

//住所チェック
  $address = $_POST['address'];
  if(mb_strlen($_POST['address']) == 100){
    array_push($err, '住所は100字以内で入力してください');
  }elseif(mb_strlen($_POST['address']) == 0){
    array_push($err,'住所が入力されていません');
  }


//パスワードチェック
  if(strlen($_POST['pass'])>12 && strlen($_POST['pass']) <6){
    array_push($err, 'パスワードは6文字以上12文字以内です');
  }elseif(mb_strlen($_POST['pass']) == 0){
    array_push($err, 'パスワードが入力されていません');
  }

//確認用パスワードチェック
  if($_POST['check_pass'] != $_POST['pass']){
    array_push($err, '確認用パスワードが間違っています');
  }elseif(strlen($_POST['check_pass']) == 0){
    array_push($err, '確認用パスワードが入力されていません');
  }


//口座番号チェック
  $bank = $_POST['bank_num'];
  if(strlen($_POST['bank_num']) == 0){
    array_push($err, '口座番号が入力されていません');
  }elseif(strlen($_POST['bank_num']) != 7){
    array_push($err, '口座番号の桁が違います');
  }
}
if(isset($_POST['regist']) && !isset($err[0])){
  session_start();
  $_SESSION['member_name'] = $member_name;
  $_SESSION['user_name'] = $user_name;
  $_SESSION['telephon_num'] = $telephon_num;
  $_SESSION['postal_num'] = $postal_num;
  $_SESSION['address'] = $address;
  $_SESSION['pass'] = $_POST['pass'];
  $_SESSION['check_pass'] = $_POST['check_pass'];
  $_SESSION['bank_num'] = $bank;
  $_SESSION['card_num'] = $_POST['card_num'];
  $_SESSION['card_name'] = $_POST['card_name'];
  $_SESSION['card_date'] = $_POST['card_date'];
  header('Location: ./user.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>本人情報編集</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/fix_personal.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <header>
    <p><img src="logo/satou.jpg"></p>
  </header>
    <h2 class="none">本人情報編集</h2>
  <main>
    <div class="mainbox">
      <div class="main">
        <form action="" method="POST">
          <div class="form-group d-flex">
            <label>名前</label>
            <input class="form-control form-control-sm" type="text" name="member_name" value="<?php echo $row01['member_name'];?>">
          </div>

          <div class="form-group d-flex">
            <label>ユーザー名</label>
            <input class="form-control form-control-sm" type="text" name="user_name" value="<?php echo $row01['user_name'];?>">
          </div>

          <div class="form-group d-flex">
            <label>電話番号</label>
            <input class="form-control form-control-sm" type="number" name="telephon_num" value="<?php echo $row01['telephon_num'];?>">
          </div>

          <div class="form-group d-flex">
            <label>郵便番号</label>
            <input class="form-control form-control-sm" type="text" name="postal_num" value="<?php echo $row01['postal_num'];?>">
          </div>

          <div class="form-group d-flex">
            <label>住所</label>
            <input class="form-control form-control-sm" type="text" name="address" value="<?php echo $row01['address'];?>">
          </div>


          <div class="form-group d-flex">
            <label>パスワード</label>
            <input class="form-control form-control-sm" type="text" name="pass">
          </div>

          <div class="form-group d-flex">
            <label>確認用パスワード</label>
            <input class="form-control form-control-sm" type="text" name="check_pass">
          </div>

          <div class="form-group d-flex">
            <label>口座番号</label>
            <input class="form-control form-control-sm" type="text" name="bank_num" value="<?php echo $row01['bank_num'];?>">
          </div>

          <div class="form-group d-flex">
            <label>カード番号</label>
            <input class="form-control form-control-sm" type="text" name="card_num" value="<?php echo $row01['card_num'];?>">
          </div>

          <div class="form-group d-flex">
            <label>名義</label>
            <input class="form-control form-control-sm" type="text" name="card_name" value="<?php echo $row01['card_name'];?>">
          </div>

          <div class="form-group d-flex">
            <label>有効期限</label>
            <input class="form-control form-control-sm" type="text" name="card_date" value="<?php echo $row01['card_date'];?>">
          </div>

          <div class="text-center">
            <a href="user.php"><button class="btn btn-primary" type="submit" name="regist">編集</button></a>
          </div>
        </form>
          <?php foreach($err as $msg): ?>
            <li class="text-danger"><?php echo $msg;?></li>
          <?php endforeach; ?>
      </div>
    </div>
    </main>
  </body>
  </html>
