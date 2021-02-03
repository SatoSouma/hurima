<?php
session_start();
$product_id = $_GET['product_id'];
$_SESSION['product_id'] = $product_id;
$f_array = [];

//DB接続
$link=mysqli_connect('localhost','root','','ih22');
mysqli_set_charset($link,'utf8');

//productから取得
$result = mysqli_query($link,"SELECT A.product_name,A.product_price,A.product_status,A.product_explain,B.member_name
  FROM product A INNER JOIN member B ON A.ls_member_id = B.id WHERE A.id = $product_id;");
$p_data = mysqli_fetch_assoc($result);

//photoから取得
$result = mysqli_query($link,"SELECT photo_url FROM photo WHERE product_id = $product_id");
foreach ($result as $key) {
  array_push($f_array,$key);
}

if (isset($_POST['buy'])) {
  mysqli_close($link);
  $_SESSION['product_id'] = $product_id;
  header("Location: ./purchase.php");
}


 ?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>各商品詳細</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/detail_product.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <header>
    <p><img src="logo/satou.jpg"></p>
  </header>

  <main class="container">
    <div class="main">
      <table class="table table-striped table-bordered">
        <tr>
          <td rowspan="4" class="imgtd">
            <div class="cp_cssslider ccc">
              <!-- 画像1枚目 -->
              <input type="radio" name="cp_switch" id="photo1" checked/>
              <label for="photo1"><img src="img/<?php echo $f_array['0']['photo_url']; ?>" /></label>
              <img class="img" src="img/<?php echo $f_array['0']['photo_url']; ?>" />

              <!-- 画像2枚目 -->
              <?php if(isset($f_array['1']['photo_url'])) :?>
              <input type="radio" name="cp_switch" id="photo2"/>
              <label for="photo2"><img src="img/<?php echo $f_array['1']['photo_url']; ?>" /></label>
              <img class="img" src="img/<?php echo $f_array['1']['photo_url']; ?>" />
              <?php endif; ?>
              <!-- 画像3枚目 -->
              <?php if(isset($f_array['2']['photo_url'])) :?>
              <input type="radio" name="cp_switch" id="photo3"/>
              <label for="photo3"><img src="img/<?php echo $f_array['2']['photo_url']; ?>" /></label>
              <img class="img" src="img/<?php echo $f_array['2']['photo_url']; ?>" />
              <?php endif; ?>

            </div>
          </td>
          <td class="text-center"><?php echo $p_data['product_name']; ?></td>
        </tr>
        <tr><td class="text-center"><?php echo $p_data['product_price']; ?>円</td></tr>
        <tr><td class="text-center"><?php echo $p_data['product_status']; ?></td></tr>
        <tr><td class="text-center"><?php echo $p_data['member_name']; ?></td></tr>
        <tr><td colspan="2" class="text-center"><?php echo $p_data['product_explain']; ?></td></tr>
        <tr>
          <td class="" colspan="2">
            <div class="bb">
              <div class="d-flex justify-content-between cc">
                <a href="./top.php"><button class="btn btn-secondary">戻る</button></a>
                  <?php if(isset($_SESSION['member_id'])): ?>
                  <form action="" class="" method="POST">
                  <button type="submit" name="buy" class="btn btn-primary">購入</button>
                  </form>
                  <?php endif; ?>
              </div>
            </div>
          </td>
        </tr>
      </table>
    </div>

  </main>
</body>
</html>
