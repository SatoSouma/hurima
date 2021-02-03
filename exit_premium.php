<?php
session_start();
// ハッシュ化関数
function pass($pass,$salt,$cnt){
  $i = 0;
  while($i < $cnt){
    $pass = md5($salt . $pass);
    $i++;
  }
  return $pass;
}

if(isset($_POST['sub_delete']) && isset($_SESSION['member_id'])){
  // サブスク会員であるかどうか確認
  $link = mysqli_connect('localhost','root','','ih22');
  mysqli_set_charset($link,'utf8');
  $sql = "SELECT member_id,delete_date FROM subscription WHERE member_id =  " . $_SESSION['member_id'] ; //本当はsession
  $result = mysqli_query($link,$sql);
  $row = mysqli_fetch_assoc($result);
  mysqli_close($link);
  //var_dump($row['member_id']);
  //var_dump($row['delete_date']);

  if($row['member_id'] !== NULL && $row['delete_date'] == NULL){
    // session_idに対応するパスワード,ハッシュ回数、郵便番号を取得
    $link = mysqli_connect('localhost','root','','ih22');
    mysqli_set_charset($link,'utf8');
    $sql = "SELECT p.pass,p.hash_cnt,m.postal_num FROM password AS p
    INNER JOIN member AS m ON m.id = p.pass_member_id WHERE p.pass_member_id = " . $_SESSION['member_id'] ;
    $result = mysqli_query($link,$sql);
    $pass_info = mysqli_fetch_assoc($result);
    mysqli_close($link);

    $pass_info['hash_cnt'] = intval($pass_info['hash_cnt']);
    // 入力したパスワードをハッシュ化
    $h_pass = pass($_POST['sub_pass'],$pass_info['postal_num'],$pass_info['hash_cnt']);
    // パスワードの照合 // ログイン処理
    if($h_pass == $pass_info['pass']){
      // パスワードが照合できた時headerでidを連れて確認画面へ
      header('Location:./exit_conf_premium.php?id=' . $_SESSION['member_id']);
    }else{
      // 確認出来なかった時err_messageをセット
      $err_msg = 'パスワードが違います。';
    }
  }else{
    // 既に会員であるときerr_messageをセット
    $err_msg = '貴方は既にプレミア会員ではありません。';
  }
  echo $err_msg;
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>プレミア会員退会</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/enter_premium.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <header>
    <p><img src="logo/satou.jpg"></p>
  </header>

  <main>
    <div class="mainbox">
      <div class="main">
        <p class="text-center">プレミア会員退会を行う</p>

        <form action="" method="POST">
            <div class="">
              <label class="">パスワード</label>
              <input class="form-control" type="password" name="sub_pass">
            </div>
            <div class="button text-center">
              <button class="btn btn-danger" type="submit" name="sub_delete">退会</button>
            </div>
        </form>

      </div>
    </div>
  </main>
</body>
</html>
