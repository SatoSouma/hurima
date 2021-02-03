<?php

$client_name = '';
$user_name = '';
$year = '';
$month = '';
$day = '';
$gender = '';
$gender2 = '';
$address = '';
$phone = '';
$bank = '';
$err = [];

// 登録ボタンが押されたとき
if(isset($_POST['regist'])){

  //db接続
  $link = mysqli_connect('localhost', 'root', '', 'ih22');
  mysqli_set_charset($link, 'utf8');

//名前の入力値チェック
  $client_name = $_POST['client_name'];
  if(mb_strlen($_POST['client_name']) > 10){ //文字数チェック
    array_push($err,'10文字以下入力してください');
  }elseif(mb_strlen($_POST['client_name']) == 0){
    array_push($err,'名前が入力されていません');
  }
  if(preg_match("/[a-zA-Z0-9]/",$_POST['client_name'])){ //a~z,A~Z,0~9があるか確認
    array_push($err,'使用できる文字は漢字、ひらがな、カタカナです');
  }

//ユーザー名チェック
  $user_name = $_POST['user_name'];
  if(mb_strlen($_POST['user_name']) > 10){
    array_push($err,'ユーザー名は10文字以下で入力してください');
  }elseif(mb_strlen($_POST['user_name']) == 0){
    array_push($err,'ユーザー名が入力されていません');
  }

//生年月日チェック
  $year = $_POST['year'];
  $month = $_POST['month'];
  $day = $_POST['day'];
  if($_POST['year'] == ''){
    array_push($err, '生年月日の年が選択されていません');
  }
  if($_POST['month'] == ''){
    array_push($err,'生年月日の月が選択されていません');
  }
  if($_POST['day'] == ''){
    array_push($err,'生年月日の日が選択されていません');
  }

//31日が存在しない月をチェック
  if($_POST['month'] == 4 || $_POST['month'] == 6 || $_POST['month'] == 9 || $_POST['month'] == 11){
    if($_POST['day'] > 30){
      array_push($err, '選択された月は30日までしか存在しません');
    }
  }elseif($_POST['month'] == 2){
    if($_POST['day'] > 28){
      array_push($err ,'2月は28日までしか存在しません');
    }
  }


//性別チェック
  $gender = $_POST['gender'];
  if($_POST['gender'] == ''){
    array_push($err,'性別が選択されていません');
  }elseif($_POST['gender'] == 'man'){
    $gender2 = '男';
  }elseif($_POST['gender'] == 'woman'){
    $gender2 = '女';
  }elseif($_POST['gender'] == 'trans'){
    $gender2 = 'どちらでもない';
  }


//電話番号チェック
  $phone = $_POST['phone'];
  if(strlen($_POST['phone']) == 0){
    array_push($err,'電話番号が入力されていません');
  }elseif(strlen($_POST['phone']) != 10 && strlen($_POST['phone']) != 11){
    array_push($err, '電話番号の桁数が違います');
  }

//郵便番号チェック
  $postal = $_POST['postal'];
  if(mb_strlen($_POST['postal']) != 7){
    array_push($err,'郵便番号は7字です');
  }elseif(mb_strlen($_POST['postal']) == 0){
    array_push($err,'郵便番号が入力されていません');
  }

//住所チェック
  $address = $_POST['address'];
  if(mb_strlen($_POST['address']) == 100){
    array_push($err, '住所は100字以内で入力してください');
  }elseif(mb_strlen($_POST['address']) == 0){
    array_push($err,'住所が入力されていません');
  }
  //$pre_idで都道府県id取得
  $pre = mb_substr($address, 0, 2);
  $sql = "SELECT id FROM prefectures WHERE prefectures_name LIKE '%".$pre."%'";
  $ary = mysqli_query($link,$sql);

  if($row = mysqli_fetch_assoc($ary)){
    $pre_id = $row['id'];
  }else{
    array_push($err, '住所が正しくありません');
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

  if($month < 10 && $month >0){ //1月から9月を01月にする
    $month = '0'.$month;
  }
  if($day<10 && $day >0){//1日から9日を01日にする
    $day = '0'.$day;
  }

  session_start();
  $_SESSION['client_name'] = $client_name;
  $_SESSION['user_name'] = $user_name;
  $_SESSION['birthday'] = $year.$month.$day;
  $_SESSION['gender'] = $gender;
  $_SESSION['phone'] = $phone;
  $_SESSION['postal'] = $postal;
  $_SESSION['address'] = $address;
  $_SESSION['pass'] = $_POST['pass'];
  $_SESSION['check_pass'] = $_POST['check_pass'];
  $_SESSION['bank_num'] = $bank;
  $_SESSION['mail'] = $_GET['mail'];
  $_SESSION['pre_id'] = $pre_id;
  header('Location: ./conf_personal_info.php');
  exit();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>本人情報入力</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/enter_personal_info.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <!-- <header>
    <p><img></p>
  </header> -->

  <main>
  <div class="mainbox">
    <div class="main">
        <p class="text-center">個人情報入力</p>
        <form action="" method="post">
          <div class="">
            <div class="form-group d-flex la">
              <label class="label">名前</label>
              <input type="text" name="client_name" value="<?php echo $client_name;?>" class="form-control form-control-sm ip">
            </div>

            <div class="form-group d-flex la">
              <label class="label">ユーザー名</label>
              <input type="text" name="user_name" value="<?php echo $user_name;?>" class="form-control form-control-sm ip">
            </div>

            <div class="form-group d-flex la">
              <label class="birth">生年月日</label>
              <div class="d-flex ip">
                <div class="d-flex y">
                  <select name="year" class="form-control form-control-sm">
                    <option value="<?php echo $year;?>" selected><?php echo $year;?></option>
                        <?php for($i=date('yy'); $i>=1920; $i--):?>
                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                        <?php endfor;?>
                  </select>
                  <label>年</label>
                </div>

                <div class="d-flex m">
                  <select name="month" class="form-control form-control-sm">
                    <option value="<?php echo $month;?>" selected><?php echo $month;?></option>
                        <?php for($i=1;$i<=12;$i++):?>
                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                        <?php endfor; ?>
                  </select>
                  <label>月</label>
                </div>

                <div class="d-flex d">
                  <select name="day" class="form-control form-control-sm">
                  <option value="<?php echo $day;?>" selected><?php echo $day;?></option>
                        <?php for($i=1;$i<=31;$i++): ?>
                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                        <?php endfor;?>
                  </select>
                  <label>日</label>
                </div>
              </div>
            </div>

            <div class="form-group d-flex la">
              <label class="label">性別</label>
              <select name="gender" class="form-control form-control-sm ip">
                <option value="<?php echo $gender;?>" selected><?php echo $gender2;?></option>
                <option value="trans">どちらでもない</option>
                <option value="man">男</option>
                <option value="woman">女</option>
              </select>
            </div>
          </div>


          <div class="">
            <div class="form-group d-flex la">
              <label class="label">電話番号</label>
              <input type="number" name="phone" value="<?php echo $phone;?>" class="form-control form-control-sm ip">
            </div>

            <div class="form-group d-flex la">
              <label class="label">郵便番号</label>
              <input type="number" name="postal" value="<?php echo $postal;?>" class="form-control form-control-sm ip">
            </div>

            <div class="form-group d-flex la">
              <label class="label">住所</label>
              <input type="text" name="address" value="<?php echo $address;?>" class="form-control form-control-sm ip">
            </div>
            <div>
              <?php ?>
            </div>
          </div>


          <div class="form-group ">
            <div class="form-group d-flex la">
              <label class="label">パスワード</label>
              <input type="password" name="pass" class="form-control form-control-sm ip">
            </div>

            <div class="form-group d-flex la">
              <label class="label">確認用パスワード</label>
              <input type="password" name="check_pass" class="form-control form-control-sm ip">
            </div>

            <div class="form-group d-flex la">
              <label class="label">口座番号</label>
              <input type="text" name="bank_num" value="<?php echo $bank ;?>" class="form-control form-control-sm ip">
            </div>
          </div>

          <div class="insert text-center">
            <button type="submit" name="regist" class="btn btn-primary">登録</button>
          </div>
        </form>
        <div class="">
          <ul>
          <?php foreach($err as $msg): ?>
            <li class="text-danger"><?php echo $msg;?></li>
          <?php endforeach; ?>
          </ul>
        </div>
  </div>
  </div>
  </main>
</body>
</html>
