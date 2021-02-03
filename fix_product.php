<?php
//データベースに接続
$link = mysqli_connect(  'localhost'  , 'root' , '' , 'ih22');
mysqli_set_charset($link, 'utf8');

//データベースから商品情報を取得
$product_id = $_GET['product_id'];
$data = mysqli_query($link,"SELECT * FROM product WHERE id=$product_id");
$row = mysqli_fetch_array($data);

//データベースの接続を切断
mysqli_close($link);

$product_name = '';
$oxplain = '';
$product_price = '';
$err = [];

// 編集ボタンが押されたとき
if(isset($_POST['regist'])){
  //商品名の入力値チェック
  $product_name = $_POST['product_name'];
  if(mb_strlen($_POST['product_name']) > 50){ //文字数チェック
    array_push($err,'50文字以下で入力してください');
  }elseif(mb_strlen($_POST['product_name']) == 0){
    array_push($err,'商品名が入力されていません');
  }

  //商品説明の入力値チェック
  $oxplain = $_POST['oxplain'];
  if(mb_strlen($_POST['oxplain']) == 0){
    array_push($err,'商品説明が入力されていません');
  }

  //値段の入力値チェック
  $product_price = $_POST['product_price'];
  if(mb_strlen($_POST['product_price']) > 6){ //桁数チェック
    array_push($err,'6桁以下で入力してください');
  }elseif(mb_strlen($_POST['product_price']) == 0){
    array_push($err,'値段が入力されていません');
  }
}

if(isset($_POST['regist']) && !isset($err[0])){
  session_start();
  $_SESSION['product_name'] = $product_name;
  $_SESSION['oxplain'] = $oxplain;
  $_SESSION['status'] = $_POST['status'];
  $_SESSION['product_price'] = $product_price;
  $_SESSION['product_id'] = $_POST['product_id'];
  header('Location: ./listing_history.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>商品編集</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/fix_product.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <header>
    <p><img src="logo/satou.jpg"></p>
  </header>

  <main>
    <h2 class="none">出品商品編集</h2>
    <div class="mainbox">
    <p class="text-center">商品情報修正</p>
    <div class="main">
    <form action="" method="POST">

      <input type="hidden" name="product_id" value="<?php echo $product_id;?>">
      <div class="form-group d-flex">
        <label>商品名</label>
        <input class="form-control form-control-sm input" type="text" name="product_name" value="<?php echo $row['product_name'];?>">
      </div>

      <div class="form-group d-flex">
        <label>商品説明</label>
        <textarea class="form-control form-control-sm input" name="oxplain" rows="4" cols="40"><?php echo $row['product_explain'];?></textarea>
      </div>

      <div class="form-group d-flex">
        <label>商品状態</label>
        <select class="form-control form-control-sm input" name="status">
          <option value="<?php echo $row['product_status'];?>"><?php echo $row['product_status'];?></option>
          <option value="新品">新品</option>
          <option value="中古">中古</option>
          <option value="キズあり">キズあり</option>
        </select>
      </div>

      <div class="form-group d-flex">
        <label>価格</label>
        <input class="form-control form-control-sm input" type="price" name="product_price" value="<?php echo $row['product_price'];?>">
      </div>

      <div class="text-center button">
        <a href="user.php"><button class="btn btn-secondary">戻る</button></a>
        <input class="btn btn-primary" type="submit" name="regist" value="編集">
      </div>

    </form>

    <?php foreach($err as $msg): ?>
      <li class="text-danger"><?php echo $msg;?></li>
    <?php endforeach; ?>

  </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>

  </main>
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
