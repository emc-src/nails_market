<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　Ajax　商品編集');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

debug('$_SESSION[user_id]の中身：' . print_r($_SESSION['user_id'], true));
debug('$_POSTの中身：' . print_r($_POST, true));

// クリックした画像を削除するフラグをセッション変数に代入
if (!empty($_SESSION['user_id']) && !empty($_POST['picDeleteFlg'])) :
  switch ($_POST['pushButton']) {
    case '画像１を削除':
      $_SESSION['pic1_delete'] = true;
      break;
    case '画像２を削除':
      $_SESSION['pic2_delete'] = true;
      break;
    case '画像３を削除':
      $_SESSION['pic3_delete'] = true;
      break;
  }
  debug('$_SESSIONの中身：' . print_r($_SESSION, true));
endif;

debug('＞ ＞ ＞ ＞ ＞　 Ajax　商品編集 処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');