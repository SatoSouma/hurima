<?php
//メールの処理
/* $mail = "";
if (isset($_POST["yourmail"])) {
  $mail = $_POST["yourmail"];
  if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)) {
    $from = "test@example.com";
    $subject = "サンプルフォームからのメール送信";
    $message = "{$mail}にメールを送信しました。\r\nこちらのURLからアクセスして本人情報を入力してください。enter_personal_info.php";
    $header = "From: {$from}\r\nReply-To: {$from}\r\n";
    $result = mb_send_mail($mail, $subject, $message, $header);
    if ($result) {
      $msg = "正常にメールを送信しました。";
    } else {
      $msg = "メールの送信に失敗しました。";
    }
  } else {
    $msg = "メールアドレスが不正です。";
  }
} else {
  $msg = "";
} */

// 本人情報入力へ飛ばすか否か判定
if(isset($_GET['yourmail'])){

  // 英語半角以外弾く
  if(preg_match('|^[0-9a-z_./?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$|',$_GET['yourmail'])){
    $link = 'enter_personal_info.php?mail=';
    $sendMile = $_GET['yourmail'];
    $msg = 'こちらから本人情報を入力してください';
  }else{
    $msg = 'メールアドレスが不正です。';
  }

}else{
  $link = '';
  $sendMile = '';
  $msg = '';
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>サインアップ</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/sign_up.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <header>
    <p><img src="./logo/satou.jpg"></p>
  </header>
  <main>
    <div class="main">

    <!-- 半角入力の時アンカータグを使用 -->
    <?php if(isset($link)){ ?>
      <p><a href="<?php echo $link ?><?php echo $sendMile;?>"><?php echo $msg ?></a></p>
    <?php } ?>

      <form action="sign_up.php" method="get" autocomplete="off">
        <div class="form-group">
          <p>メールアドレス<br>
          <input class="form-control form-control" type="text" name="yourmail" value="">

          <!-- 全角の時エラー文表示 -->
          <?php if(!isset($link)){ ?>
            <p class="text-danger"><?php echo $msg ?></p>
          <?php } ?>

        </div>
        <div class="sign d-flex justify-content-center align-bottom">
          <input class="btn btn-primary" type="submit" name="signup" value="サインアップ">
        </div>
      </form>
    </div>
  </main>
</body>
</html>
