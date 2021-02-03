<?php
function pass($pass,$salt,$cnt){
  $i = 0;
  while($i < $cnt){
      $pass = md5($salt . $pass);
      $i++;
  }
  return $pass;
}
$msg = '';
if(isset($_POST['login'])){
  $link = mysqli_connect('localhost', 'root', '', 'ih22');
  mysqli_set_charset($link, 'utf8');

  $sql = "SELECT password.hash_cnt, password.pass, member.postal_num, member.id FROM member INNER JOIN password ON member.id = password.pass_member_id WHERE password.mail = '".$_POST['login_mail']."'";
  $list = mysqli_query($link, $sql);
  $list = mysqli_fetch_assoc($list);
  if(!$list){
    $msg = 'メールアドレスかパスワードが間違っています';
  }else{
    $pass = pass($_POST['login_pass'], $list['postal_num'],$list['hash_cnt']);
    if($pass == $list['pass']){
      session_start();
      $_SESSION['member_id'] = $list['id'];
      header('Location: ./top.php');
  // 　  exit;
    }else{
      $msg = 'メールアドレスかパスワードが間違っています';
    }
  }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ログイン</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/login.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <header>
    <p><img class="logo_img" src="./logo/satou.jpg"></p>
  </header>
  <main>
    <div class="mainbox">
      <div class="main">
        <form method="post" action="#">
          <div>
            <p>メールアドレス<br>
            <input class="form-control" type="text" name="login_mail"></p>
          </div>
          <div>
            <p>パスワード<br>
            <input class="form-control" type="password" name="login_pass"></p>
          </div>
          <div class="text-center">
            <button class="btn btn-primary" type="submit" name="login">ログイン</button>
          </div>
        </form>
        <p class="text-danger"><?php echo $msg;?></p>
        <p><a href="./sign_up.php">まだ登録してない人はこちら</a></p>
      </div>
    </div>
  </main>
</body>
</html>
