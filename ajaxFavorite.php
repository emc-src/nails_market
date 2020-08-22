<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　Ajax　お気に入り機能');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

debug('$_SESSION[user_id]の中身：' . print_r($_SESSION['user_id'], true));
debug('$_POSTの中身：' . print_r($_POST, true));

// お気に入り登録がある場合は削除、お気に入り登録がない場合は登録
if (!empty($_SESSION['user_id']) && !empty($_POST['productId'])) :
  debug('AjaxのPOST送信があります。');
  $u_id = $_SESSION['user_id'];
  $p_id = $_POST['productId'];

  try {
    $dbh = dbConnect();
    // お気に入り登録があるかを確認
    $sql = 'SELECT count(*)
            FROM favorites
            WHERE product_id = :p_id AND user_id = :u_id
            ';
    $data = array(':p_id' => $p_id, ':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data, 'Ajax お気に入り登録確認');
    if ($stmt) :
      $favCount = $stmt->fetch(PDO::FETCH_ASSOC);
      debug('$favCountの中身(お気に入り登録):' . print_r($favCount, true));
    endif;

    // お気に入り登録ありの場合はDBから削除
    if ($favCount['count(*)'] > 0) :
      $sql = 'DELETE FROM favorites
            WHERE product_id = :p_id AND user_id = :u_id
            LIMIT 1
              ';
      $data = array(':p_id' => $p_id, ':u_id' => $u_id);
      $stmt = queryPost($dbh, $sql, $data, 'Ajax お気に入りから削除');
      if ($stmt) :
        debug('お気に入りから削除のクエリ成功。');
      else :
        debug('お気に入りから削除のクエリ失敗、');
      endif;
    // お気に入り登録なしの場合はDBへ登録
    else :
      $sql = 'INSERT INTO favorites
              (product_id, user_id, create_date)
              VALUES
              (:p_id, :u_id, :date)
            ';
      $data = array(
        ':p_id' => $p_id, ':u_id' => $u_id, ':date' => date('Y-m-d H:i:s')
      );
      $stmt = queryPost($dbh, $sql, $data, 'Ajax お気に入り登録');
      if ($stmt) :
        debug('お気に入り登録のクエリ成功。');
      else :
        debug('お気に入り登録のクエリ失敗。');
      endif;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（お気に入り登録削除 ajaxTran.phpにて）：' . $e->getMessage());
  }
endif;

debug('＞ ＞ ＞ ＞ ＞　 Ajax　お気に入り機能 処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');