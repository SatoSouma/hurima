<?php
//セッション
session_start();
//$_SESSION['member_id'];
$id = $_SESSION['member_id'];

// var_dump($_SESSION['product_name']);
// var_dump($_SESSION['oxplain']);
// var_dump($_SESSION['status']);
// var_dump($_SESSION['product_price']);
// var_dump($_SESSION['product_id']);
// var_dump($_SESSION[$id]);


//データベース接続
$link=mysqli_connect('localhost','root','','ih22');
mysqli_set_charset($link,'utf8');

// 出品取り消し処理(購入されていないとき可能)
if(isset($_POST['del_id'])){
  // idと一致するphotoの行を削除
  $sql = "DELETE FROM photo WHERE product_id = '" . $_POST['del_id'] . "'";
  $result = mysqli_query($link,$sql);
  // idと一致するserviceselectの行を削除
  $sql = "DELETE FROM serviceselect WHERE product_id = '" . $_POST['del_id'] . "'";
  $result = mysqli_query($link,$sql);
  // idと一致するproductの行を削除
  $sql = "DELETE FROM product WHERE id = '" . $_POST['del_id'] . "'";
  $result = mysqli_query($link,$sql);
}

//出品履歴情報編集：アップデート文
if(isset($_SESSION['product_name']) || isset($_SESSION['oxplain']) || isset($_SESSION['status']) || isset($_SESSION['product_price'])){
  $product_name = $_SESSION['product_name'];
  $explain = $_SESSION['oxplain'];
  $status = $_SESSION['status'];
  $product_price = $_SESSION['product_price'];
  $p_id = $_SESSION['product_id'];


  unset($_SESSION['product_name']);
  unset($_SESSION['oxplain']);
  unset($_SESSION['status']);
  unset($_SESSION['product_price']);
  unset($_SESSION['product_id']);

  $update = "UPDATE product SET product_name = '" .$product_name. "' , product_explain = '" .$explain. "' ,  product_status = '" .$status. "' , product_price = '" .$product_price. "' WHERE id =".$p_id;
  $result = mysqli_query($link,$update);
}


//出品履歴(LIMIT)
function get_data_listing_history($link , $start , $cnt , $id){
  $sql = ("SELECT pr.id,pr.product_name,ph.photo_url,pr.product_status,pr.product_price,pr.purchase_flug
  FROM product AS pr
  INNER JOIN photo AS ph ON ph.product_id = pr.id  AND ph.photo_no = 1
  WHERE purchase_flug = 0 AND ls_member_id = " .$id. " LIMIT " .$start. " , " . $cnt);
  $result = mysqli_query($link , $sql);

  $list =[];
  while($row = mysqli_fetch_assoc($result)){
      $list[] = $row;
  };
  return $list;
}

//出品履歴(COUNT)
function get_cnt_listing_history($link , $id){
  $sql = ("SELECT COUNT(*) AS cnt
  FROM product AS pr
  INNER JOIN photo AS ph ON ph.product_id = pr.id  AND ph.photo_no = 1
  WHERE purchase_flug = 0 AND ls_member_id = " .$id);
  $result = mysqli_query($link , $sql);

  return mysqli_fetch_assoc($result);
}

/* $link = @mysqli_connect(HOST, USER_ID , PASSWORD , DB_NAME); */
// if(!$link){
//     $err_msg = '予期せぬエラーが発生しました。しばらくたってから再度お試しください。(エラーコード：101)';
//     require_once './tpl/error.php';
//     exit;
//   }

mysqli_set_charset($link , 'utf8');


//件数取得
$cnt = get_cnt_listing_history($link , $id);


//ページ数取得
$pagecnt = ceil($cnt['cnt'] / 20);

$i = 0;
while($i < $pagecnt){
  $page[$i] = $i+1;
  $i++;
}
if($pagecnt == 0){
  $page[0] = 1;
}

//開始位置
$start = 0;//初期化

if(isset($_GET['page'])){//ページ選択されたら
  $start = $_GET['page'] * 5 - 5;
}

//現在のページを取得
$nowpage = 1;//初期化
if(isset($_GET['page'])){
  $nowpage = $_GET['page'];
}

//開始位置と件数を指定して取得
$list = get_data_listing_history($link , $start , 20 , $id);
mysqli_close($link);

// echo '<pre>';
// var_dump($cnt,$list,$page);
// echo '</pre>';


?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>出品履歴</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/listing_history.css">
<link rel="stylesheet" href="css/all.css">

<body>
  <h1 class="none">サトウのフリマ</h1>
  <header>
    <p><img src="logo/satou.jpg"></p>
  </header>
  <main class="container">
    <table class="table table-bordered">
      <thead class="thead-dark">
        <tr class="d-flex">
          <th class="col-2 text-center">商品名</th>
          <th class="col-2 text-center">商品画像</th>
          <th class="col-2 text-center">状態</th>
          <th class="col-2 text-center">価格</th>
          <th class="col-2 text-center">編集</th>
          <th class="col-2 text-center">購入済み</th>
        </tr>
      <thead>
      <?php foreach($list as $key => $val):?>
      <tr class="d-flex">
        <td class="col-2 text-center"><?php echo $val['product_name']; ?></td>
        <td class="col-2 text-center"><img class="h_img" src="./img/<?php echo $val['photo_url']; ?>" class="his_img"></td>
        <td class="col-2 text-center"><?php echo $val['product_status']; ?></td>
        <td class="col-2 text-center"><?php echo $val['product_price']; ?></td>
        <td class="col-2 text-center"><a href="fix_product.php?product_id=<?php echo $val['id']; ?>">編集</a></td>
        <!-- 購入フラグが0の時出品を取り消せる -->
        <?php if($val['purchase_flug'] == 0){ ?>
          <td class="col-2 text-center"><form action="" method="POST"><button class="btn btn-primary" name='del_id' value="<?php echo $val['id'] ?>">出品取り消し</button></form></td>
        <?php }else{ ?>
          <td class="col-2 text-center">購入済み</td>
        <?php } ?>
      </tr>
      <?php endforeach; ?>
    </table>

  <div class="container page">
    <table class="table">
      <tr class="d-flex">
          <td class="text-center col-2"><a href="?=<?php echo max($nowpage - 1 , 1); ?>">前へ</a></td>
          <td class="text-center col-8">
            <?php foreach($page as $key => $val):?>
              <a <?php if($nowpage != $val): ?> href="./list.php?page=<?php echo $val;?>"<?php endif; ?>><?php echo $val; ?></a>
            <?php endforeach; ?>
          </td>
          <td class="text-center col-2"><a href="?=<?php echo min($nowpage +1 , count($page)); ?>">次へ</a></td>
      </tr>
    </table>
  </div>

  <a href="user.php" class="text-white"><button class="btn btn-secondary" type="submit" name="back">戻る</button></a>

  </main>
</body>
</html>
