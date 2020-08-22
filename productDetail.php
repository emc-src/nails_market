<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　商品詳細　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


define('NEWBUY', 'このたびはご購入いただきありがとうございます。\nとくにご指定がない場合はご登録のご住所へ発送いたします。');
define('NEWBUY02', 'とくにご指定がない場合はご登録のご住所へ発送いたします。');


$dbProductInfo = '';       // 商品情報
$sale_id = '';             // 販売者ID
$rom_id = '';              // 閲覧者ID
$same_id = false;          // 販売者と閲覧者が同じ場合はtrue
$msgCount = '';            // 商品の質問メッセージ件数
$getParamSeller = '';      // 出品者の閲覧用プロフィールページのGETパラメータ
$sellerInfo = array();     // 販売者情報
$romUserInfo = array();    // 閲覧者情報
$msg = array();            // 商品の質問メッセージすべて


$p_id = (!empty($_GET['p_id']) ? $_GET['p_id'] : '');
$_SESSION['p_id'] = $p_id;
$dbProductInfo = (!empty($p_id)) ? getProductRomFree($p_id) : '';
// GETパラメータ改ざん等、DBに該当商品IDがない場合
if (empty($dbProductInfo)) :
  debug('GETパラメータの商品IDに該当するデータがDBにありません。');
  debug('トップページへ遷移します。');
  header("Location:home.php");
  exit();
endif;

debug('$dbProductInfoの中身：' . print_r($dbProductInfo, true));

// 価格を変数に代入
$price = $dbProductInfo['price'];
$deli_cost = $dbProductInfo['deli_cost'];
$total_price = ($price + $deli_cost);

// 出品者情報取得
$sale_id = $dbProductInfo['sale_id'];
$sellerInfo = getUserOpenData($sale_id);
$sellerInfo = (!empty($sellerInfo)) ? $sellerInfo : array();

// 閲覧者情報取得
$rom_id = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';
$romUserInfo = getUserOpenData($rom_id);
$romUserInfo = (!empty($romUserInfo)) ? $romUserInfo : array();
$same_id = ($dbProductInfo['sale_id'] === $rom_id) ? true : false;
debug('出品者情報：' . print_r($sellerInfo, true));
debug('閲覧者情報：' . print_r($romUserInfo, true));

if (!empty($_SESSION['user_id'])) :
  $tranUser_flg = ($rom_id == $_SESSION['user_id'] || $sale_id == $_SESSION['user_id']) ? true : false;
endif;

// 閲覧者の確認
if (empty($rom_id)) :
  debug('未ログインユーザーです。');
else :
  if (empty($same_id)) :
    debug('出品者IDと閲覧者IDの比較：同一です。');
  else :
    debug('出品者IDと閲覧者IDの比較：別人です。');
  endif;
endif;

// POST送信した場合
if (!empty($_POST)) :
  // 未ログインユーザーによる不正なHTML編集による送信の場合はリダイレクト
  if (empty($rom_id)) :
    debug('未ログインユーザーによる不正なHTML編集がありました。(購入ボタンクリック)');
    header("Location:home.php");
    exit();
  endif;

  // ログインユーザーによる商品購入
  if (!empty($rom_id) && $_POST['push-submit'] === 'product-buy') :
    debug('購入ボタンがクリックされました。DBの商品情報を変更します。');
    try {
      $dbh = dbConnect();

      // ■ ■ ■ ■ お取引画面新規レコード挿入 ■ ■ ■ ■
      debug('購入商品のお取引画面新規レコードを追加します。');
      $sql = 'INSERT INTO transactions
              (product_id, sale_id, buy_id, delete_flg, create_date)
              VALUES
              (:p_id, :sale_id, :buy_id, 0, :date)
              ';
      $data = array(
        ':p_id' => $p_id, ':sale_id' => $sale_id, ':buy_id' => $rom_id, ':date' => date('Y-m-d H:i:s')
      );
      $stmt = queryPost($dbh, $sql, $data, '商品詳細画面 購入ボタンクリック');
      if ($stmt) :
        $t_id = $dbh->lastInsertId();   // 取引画面新規レコードID
        debug('お取引画面の新規レコード追加成功。');
      else :
        $err_msg['common'] = MSG99;
      endif;

      // ■ ■ ■ ■ 商品情報テーブルに購入者IDと取引IDを登録 ■ ■ ■ ■
      $sql = 'UPDATE products
              SET buy_id = :buy_id, tran_id = :tran_id
              WHERE id = :p_id
              LIMIT 1
             ';
      $data = array(':buy_id' => $rom_id, ':tran_id' => $t_id, ':p_id' => $p_id);
      $stmt = queryPost($dbh, $sql, $data, '商品詳細画面 購入ボタンクリック');
      if ($stmt) :
        debug('商品情報テーブルに購入者IDを登録しました。');
      else :
        $err_msg['common'] = MSG99;
      endif;

      // ■ ■ ■ ■ 新規用メッセージへレコード挿入 ■ ■ ■ ■
      debug('購入商品の新規用メッセージレコードを追加します。');
      $sql = 'INSERT INTO tran_messages
              (tran_id, send_id, msg, send_date, delete_flg, create_date)
              VALUES
              (:tran_id, :send_id, :msg, :send_date, 0, :date)
              ';
      $data = array(
        ':tran_id' => $t_id, ':send_id' => $sale_id, ':msg' => NEWBUY,
        ':send_date' => date('Y-m-d H:i:s'), ':date' => date('Y-m-d H:i:s')
      );
      $stmt = queryPost($dbh, $sql, $data, '商品詳細画面 新規用メッセージレコード挿入');
      // クエリ成功の場合
      if ($stmt) :
        debug('お取引メッセージに新規用メッセージレコード追加成功。');
        debug('お取引画面に遷移します。');
        $_SESSION['msg_success'] = SUC10;
        header("Location:transaction.php?t_id=" . $t_id . '&p_id=' . $p_id);
        exit();
      else :
        $err_msg['common'] = MSG99;
      endif;
    } catch (Exception $e) {
      error_log('例外エラー発生（商品詳細ページのメッセージ送信にて）：' . $e->getMessage());
      $err_msg['common'] = MSG99;
    }
  endif;

  // 質問メッセージ送信ボタンをクリックした場合(メッセージ空送信なし)

  if (($_POST['push-submit'] === 'send-msg') && !empty($_POST['comment'])) :
    $comment = $_POST['comment'];
    validHalfKana($comment, 'comment');
    validMaxLen($comment, 'comment', 1000);
    if (empty($err_msg)) :
      debug('メッセージをDBに登録します。');
      try {
        $dbh = dbConnect();
        $sql = 'INSERT INTO pdt_messages
              (product_id, send_id, send_date, msg, delete_flg, create_date)
              VALUES
              (:p_id, :send_id, :send_date, :msg, 0, :date)
              ';
        $data = array(
          ':p_id' => $p_id, ':send_id' => $rom_id, ':send_date' => date('Y-m-d H:i:s'),
          ':msg' => $comment, ':date' => date('Y-m-d H:i:s')
        );
        $stmt = queryPost($dbh, $sql, $data, '商品詳細ページ');
        // クエリ成功の場合、同じ画面を読み込む
        if ($stmt) :
          $_POST = array();
          debug('メッセージ登録成功。リロードします。');
          header("Location:productDetail.php?p_id=" . $p_id);
          exit();
        endif;
      } catch (Exception $e) {
        error_log('例外エラー発生（商品詳細ページのメッセージ送信にて）：' . $e->getMessage());
        $err_msg['common'] = MSG99;
      }
    endif;
  endif;
endif;

// 質問コメント取得
$msgCount = getMsgProduct($p_id, $recordCount = true);
$msg = getMsgProduct($p_id);
$msg = (!empty($msg)) ? $msg : array();
debug('$msgの中身：' . print_r($msg, true));

// GETパラメータ生成（出品者）
$getParamSeller = appendGetParam();
if (!empty($getParamSeller)) :
  $getParamSeller .= '&prof_p=' . $dbProductInfo['sale_id'];
else :
  $getParamSeller = '&prof_p=' . $dbProductInfo['sale_id'];
endif;
debug('$getParamSellerの中身：' . $getParamSeller);

// プロフィール画面から戻る用のリンク
$_SESSION['return_link'] = 'productDetail.php';

debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>

<!-- ■ ■ ■   ここからHTML   ■ ■ ■ -->

<?php
$siteTitle = '商品詳細';
require('head.php');
?>

<body class="page-productDetail page-2colum">
  <!-- - - - ヘッダー - - - -->
  <?php
  require('header.php');
  ?>

  <!-- JSアニメーション表示 -->
  <p id="js-show-msg" style="display: none" class="js-msg-slide">
    <?php echo getOnetimeSession('msg_success'); ?>
  </p>

  <div id="contents-wrap" class="site-width">
    <div id="product-wrap">
      <form action="" name="" method="post">

        <!-- - - - 商品データメイン - - - -->

        <?php if (!empty($tranUser_flg) && $dbProductInfo['deal_flg'] == 1) : ?>
        <p class="tran-end">こちらの商品はお取引が完了しています</p>
        <?php endif; ?>

        <section id="productData-wrap">
          <!-- - - - タイトルバー - - - -->
          <div class="title-bar">
            <span class="badge category"><?php echo sanitize($dbProductInfo['categoryName']); ?></span>
            <span class="badge condition"><?php echo sanitize($dbProductInfo['conditionName']); ?></span>
            <h1><?php echo sanitize($dbProductInfo['name']); ?></h1>
          </div>
          <!-- - - -データエリア（左）- - - -->
          <div id="data-left">
            <!-- - - - 画像エリア - - - -->
            <div class="pic-wrap">
              <div class="pic-main pic">
                <img src="<?php echo showImage($dbProductInfo['pic1']); ?>"
                  alt="<?php sanitize($dbProductInfo['name']);; ?>" class="js-switch-img-main">
              </div>
              <div class="picSub-wrap">
                <div class="pic-sub1 pic">
                  <img src="<?php echo showImage($dbProductInfo['pic1']); ?>"
                    alt="画像1：<?php echo sanitize($dbProductInfo['name']);; ?>" class="js-switch-img-sub">
                </div>
                <div class="pic-sub2 pic">
                  <img src="<?php echo showImage($dbProductInfo['pic2']); ?>"
                    alt="画像2：<?php echo sanitize($dbProductInfo['name']);; ?>" class="js-switch-img-sub">
                </div>
                <div class="pic-sub3 pic">
                  <img src="<?php echo showImage($dbProductInfo['pic3']); ?>"
                    alt="画像3：<?php echo sanitize($dbProductInfo['name']);; ?>" class="js-switch-img-sub">
                </div>
              </div>
              <div class="date">
                <p>出品日：<?php echo date('Y年m月d日　H時i分s秒', strtotime(sanitize($dbProductInfo['create_date']))); ?></p>
                <p>更新日：<?php echo date('Y年m月d日　H時i分s秒', strtotime(sanitize($dbProductInfo['update_date']))); ?></p>
              </div>

              <!-- - - - 出品者 - - - -->
              <div class="seller-wrap">
                <p class="seller-title">【 出品者 】</p>
                <a href="openProfile.php<?php echo $getParamSeller; ?>">
                  <p class="seller-name"><?php echo sanitize($dbProductInfo['sellerName']); ?></p>
                </a>
              </div>
            </div>
          </div>

          <!-- - - - データエリア（右）- - - -->
          <div id="data-right">
            <!-- - - -  お気に入り  - - - -->
            <?php if (!empty($_SESSION['user_id'])) : ?>
            <div class="favorite js-click-favo">
              <i class="fa fa-heart fa-2x icon-favorite js-favo <?php if (!empty($rom_id) && getFavoriteInfo($rom_id, $p_id)) : echo 'active';
                                                                  endif; ?>" aria-hidden="true"
                data-productid="<?php echo sanitize($p_id); ?>"></i><span class="js-favo-text">お気に入りに登録</span>
            </div>
            <?php endif; ?>
            <!-- - - -価格・送料 - - - -->
            <div class="detail-price-area">
              <div class="price-deli">
                <p class="price">価格&ensp;¥<?php echo sanitize(number_format($price)); ?></p>
                <p class="deli-cost">送料&ensp;¥<?php echo sanitize(number_format($deli_cost)); ?></p>
              </div>
              <div class="total-price">
                <img src="images/wallet-1.png"
                  alt=""><span>お支払い金額&emsp;¥<?php echo sanitize(number_format($total_price)); ?></span>
              </div>

              <!-- - - - 購入ボタン - - - -->
              <div class="btn buy-button">
                <p class="err-msg"><?php getErrMessage('common'); ?></p>

                <?php // 出品した商品ページの場合は「出品した商品です」を表示
                if (!empty($_SESSION['user_id']) && !empty($same_id)) :
                ?>
                <a class="to-detail js-to-detail" href="registProduct.php?p_id=<?php echo $p_id; ?>">
                  <p class="non-submit non-submit1 js-to-editPage">▶ 出品を編集する</p>
                </a>
                <?php endif; ?>

                <?php // 未ログインユーザーの場合はログイン要否を表示
                if (empty($_SESSION['user_id']) && empty($dbProductInfo['buy_id'])) :
                ?>
                <p class="non-submit non-submit2">ご購入には&ensp;<a href="login.php">ログイン</a><br />が必要です
                </p>
                <?php endif; ?>

                <?php // 販売済みでなく、かつ出品者ではなく、かつログインユーザーの場合は購入ボタンを表示
                if (empty($dbProductInfo['buy_id']) && empty($same_id) && !empty($_SESSION['user_id'])) :
                ?>
                <button type="submit" id="btn" name="push-submit" value="product-buy" onClick="return submitCheckOne()">
                  <i class="fas fa-shopping-cart"></i>&ensp;購入する
                </button>
                <?php endif; ?>
              </div>

              <?php // 購入した場合は「sold out」を表示
              if (!empty($dbProductInfo['buy_id'])) :
              ?>
              <p class="soldOut">sold out</p>
              <?php endif; ?>
            </div>

            <hr>

            <!-- - - - お支払い方法 - - - -->
            <div class="payment">
              <h2>【お支払い方法】</h2>
              <ul>
                <li>ゆうちょ銀行振込み</li>
                <li>LINE Pay お友達送金</li>
                <span class="note-1">※LINEでのお問い合わせは受け付けておりません。</br>&emsp;サイト内のメッセージボードをご利用ください。</span>
              </ul>
              <div class="note-2">
                <i class="fas fa-exclamation-circle fa-lg icon-note"></i>&thinsp;<span>ご入金確認後の発送となります</span>
              </div>
            </div>
          </div>
        </section>

        <section id="productComments-wrap">
          <!-- - - - 商品詳細コメント - - - -->
          <div class="detail-comment">
            <label>
              <span>商品詳細</span>
              <pre><textarea name="comment" id="" cols="50" rows="20" maxlength="500" disabled><?php echo sanitize(($dbProductInfo['detail'])); ?></textarea></pre>
            </label>
          </div>
        </section>

        <hr>

        <!-- - - - 質問メッセージ - - - -->
        <section id="message-wrap">
          <div class="message-container">
            <div class="title-wrap">
              <h2 class="tranMsg-title">商品についての質問</h2><span class="msg-count"><i
                  class="far fa-comment"></i>&ensp;<?php echo sanitize($msgCount); ?>件のコメント</span>
            </div>
            <div class="message-area" id="js-scroll-bottom">
              <?php if (empty($msgCount)) : ?>
              <P class="no-msg">コメントはありません</P>
              <?php endif; ?>
              <?php
              if (!empty($msg)) :
                foreach ($msg as $key => $val) :
                  if ($val['send_id'] === $sale_id) :

              ?>
              <!-- - - - 質問メッセージ（左）- - - -->
              <div class="msg-left msg-common">
                <div class="avatar">
                  <img src="<?php echo showImage($sellerInfo['profPic']); ?>" alt="">
                </div>
                <pre><p class="msg-text"><span class="triangle"></span><?php echo str_replace('\n', '<br />', sanitize($val['msg'])); ?></p></pre>
                <span class="send-date">出品者 / <a
                    href="openProfile.php<?php echo $getParamSeller; ?>"><?php echo sanitize($sellerInfo['subName']); ?></a>&ensp;：&ensp;コメント送信日&ensp;<?php echo sanitize($val['send_date']); ?></span>
              </div>

              <?php else : ?>
              <!-- - - - 質問メッセージ（右）- - - -->
              <div class="msg-right msg-common">
                <div class="avatar">
                  <img src="<?php echo showImage($val['profPic']); ?>" alt="">
                </div>
                <pre><p class="msg-text"><?php echo sanitize($val['msg']); ?><span class="triangle"></span></p></pre>
                <span class="send-date"><a
                    href="openProfile.php<?php if (!empty(appendGetParam())) : echo appendGetParam() . '&prof_p=' . $val['send_id'];
                                                                      endif; ?>"><?php echo sanitize($val['subName']); ?></a>&ensp;：&ensp;コメント送信日&ensp;<?php echo sanitize($val['send_date']); ?></span>
              </div>
              <?php endif;
                  ?>
              <?php endforeach;
                ?>
              <?php endif; ?>
            </div>

            <!-- - - - メッセージ送信欄 - - - -->
            <?php if (!empty($rom_id) && (empty($dbProductInfo['buy_id']))) : ?>
            <div class="send-message js-counter">
              <label>
                <span class="title">コメント送信欄</span><span> （200文字以内）</span>
                <pre><textarea name="comment" id="js-count" class="js-input-cmt" cols="50" rows="5" maxlength="200"></textarea></pre>
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('comment'); ?>
              </div>
              <p class="js-text-counter"><span id="js-count-view">0</span>/200文字</p>
            </div>
            <?php endif; ?>

            <!-- - - - メッセージ送信ボタン - - - -->
            <?php if (!empty($rom_id) && (empty($dbProductInfo['buy_id']))) : ?>
            <div class="btn-container btn-msgQuAn">
              <button type="submit" class="js-btn-cmt" name="push-submit" value="send-msg">コメントを送信する</button>
            </div>
            <?php endif; ?>
          </div>
        </section>
    </div>

    <!-- - - - トップページへのリンク - - - -->
    <section>
      <div class="link">
        <ul>
          <?php debug('$_GETの中身：' . print_r($_GET, true)); ?>

          <?php if ((!empty($_GET['p_id']) && (empty($_GET['t_id'])) && (!empty($_GET['p_id']) && empty($_GET['his'])))) :  ?>

          <li><a href="<?php if (!empty($_GET['p'])) : echo 'home.php?p=' . $_GET['p'];
                          else : echo 'home.php';
                          endif; ?> "><i class="fas fa-angle-double-left"></i>&ensp;前のページへ戻る</a>
          </li>
          <?php elseif (!empty($_GET['t_id'])) :  ?>
          <li><a href="transaction.php?t_id=<?php echo $_GET['t_id'] . '&p_id=' . $p_id; ?>">
              <i class="fas fa-angle-double-left"></i>&ensp;前のページへ戻る
            </a>
          </li>
          <?php elseif (!empty($_GET['his'])) :  ?>
          <li><a href="history.php?his=<?php echo $_GET['his']; ?>"><i
                class="fas fa-angle-double-left"></i>&ensp;前のページへ戻る</a>
          </li>

          <?php endif; ?>

          <li><a href="home.php"><span class="fas fa-home"></span>&ensp;トップページへ</a></li>
        </ul>
      </div>
    </section>
  </div>
  <?php
  require('footer.php');
  ?>