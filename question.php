<?php
$list = [];
$fp = fopen('./question.csv','r');
$row = fgets($fp);

while($row){
  $row = explode(',',$row);
  array_push($list, $row);
  $row = fgets($fp);
}
fclose($fp);
$msg = '';
$msg2 = '';
if(isset($_POST['text'])){
  if($_POST['text'] == ''){
    $msg = '問い合わせ内容が入力されていません';
  }
  if($_POST['mail'] == ''){
    $msg2 = 'メールアドレスが入力されていません';
  }
  if($_POST['text'] != '' && $_POST['mail'] != ''){
    header('Location: ./top.php');
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>商品情報入力</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/question.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <header>
    <p><img src="logo/satou.jpg"></p>
  </header>

  <main>
  <div class="mainbox">
      <div class="main">
        <p class="p">よくある質問</p>
    
  <?php foreach($list as $row):?>
    <div>
      <p>Q,<?php echo $row[0];?></p>
      <p>A,<?php echo $row[1];?></p>
    </div>
  <?php endforeach;?>

    <form method="post" action="" enctype="multipart/form-data">
      <div class="form-group">
        <label>メールアドレス</label>
        <input class="form-control" type="text" name="mail">
      </div>

      <div class="form-group">
        <label>問い合わせ内容</label>
        <textarea class="form-control" name="text" rows="4" cols="40"></textarea>
      </div>

      <p class="text-danger"><?php echo $msg2;?></p>
      <p class="text-danger"><?php echo $msg;?></p>

      <button  class="btn btn-primary" type="submit" name="send">送信</button>
    </form>
    <a href="./top.php"><button class="btn btn-secondary">戻る</button></a>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>

<script>
  $(function() {
  var $textarea = $('#textarea');
  var lineHeight = parseInt($textarea.css('lineHeight'));
  $textarea.on('input', function(e) {
    var lines = ($(this).val() + '\n').match(/\n/g).length;
    $(this).height(lineHeight * lines);
  });
});

</script>
</body>
</html>
