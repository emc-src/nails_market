<?php

//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　出品・購入一覧　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

define('NO_MSG', 'はありません。');

// ログイン認証
require('auth.php');

debug('$_GETの中身：' . print_r($_GET, true));

$u_id = $_SESSION['user_id'];
$myHistoryInfo = '';    // 販売・購入情報
$no_message = '';       // 検索結果が０の場合の表示メッセージ
$select_index = '出品・購入情報';     // 選択した表示項目

if (!empty($_GET['his'])) :
  switch ($_GET['his']):
      // お取引中：出品した商品
    case '1':
      $myHistoryInfo =  getHistory($u_id, 'sale', true, false, false);
      $select_index = 'お取引中の出品した商品';
      break;
      // お取引中：購入した商品
    case '2':
      $myHistoryInfo =  getHistory($u_id, 'buy', true, false, false);
      $select_index = 'お取引中の購入した商品';
      break;
      // 出品・出品準備中：出品中の商品
    case '3':
      $myHistoryInfo =  getHistory($u_id, 'sale', false, false, false);
      $select_index = '出品中の商品';
      break;
      // 出品・出品準備中：下書きの商品
    case '4':
      $myHistoryInfo =  getHistory($u_id, 'sale', false, true, false);
      $select_index = '出品準備中（下書き）の商品';
      break;
      // 取引完了の商品：売却済みの商品
    case '5':
      $myHistoryInfo =  getHistory($u_id, 'sale', true, false, true);
      $select_index = '売却済みの商品';
      break;
      // 取引完了の商品：購入済みの商品
    case '6':
      $myHistoryInfo =  getHistory($u_id, 'buy', true, false, true);
      $select_index = '購入済みの商品';
      break;
      // お気に入り登録
    case '7':
      $myHistoryInfo = getFavoriteList($u_id);
      $select_index = 'お気に入り登録中の商品';
      break;
      // GETパラメータ改ざんの場合
    default:
      debug('GETパラメータに不正な入力がありました。');
      debug('$_GET[his]の中身：' . $_GET['his']);
      header("Location:mypage.php");
      exit();
  endswitch;
endif;
// debug('$myHistoryInfoの中身：' . print_r($myHistoryInfo, true));
debug('データの件数：' . count($myHistoryInfo));
// 出品・購入履歴情報がない場合はメッセージを変数に代入
$no_message = (empty($myHistoryInfo)) ? $select_index . NO_MSG : '';
// debug('$no_messageの中身：' . $no_message);


debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>


<?php
$siteTitle = '出品・購入情報';
require('head.php');
?>

<body class="page-history page-1colum">
  <!-- ヘッダー -->
  <?php
  require('header.php');
  ?>

  <!-- メインコンテンツ -->
  <div id="contents-wrap" class="site-width">
    <section id="main-wrap">
      <div class="form-wrap">
        <h1 class="title"><?php echo sanitize($select_index); ?></h1>
        <?php // 表示する情報がある場合は件数とメッセージを表示
        if (!empty($myHistoryInfo)) :
        ?>
          <p class="p_count">
            <?php echo sanitize(count($myHistoryInfo)) . '件&thinsp;の情報があります'; ?></p>
        <?php endif; ?>

        <div class="history-wrap">
          <?php // 表示する情報がない場合は情報なしのメッセージを表示
          if (!empty($no_message)) :
          ?>
            <p class="no-list"><?php echo sanitize($no_message); ?></p>
          <?php endif; ?>

          <div class="list-wrap" style="<?php if (empty($myHistoryInfo)) : echo 'border-top:none;';
                                        endif; ?>">
            <?php foreach ($myHistoryInfo as $key => $val) : ?>
              <ul>
                <li class="tran-container">
                  <a href="<?php if ($_GET['his'] == 1 || $_GET['his'] == 2) : echo 'transaction.php?t_id=' . sanitize($val['tran_id']) . '&his=' . $_GET['his'];
                            else : echo 'productDetail.php?p_id=' . sanitize($val['id']) . '&his=' . $_GET['his'];
                            endif; ?>">
                    <div class="p_pic">
                      <img src="<?php echo showImage($val['pic1']); ?>" alt="">
                    </div>
                    <p><?php echo sanitize($val['name']); ?></p>
                  </a>
                </li>
              </ul>
            <?php endforeach; ?>
          </div>
        </div>


      </div>
      <!-- トップページへのリンク -->
      <div class="link">
        <ul>
          <li><a href="mypage.php"><i class="fas fa-user"></i>&ensp;マイページへ戻る</a></li>
          <li><a href="home.php"><i class="fas fa-home"></i>&ensp;トップページへ</a></li>
        </ul>
      </div>
    </section>

  </div>
  <?php
  require('footer.php');
  ?>