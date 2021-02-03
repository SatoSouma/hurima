<?php
$link = mysqli_connect('localhost', 'root', '', 'ih22');
mysqli_set_charset($link, 'utf8');


//ログアウト処理
session_start();
if(isset($_GET['log'])){
  unset($_SESSION['member_id']);
}

$login = "./login.php";
$login_msg ="ログイン";
$id = 1;
$user_name = 'ログインしてください';
//セッションでログインしてるか確認
if(isset($_SESSION['member_id'])){
  $login ="top.php?log=1";
  $login_msg = "ログアウト";
  $id = $_SESSION['member_id'];
  $sql = "SELECT member_name FROM member WHERE id = ".$_SESSION['member_id'];
  $ary = mysqli_query($link, $sql);
  $user = mysqli_fetch_assoc($ary);
  $user_name = $user['member_name'].'さん';
}

//ページ数を確認
$sql = "SELECT count(*) FROM product WHERE purchase_flug <> 1";
if(isset($_GET['search_name'])){
  $sql = $sql." AND product.product_name LIKE '%".$_GET['search_name']."%'";
}
$cnt = mysqli_query($link,$sql);
$cnt = mysqli_fetch_assoc($cnt);
$cnt = ceil($cnt["count(*)"]/20);
$page = 1;

//商品一覧表示
if(isset($_SESSION['member_id'])){
  $sql = "SELECT product.id, product.product_name, product.product_price, photo.photo_url FROM product INNER JOIN photo ON product.id = photo.product_id WHERE photo.photo_no = 1 AND purchase_flug <> 1 AND ls_member_id <> ".$_SESSION['member_id'];
  // AND ls_member_id <> ".$_SESSION['member_id']
}else{
  $sql = "SELECT product.id, product.product_name, product.product_price, photo.photo_url FROM product INNER JOIN photo ON product.id = photo.product_id WHERE photo.photo_no = 1 AND purchase_flug <> 1";
}

//検索が押されたとき
if(isset($_GET['search_name'])){
  $sql = $sql." AND product.product_name LIKE '%".$_GET['search_name']."%'";
}
if(isset($_GET['page'])){
  $page = $_GET['page'];
}
$start = $page*20-20;
$end = $page*20;
$sql = $sql." LIMIT 20 OFFSET ".$start;

$ary = mysqli_query($link,$sql);
$row = mysqli_fetch_assoc($ary);
$cnt3 = 1;
$list = [];
while($row){
  array_push($list, $row);
  $row = mysqli_fetch_assoc($ary);
  $cnt3++;
}
$y = ceil(count($list)/4);
$x = count($list);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>TOP</title>
<meta content="タイトル" name="title">


</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/top.css">
<link rel="stylesheet" href="css/all.css">
<body>
  dev
  <h1 class="none">サトウのフリマ</h1>
  <header class="d-flex">
    <p class="logo"><img class="logo_img" src="./logo/satou.jpg"></p>
    <div class="d-flex zz">
      <?php if(isset($_SESSION['member_id'])): ?>
      <p class="user"><a href="./user.php"><?php echo $user_name;?></a></p>
      <?php endif; ?>
      <div>
        <p><a href="./question.php">問い合わせ</a></p>
        <a href="<?php echo $login;?>"><button class="<?php echo $login_msg=='ログイン'?'btn btn-primary':'btn btn-danger' ?>"><?php echo $login_msg;?></button></a>
      </div>
    </div>
  </header>

  <main>
    <h2 class="none">検索</h2>
      <form class="d-flex form-group" action="" method="get">
        <div class="serz d-flex text-center">
          <input class="ser form-control" type="text" name="search_name">
          <button class="serbtn btn btn-primary" type="submit" name="search">検索</button>
        </div>
      </form>

    <div class="main">
      <h2 class="none">商品一覧</h2>

      <?php for($i=0; $i<=$y; $i++){
          $j=$i*4; ?>
      <div class="d-flex colpro">
      <?php for($cnt1=0; $cnt1<4; $cnt1++):
        if($j>=$x){
          break;
        }  ?>
        <div class="pro <?php echo ($j+1)%4 != 0?'pro3':''; ?>">
          <p class="img"><a href="./detail_product.php?product_id=<?php echo $list[$j]['id'];?>"><img class="pro_img" src="./img/<?php echo $list[$j]['photo_url'];?>"></a></p>
            <div class="">
              <p class="price">¥<?php echo $list[$j]['product_price'];?></p>
              <p class="pro_info"><?php echo $list[$j]['product_name'];?></p>
            </div>
        </div>
        <?php $j++;
        endfor; ?>
      </div>
      <?php } ?>

      <?php if(isset($_SESSION['member_id'])): ?>
      <a href="./enter_product_info.php" class="circle">
        <!-- <span class="text">
          <div>出</div>
          <div>品</div>
        </span> -->
        <div class="sell">出品</div>
      </a>
      <?php endif; ?>



    <div class="container">
      <table class="table">
        <tr class="d-flex">
            <td class="text-center col-12">
              <?php for($k=1;$k<=$cnt;$k++):?>
                <a class="dpage" href="./top.php?page=<?php echo $k;?>"><?php echo $k;?></a>
              <?php endfor;?>
            </td>
        </tr>
      </table>
    </div>
  </div>
  </main>
</body>
</html>
