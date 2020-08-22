<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　退会　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');
$u_id = $_SESSION['user_id'];
$releaseCount = 0;  // 出品中の商品件数
$buyTranCount = 0;  // 購入中の商品件数

$isDelete_pdt = 0;  // 商品一覧
$isDelete_pdtMsg = 0; // 商品詳細ページコメント
$isDelete_favo = 0; // お気に入り
$isDelete_userId = 0; // ユーザーID



debug('$_POSTの中身：' . print_r($_POST, true));


if (!empty($_POST)) :
  debug('POST送信があります。');
  debug('$_POSTの中身：' . print_r($_POST, true));
  // DB接続
  try {
    $dbh = dbConnect();
    // 出品中の件数取得(売却済みは除く)
    $sql = 'SELECT count(*)
            FROM products
            WHERE sale_id = :u_id AND deal_flg IS NULL AND delete_flg = 0
            ';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data, '退会ページ');
    if ($stmt) :
      debug('出品中の件数取得成功。');
      $releaseCount = $stmt->fetch(PDO::FETCH_ASSOC);
      $releaseCount = $releaseCount['count(*)'];
    else :
      debug('出品中の件数を取得できませんでした。');
    endif;

    // 購入中の件数取得(by_idが一致かつ取引未完了の商品)
    $sql = 'SELECT count(*)
            FROM products
            WHERE buy_id = :u_id AND deal_flg IS NULL AND delete_flg = 0
            ';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data, '退会ページ');
    if ($stmt) :
      debug('出品中の件数取得成功。');
      $buyTranCount = $stmt->fetch(PDO::FETCH_ASSOC);
      $buyTranCount = $buyTranCount['count(*)'];
    else :
      debug('購入中の件数を取得できませんでした。');
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（商品詳細ページのメッセージ送信にて）：' . $e->getMessage());
    $err_msg['common'] = MSG99;
  }
  debug('$releaseCountの中身：' . print_r($releaseCount, true));
  debug('$buyTranCountの中身：' . print_r($buyTranCount, true));

  // 出品中と購入取引中の商品がない場合は退会
  if ($releaseCount == 0 && $buyTranCount == 0) :
    $isDelete_pdt = userIdIsFalse($u_id, 'products', 'sale_id');
    $isDelete_pdtMsg = userIdIsFalse($u_id, 'pdt_messages', 'send_id');
    $isDelete_favo = userIdIsFalse($u_id, 'favorites', 'user_id');
    $isDelete_userId = userIdIsFalse($u_id, 'users', 'id');

    debug('売却済み商品件数:' . print_r($isDelete_pdt, true));
    debug('商品詳細ページコメント:' . print_r($isDelete_pdtMsg, true));
    debug('ユーザーID件数:' . print_r($isDelete_userId, true));

    debug('お気に入り件数:' . print_r($isDelete_favo, true));

    // セッション削除してHOMEへ
    session_destroy();
    header("Location:home.php");
    exit();


  endif;

endif;







debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>


<?php
$siteTitle = '退会';
require('head.php');
?>

<body class="page-withdraw page-1colum">
  <!-- ヘッダー -->
  <?php
  require('header.php');
  ?>
  <!-- メインコンテンツ -->
  <div id="contents-wrap" class="site-width">
    <section id="main-wrap">
      <div class="form-wrap">
        <h1 class="title">退　会</h1>
        <!-- 入力フォーム -->
        <form action="" method="post" class="form" onsubmit="return submitCheck();">
          <div class="form-group">
          </div>
          <!-- 共通のエラーメッセージ -->
          <div class="withdraw-msg">
            <?php if ($releaseCount > 0) :
              echo '出品中の商品があります。退会する場合は出品を削除してください。';
            elseif ($buyTranCount > 0) :
              echo '購入した商品のお取引を完了してください。';
            endif;
            ?>
          </div>
          <!-- 退会ボタン -->
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" name="withdraw" value="退会する">
          </div>
        </form>
      </div>
      <!-- トップページへのリンク -->
      <div class="link">
        <a href=""><span class="fas fa-home"></span>&ensp;トップページへ</a>
      </div>
    </section>
  </div>
  <?php
  require('footer.php');
  ?>