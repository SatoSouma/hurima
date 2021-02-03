<?php

function pass($pass,$salt,$cnt){
  $i = 0;
  while($i < $cnt){
      $pass = md5($salt . $pass);
      $i++;
  }
  return $pass;
}

//DB接続
$link = mysqli_connect('localhost', 'root', '', 'ih22');
mysqli_set_charset($link, 'utf8');

session_start();
//性別の日本語表示
if($_SESSION['gender'] == 'man'){
  $gender = '男';
}elseif($_SESSION['gender'] == 'woman'){
  $gender = '女';
}else{
  $gender = '不';
}

//生年月日をoooo年00月00日にする
$ary = str_split($_SESSION['birthday'], 2);
$birthday = $ary[0].$ary[1].'年'.$ary[2].'月'.$ary[3].'日';

$ary = str_split($_SESSION['postal'],3);
$postal = $ary[0].'-'.$ary[1].$ary[2];

$cnt = rand(100,999);
$pass = pass($_SESSION['pass'], $_SESSION['postal'], $cnt);

$mail = $_SESSION['mail'];

//$pre_idで都道府県id取得
$pre_id = $_SESSION['pre_id'];


if(isset($_GET['regist'])){

//一番最新のid下四桁を取得
  $sql = "SELECT id FROM member  ORDER BY SUBSTRING(id,4) desc limit 1";
  $ary = mysqli_query($link,$sql);
  $row = mysqli_fetch_assoc($ary);
  $id = $pre_id.mb_substr($row['id'], 2, 4);
  $id++;
//会員情報登録(id,名前,性別,生年月日,電話番号,ユーザー名,郵便番号,住所,口座番号,都道府県id)
  $sql = "INSERT INTO member (id, member_name, gender, birthday, telephon_num, user_name, postal_num, address, bank_num, prefectures_id) VALUES(".$id.", '".$_SESSION['client_name']."', '".$gender."', ".$_SESSION['birthday'].", ".$_SESSION['phone'].", '".$_SESSION['user_name']."',".$_SESSION['postal'].", '".$_SESSION['address']."', ".$_SESSION['bank_num'].",".$pre_id.")";
  mysqli_query($link, $sql);

//パスワード登録(id,ハッシュ済みパスワード,ハッシュ回数,メアド)
  $sql = "INSERT INTO password (pass_member_id, pass, hash_cnt,mail) VALUES(".$id.", '".$pass."', ".$cnt.", '".$mail."')";
  mysqli_query($link,$sql);

//sessionで会員id保持してtop.phpに飛ばす
  session_destroy();
  session_start();
  $_SESSION['member_id'] = $id;
   header('Location: ./top.php');
   exit();
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>本人情報確認</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/conf_personal_info.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <!-- <header>
    <p><img></p>
  </header> -->
  <main class="container">
    <div class="mainbox">
      <div class="main">
        <table class="table table-bordered">
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">名前</th><td class="col-9"><?php echo $_SESSION['client_name'];?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">ユーザー名</th><td class="col-9"><?php echo $_SESSION['user_name'];?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">生年月日</th><td class="col-9"><?php echo $birthday;?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">性別</th><td class="col-9"><?php echo $gender;?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">電話番号</th><td class="col-9"><?php echo $_SESSION['phone'];?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">郵便番号</th><td class="col-9"><?php echo $postal;?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">住所</th><td class="col-9"><?php echo $_SESSION['address'];?></td></tr>
          <tr class="d-flex">
            <td class="text-center col-12" colspan="2">
              <form action="">
                <button type="submit" name="regist" class="btn btn-primary">登録</button>
              </form>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
