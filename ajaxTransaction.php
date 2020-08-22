<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　Ajax　お取引画面');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

debug('$_SESSION[user_id]の中身：' . print_r($_SESSION['user_id'], true));
debug('$_SESSION[t_id]の中身：' . print_r($_SESSION['t_id'], true));
debug('$_POSTの中身：' . print_r($_POST, true));

$return = array();
// お取引IDとAjaxのデータがある場合
if (isset($_SESSION['t_id']) && isset($_POST['tranChange'])) :
  debug('AjaxのPOST送信があります。');
  // 閲覧者（出品者or購入者）を判別してクリックしたノードの編集が可能かを判定する
  // データ更新権なしの場合は変数にfalseを代入
  switch ($_POST['tranChange']):
    case 'payment':
    case 'tranEnd':
      if ($_SESSION['user_id'] !== $_SESSION['seller_id']) :
        $return = array('result' => false, 'change_user' => 'no_match');
      endif;
      break;
    case 'getProduct':
      if ($_SESSION['user_id'] !== $_SESSION['buyer_id']) :
        $return = array('result' => false, 'change_user' => 'no_match');
        break;
      endif;
  endswitch;
  // データ更新権なしの場合はJSにfalse情報を返す
  if (!empty($return)) :
    debug('閲覧者にクリックしたお取引状況の更新権がありません。');
    echo json_encode($return);
    exit();
  endif;
  // データ更新権ありの場合
  if (empty($return)) :
    try {
      $dbh = dbConnect();
      $sql = 'UPDATE products SET';
      $date = date('Y-m-d H:i:s');
      switch ($_POST['tranChange']):
        case 'payment':
          $sql .= ' pay_flg = 1, pay_date = :date';
          break;
        case 'getProduct':
          $sql .= ' get_flg = 1, get_date = :date';
          break;
        case 'tranEnd':
          $sql .= ' deal_flg = 1, dealEnd_date = :date';
          break;
      endswitch;
      $sql .= ' WHERE tran_id = :tran_id LIMIT 1';
      $data = array(':tran_id' => $_SESSION['t_id'], ':date' => $date);
      $stmt = queryPost($dbh, $sql, $data, 'Ajaxによるお取引状況変更');
      if ($stmt) :
        debug('Ajaxによるお取引状況変更が成功しました。');
        $date = date('Y年m月d日　H:i:s', strtotime($date));
        $return = array('result' => true, 'date' => '更新日：' . $date);
        echo json_encode($return);
      else :
        $return = array('result' => false);
        echo json_encode($return);
      endif;
    } catch (Exception $e) {
      error_log('例外エラー発生（お取引状況入力ajaxTran.phpにて）：' . $e->getMessage());
      $return = array('result' => false);
      echo json_encode($return);
    }
  endif;
endif;
debug('＞ ＞ ＞ ＞ ＞　 Ajax　お取引画面  処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');