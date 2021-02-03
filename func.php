<?php
//購入履歴(LIMIT)
function get_data_purchase_history($link , $start , $cnt ,$id){
    $sql = ("SELECT tr.tr_product_id,pr.product_name,ph.photo_url,pr.product_status,pr.product_price
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

//出品履歴(LIMIT)
function get_data_listing_history($link , $start , $cnt , $id){
    $sql = ("SELECT pr.id,pr.product_name,ph.photo_url,pr.product_status,pr.product_price
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
