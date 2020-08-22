<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　お取引画面　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

$dbProductInfo = '';       // 商品情報
$sellerInfo = '';          // 出品者情報
$buyerInfo = '';           // 購入者情報
$msgCount = '';            // 取引画面の質問メッセージ件数
$dbTranData = array();     // 取引画面情報
$msg = array();            // 取引画面の質問メッセージすべて

$pay_flg = NULL;           // 入金フラグ
$get_flg = NULL;           // 商品受領フラグ
$deal_flg = NULL;          // 取引完了フラグ

$pay_date = NULL;           // 入金確認日
$get_date = NULL;           // 商品受領日
$dealEnd_date = NULL;       // 取引完了日

$u_id = $_SESSION['user_id'];
$t_id = (!empty($_GET['t_id']) ? $_GET['t_id'] : '');
$dbTranData = (!empty($t_id)) ? getTransaction($t_id) : array();
// GETパラメータ改ざんの場合
if (empty($dbTranData)) :
  debug('GETパラメータの取引画面IDの情報がDBにありません。');
  debug('マイページへ遷移します。');
  header("Location:mypage.php");
  exit();
endif;

$p_id = $dbTranData['product_id'];
$dbProductInfo = getProductRomFree($p_id);
debug('dbTranDataの中身：' . print_r($dbTranData, true));
debug('$dbProductInfoの中身：' . print_r($dbProductInfo, true));
// ユーザーIDが取引商品のユーザーIDに該当しない場合
if (!($u_id === $dbProductInfo['sale_id'] || $u_id === $dbProductInfo['buy_id'])) :
  debug('出品者でも購入者でもないIDです。マイページへ遷移します。');
  header("Location:mypage.php");
  exit();
endif;

// 価格を変数に代入
$price = $dbProductInfo['price'];
$deli_cost = $dbProductInfo['deli_cost'];
$total_price = ($price + $deli_cost);

// メッセージ、出品者、購入者情報を取得
$msgCount = getMegTransaction($t_id, $recordCount = true);
$msg = getMegTransaction($t_id);
$sellerInfo = getUser($dbProductInfo['sale_id']);
$buyerInfo = getUser($dbProductInfo['buy_id']);
$seller_id = $sellerInfo['id'];
$buyer_id = $buyerInfo['id'];
debug('$msgCountの中身：' . print_r($msgCount, true));
debug('$msgの中身：' . print_r($msg, true));
debug('$sellerInfo中身：' . print_r($sellerInfo, true));
debug('$buyerInfoの中身：' . print_r($buyerInfo, true));

// セッション変数に代入
$_SESSION['t_id'] = $t_id;
$_SESSION['seller_id'] = $seller_id;
$_SESSION['buyer_id'] = $buyer_id;


// お取引状況のデータを変数に代入
$pay_flg = (!empty($dbProductInfo['pay_flg'])) ? $dbProductInfo['pay_flg'] : NULL;
$get_flg = (!empty($dbProductInfo['get_flg'])) ? $dbProductInfo['get_flg'] : NULL;
$deal_flg = (!empty($dbProductInfo['deal_flg'])) ? $dbProductInfo['deal_flg'] : NULL;
$pay_date = (!empty($dbProductInfo['pay_date'])) ?  sanitize(date('Y年m月d日　H:i:s', strtotime($dbProductInfo['pay_date']))) : NULL;
$get_date = (!empty($dbProductInfo['get_date'])) ? sanitize(date('Y年m月d日　H:i:s', strtotime($dbProductInfo['get_date']))) : NULL;
$dealEnd_date = (!empty($dbProductInfo['dealEnd_date'])) ? sanitize(date('Y年m月d日　H:i:s', strtotime($dbProductInfo['dealEnd_date']))) : NULL;
debug('$pay_flgの中身：' . print_r($pay_flg, true));
debug('$get_flgの中身：' . print_r($get_flg, true));
debug('$deal_flgの中身：' . print_r($deal_flg, true));


// POST送信した場合
if (!empty($_POST)) :

  // メッセージ送信ボタンクリックの場合
  if (($_POST['push-submit'] === 'send-msg') && !empty($_POST['comment'])) :
    $comment = $_POST['comment'];
    validHalfKana($comment, 'comment');
    validMaxLen($comment, 300);
    if (empty($err_msg)) :
      debug('メッセージをDBに登録します。');
      try {
        $dbh = dbConnect();
        $sql = 'INSERT INTO tran_messages
                (tran_id, send_id, msg, send_date, delete_flg, create_date)
                VALUES
                (:tran_id, :send_id, :msg, :send_date, 0, :date)
              ';
        $data = array(
          ':tran_id' => $t_id, ':send_id' => $u_id, ':msg' => $comment,
          ':send_date' => date('Y-m-d H:i:s'), ':date' => date('Y-m-d H:i:s')
        );
        $stmt = queryPost($dbh, $sql, $data, 'お取引画面メッセージ送信');
        // クエリ成功の場合、同じ画面を読み込む
        if ($stmt) :
          $_POST = array();
          debug('メッセージ登録成功。リロードします。');
          header("Location:transaction.php?t_id=" . $t_id);
          exit();
        endif;
      } catch (Exception $e) {
        error_log('例外エラー発生（お取引画面のメッセージ送信にて）：' . $e->getMessage());
        $err_msg['common'] = MSG99;
      }
    endif;
  endif;
endif;

$_SESSION['return_link'] = 'transaction.php';

debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>


<?php
$siteTitle = '取引画面';
require('head.php');
?>

<body class="page-transaction page-2colum">
  <!-- ヘッダー -->
  <?php
  require('header.php');
  ?>

  <!-- JSアニメーション表示 -->
  <p id="js-show-msg" style="display: none" class="js-msg-slide">
    <?php echo getOnetimeSession('msg_success'); ?>
  </p>

  <div id="contents-wrap" class="site-width">
    <div id="transaction-wrap">
      <div class="mainTitle-bar">
        <h1>お取引画面</h1>
      </div>
      <form action="" name="" method="post">
        <!-- 取引情報 -->
        <section id="tranProductData-wrap">
          <!-- タイトルバー -->
          <!-- データエリア（左） -->
          <div id="tranData-left" class="tran-data">
            <h2>商品情報</h2>
            <div class="p-data data-group">
              <p>【商品名】</p>
              <p class="data p_name"><a
                  href="<?php echo 'productDetail.php?p_id=' . $p_id . '&t_id=' . $t_id; ?>"><?php echo sanitize($dbProductInfo['name']) ?></a>
              </p>
              <p>【カテゴリ】</p>
              <p class="data"><?php echo sanitize($dbProductInfo['categoryName']) ?></p>
              <p>【商品の状態】</p>
              <p class="data"><?php echo sanitize($dbProductInfo['conditionName']) ?>
              </p>
              <p>【お取引開始日】<?php echo sanitize(date('Y年m月d日', strtotime($dbTranData['create_date']))) ?></p>
              <div class="p-price">
                <div class="total-price">
                  <span>【販売価格】¥</span><?php echo sanitize(number_format($total_price)); ?><span></span>
                </div>
                <div class="price-detail">
                  <span>&ensp;（商品価格：¥<?php echo sanitize(number_format($price)); ?></span><span>&emsp;送料：¥<?php echo sanitize(number_format($deli_cost)); ?>）</span>
                </div>
              </div>
            </div>

            <!-- 画像エリア -->
            <div class="pic-wrap">
              <div class="product-pic">
                <img src="<?php echo sanitize($dbProductInfo['pic1']) ?>"
                  alt="<?php echo sanitize($dbProductInfo['name']) ?>">
              </div>
            </div>
          </div>

          <!-- データエリア（左） -->
          <div id="tranData-right" class="tran-data">
            <!-- 販売者情報 -->
            <div class="seller data-group">
              <div class="sub-name">
                <span class="badge">出品者</span>
                <h2><?php echo sanitize($sellerInfo['subName']); ?></h2>
              </div>
              <p class="u-name">【名前】<?php echo sanitize($sellerInfo['userName']); ?></p>
              <p>【住所】</p>
              <p class="data">
                〒<?php echo sanitize($sellerInfo['zip']); ?></br>
                <?php echo sanitize($sellerInfo['address1']); ?></br>
                <?php echo sanitize($sellerInfo['address2']); ?></br>
              </p>
            </div>
            <hr>
            <!-- 購入者情報 -->
            <div class="buyer data-group">
              <div class="sub-name">
                <span class="badge">購入者</span>
                <h2><?php echo sanitize($buyerInfo['subName']); ?></h2>
              </div>
              <p class="u-name">【名前】<?php echo sanitize($buyerInfo['userName']); ?>&ensp;様</p>
              <p>【住所】</p>
              <p class="data">
                〒<?php echo sanitize($buyerInfo['zip']); ?></br>
                <?php echo sanitize($buyerInfo['address1']); ?></br>
                <?php echo sanitize($buyerInfo['address2']); ?></br>
              </p>
            </div>
          </div>
        </section>

        <!-- 現在のお取引状況 -->
        <section id="progress">
          <div class="progress-wrap">
            <div class="title-bar">
              <h2>【現在のお取引状況】</h2>
              <i class="fas fa-arrow-alt-circle-down fa-lg icon-point" style="display: none"></i><span
                class="comment">現在の状況を入力してください</span>
            </div>

            <table class="progress-table">
              <tr>
                <td>ご入金の確認</td>
                <td
                  class="js-payment-ok prog-input <?php if ((!empty($pay_flg) && (int) $pay_flg === 1)) echo 'entered-payment'; ?>">
                  <?php if (empty($pay_flg)) : // 入金フラグがfalseの場合はフォントアイコンを表示
                  ?>
                  <i class="fas fa-arrow-alt-circle-right fa-lg js-icon-click1 icon-click"></i>
                  <?php endif; ?>
                  <?php if (empty($pay_flg)) : echo 'クリックして入力（出品者入力）';
                  elseif ((!empty($pay_flg) && (int) $pay_flg === 1)) : echo '出品者がご入金を確認しました';
                  endif; ?>
                </td>
                <td class="create-date1"><?php if (empty($pay_date)) : echo '更新日：ー ー ー ー ー ー';
                                          elseif (((!empty($pay_flg) && (int) $pay_flg === 1))) : echo  '更新日：' . $pay_date;
                                          endif; ?></td>
              </tr>
              <tr>
                <td>商品受領の確認</td>
                <td
                  class="js-product-get prog-input <?php if ((!empty($get_flg) && (int) $get_flg === 1)) echo 'entered-getProduct'; ?>">
                  <?php if (empty($get_flg)) : // 商品受領フラグがfalseの場合はフォントアイコンを表示
                  ?>
                  <i class="fas fa-arrow-alt-circle-right fa-lg js-icon-click1 icon-click"></i>
                  <?php endif; ?>
                  <?php if (empty($get_flg)) : echo 'クリックして入力（購入者入力）';
                  elseif ((!empty($get_flg) && (int) $get_flg === 1)) : echo '購入者が商品を受け取りました';
                  endif; ?>
                </td>
                <td class="create-date2"><?php if (empty($get_date)) : echo '更新日：ー ー ー ー ー ー';
                                          elseif ((!empty($get_flg) && (int) $get_flg === 1)) : echo '更新日：' . $get_date;
                                          endif; ?></td>
              </tr>
              <tr>
                <td>お取引完了確認</td>
                <td
                  class="js-tran-end prog-input <?php if ((!empty($deal_flg) && (int) $deal_flg === 1)) echo 'entered-tranEnd'; ?>">

                  <?php if (empty($deal_flg)) : // 取引完了フラグがfalseの場合はフォントアイコンを表示
                  ?>
                  <i class="fas fa-arrow-alt-circle-right fa-lg js-icon-click1 icon-click"></i>
                  <?php endif; ?>
                  <?php if (empty($deal_flg)) : echo 'クリックして入力（出品者入力）';
                  elseif ((!empty($deal_flg) && (int) $deal_flg === 1)) : echo '出品者がお取引を完了しました';
                  endif; ?>
                </td>

                <td class="create-date3"><?php if (empty($dealEnd_date)) : echo '更新日：ー ー ー ー ー ー';
                                          elseif ((!empty($pay_flg) && (int) $pay_flg === 1 && !empty($deal_flg))) : echo '更新日：' . $dealEnd_date;
                                          endif; ?></td>
              </tr>
            </table>
          </div>
        </section id="conclude">

        <section>
          <?php // 取引完了フラグがtrueの場合は表示
          if (!empty($dbProductInfo['deal_flg'])) :
          ?>
          <p class="conclude-msg">こちらの商品のお取引は完了しました</p>
          <?php endif; ?>
        </section>

        <!-- - - -  取引メッセージ  - - - -->
        <section id="message-wrap">
          <div class="message-container">

            <div class="title-wrap">
              <h2 class="tranMsg-title">お取引メッセージ</h2><span class="msg-count"><i
                  class="far fa-comment"></i>&ensp;<?php echo sanitize($msgCount); ?>件のメッセージ</span>
            </div>

            <div class="message-area js-scroll-bottom">
              <?php // メッセージが０件の場合は表示
              if (empty($msgCount)) :
              ?>
              <P class="no-msg">">メッセージはありません</P>
              <?php endif; ?>

              <?php // メッセージ読み込み
              foreach ($msg as $key => $val) :
              ?>
              <!-- - - -  取引メッセージ（左）  - - - -->
              <?php if ($val['send_id'] === $seller_id) : ?>

              <div class="msg-left msg-common">
                <div class="avatar">
                  <img src="<?php echo showImage($sellerInfo['profPic']); ?>" alt="">
                </div>
                <pre><p class="msg-text"><span class="triangle"></span><?php echo str_replace('\n', '<br />', sanitize($val['msg'])); ?></p></pre>
                <span class="send-date"><a
                    href="openProfile.php<?php if (!empty(appendGetParam())) : echo appendGetParam() . '&prof_p=' . $seller_id;
                                                                    endif; ?>"><?php echo sanitize($sellerInfo['subName']); ?></a>&ensp;：&ensp;メッセージ送信日&ensp;<?php echo sanitize($val['send_date']); ?></span>
              </div>

              <?php elseif ($val['send_id'] === $buyer_id) : ?>
              <!-- - - -  取引メッセージ（右）  - - - -->
              <div class="msg-right msg-common">
                <div class="avatar">
                  <img src="<?php echo showImage($buyerInfo['profPic']); ?>" alt="">
                </div>
                <pre><p class="msg-text"><?php echo sanitize($val['msg']); ?><span class="triangle"></span></p></pre>
                <span class="send-date"><a
                    href="openProfile.php<?php if (!empty(appendGetParam())) : echo appendGetParam() . '&prof_p=' . $buyer_id;
                                                                    endif; ?>"><?php echo sanitize($buyerInfo['subName']); ?></a>&ensp;：&ensp;メッセージ送信日&ensp;<?php echo sanitize($val['send_date']); ?></span>
              </div>

              <?php endif; ?>
              <?php endforeach; ?>
            </div>

            <!-- - - -  メッセージ送信欄  - - - -->
            <?php if (empty($deal_flg)) : ?>
            <div class="send-message js-counter">
              <label>
                <span class="title">お取引メッセージ送信欄</span><span> （300文字以内）</span>
                <pre><textarea name="comment" id="js-count" class="js-input-msg" cols="50" rows="6" maxlength="300"></textarea></pre>
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('comment'); ?>
              </div>
              <p class="js-text-counter"><span id="js-count-view">0</span>/300文字</p>
            </div>
            <?php endif; ?>

            <!-- - - -  メッセージ送信ボタン  - - - -->
            <?php if (empty($deal_flg)) : ?>
            <div class="btn-container btn-msgSend">
              <button type="submit" class="js-btn-msg" name="push-submit" value="send-msg">メッセージを送信する</button>
            </div>
            <?php endif; ?>
          </div>
        </section>
      </form>
    </div>

    <!-- トップページへのリンク -->
    <section>
      <div class="link">
        <ul>
          <?php if (!empty($_GET['his'])) :  ?>
          <li><a href="history.php?his=<?php echo $_GET['his']; ?>"><i
                class="fas fa-angle-double-left"></i>&ensp;前のページへ戻る</a>
          </li>

          <?php elseif (!empty($_GET['p_id'])) :  ?>
          <li><a href="productDetail.php?p_id=<?php echo $_GET['p_id'] . '&t_id=' . $t_id; ?>"><i
                class="fas fa-angle-double-left"></i>&ensp;前のページへ戻る</a>
            <?php endif; ?>
          </li>
          <li><a href="home.php"><i class="fas fa-home"></i>&ensp;トップページへ</a></li>
        </ul>
      </div>
    </section>
  </div>
  <?php
  require('footer.php');
  ?>