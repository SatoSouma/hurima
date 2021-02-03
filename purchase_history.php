<?php
const HOST = 'localhost';
const USER_ID = 'root';
const PASSWORD = '';
const DB_NAME = 'ih22';
session_start();
//購入履歴(LIMIT)
function get_data_purchase_history($link , $start , $cnt ,$id){
  $sql = ("SELECT tr.tr_product_id,pr.product_name,ph.photo_url,pr.product_status,pr.product_price,tr.sttlement_date
  FROM product AS pr
  INNER JOIN transaction AS tr ON tr.tr_product_id = pr.id
  INNER JOIN photo AS ph ON ph.product_id = pr.id
  WHERE ph.photo_no = 1 AND tr.pu_member_id = " .$id. " LIMIT " .$start. " , " . $cnt);
  $result = mysqli_query($link , $sql);

  $list =[];
  while($row = mysqli_fetch_assoc($result)){
      $list[] = $row;
  };
  return $list;
}

//購入履歴(COUNT)
function get_cnt_purchase_history($link , $id){
  $sql = ("SELECT COUNT(*) AS cnt
  FROM product AS pr
  INNER JOIN transaction AS tr ON tr.tr_product_id = pr.id
  INNER JOIN photo AS ph ON ph.product_id = pr.id
  WHERE ph.photo_no = 1 AND tr.pu_member_id = " .$id);

  $result = mysqli_query($link , $sql);

  return mysqli_fetch_assoc($result);
}

$id = $_SESSION['member_id'];
// var_dump($id);
// $id = (int) $id;
// var_dump($id);
// $id = sprintf('%06d', $id);
// var_dump($id);


$link = @mysqli_connect(HOST, USER_ID , PASSWORD , DB_NAME);
// if(!$link){
//     $err_msg = '予期せぬエラーが発生しました。しばらくたってから再度お試しください。(エラーコード：101)';
//     require_once './tpl/error.php';
//     exit;
//   }

mysqli_set_charset($link , 'utf8');

// 購入取り消し処理(入金前のみ有効)
// $_POST['del_id']が送られてきたら処理開始
if(isset($_POST['del_id'])){
  // idと一致するproductのpurchaseフラグを0に
  $sql = "UPDATE product SET purchase_flug = 0 WHERE id = '" . $_POST['del_id'] . "'";
  $result = mysqli_query($link,$sql);
  // idと一致するtransactionの行を消す。
  $sql = "DELETE FROM transaction WHERE tr_product_id = '" . $_POST['del_id'] . "'";
  $result = mysqli_query($link,$sql);
  // idと一致するpickupDestination_idとstorehouse_idを取り出す
  $sql = "SELECT pickupdestination_id,storehouse_id FROM pickup WHERE product_id = '" . $_POST['del_id'] . "'";
  $result = mysqli_query($link,$sql);
  $row = mysqli_fetch_assoc($result);
  // pickupDestination_id,storehouse_idと一致するpickupの行を消す。
  $sql = "DELETE FROM pickup WHERE pickupDestination_id  = '" . $row['pickupdestination_id'] . "' AND storehouse_id = '" . $row['storehouse_id'] . "'";
  $result = mysqli_query($link,$sql);
  // pickupdestination_idと一致するpickupDestinationの行を消す。
  $sql = "DELETE FROM pickupDestination WHERE id = '" . $row['pickupdestination_id'] . "'";
  $result = mysqli_query($link,$sql);
  // idと一致するdestination_idとstorehouse_idを取り出す。
  $sql = "SELECT destination_id,storehouse_id FROM delivery WHERE product_id = '" . $_POST['del_id'] . "'";
  $result = mysqli_query($link,$sql);
  $row_f = mysqli_fetch_assoc($result);
  // destination_id、storehouse_idと一致するdeliveryの行を消す。
  $sql = "DELETE FROM delivery WHERE destination_id  = '" . $row_f['destination_id'] . "' AND storehouse_id = '" . $row_f['storehouse_id'] . "'";
  $result = mysqli_query($link,$sql);
  // destination_idと一致するdestinationの行を消す。
  $sql = "DELETE FROM destination WHERE id = '" . $row_f['destination_id'] . "'";
  $result = mysqli_query($link,$sql);
}

//件数取得
$cnt = get_cnt_purchase_history($link , $id);


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
$list = get_data_purchase_history($link , $start , 20 , $id);

// echo '<pre>';
// var_dump($list);
// echo '</pre>';

//開始位置と件数を指定して取得
$list = get_data_purchase_history($link , $start , 20 , $id);

mysqli_close($link);


?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>購入履歴</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/purchase_history.css">
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
          <th class="col-3">商品名</th>
          <th class="col-3">商品画像</th>
          <th class="col-2">状態</th>
          <th class="col-2">価格</th>
          <th class="col-2">購入取り消し</th>
        </tr>
        <thead>
        <?php foreach($list as $key => $val):?>
        <tr class="d-flex">
          <td class="col-3 text-center"><?php echo $val['product_name']; ?></td>
          <td class="col-3 text-center"><img class="h_img" src="./img/<?php echo $val['photo_url']; ?>" class="his_img"></td>
          <td class="col-2 text-center"><?php echo $val['product_status']; ?></td>
          <td class="col-2 text-center"><?php echo $val['product_price']; ?></td>
          <!-- 入金されていないとき購入取り消しの選択が可能 -->
          <?php if($val['sttlement_date'] == NULL){ ?>
            <td class="col-2 text-center"><form action="" method="POST"><button class="btn btn-primary" name='del_id' value="<?php echo $val['tr_product_id'] ?>">購入取り消し</button></form></td>
          <?php }else{ ?>
            <td class="col-2 text-center">入金済み</td>
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
