<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　インフォメーションページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if (isset($_SESSION)) :
  debug('$_SESSIONの中身：' . print_r($_SESSION, true));
endif;

if (isset($_SESSION['msg_info'])) :
  $msg_info = $_SESSION['msg_info'];
else :
  header("Location:home.php");
// $msg_info = INFO1;
endif;



// 未ログインユーザーによる購入ボタンクリックの場合
// 下記をリンク元に書く
// if (empty($rom_id) && $_POST['push-submit'] === 'product-buy') :
//   debug('未ログインユーザーによる購入ボタンクリック。');
//   debug('インフォメーションページへ遷移します。');
//   $_SESSION['msg_info'] = INFO5;
//   $_SESSION['link_info'] = 'login.php';
//   $_SESSION['pageName_info'] = '&ensp;ログインページへ';
//   header("Location:information.php");
//   exit();



debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>


<?php
$siteTitle = 'インフォメーション';
require('head.php');
?>

<body class="page-info page-1colum">
  <!-- ヘッダー -->
  <?php
  require('header.php');
  ?>
  <!-- メインコンテンツ -->
  <div id="contents-wrap" class="site-width">
    <section id="main-wrap">
      <div class="info-wrap form-wrap">
        <h1 class="title">information</h1>
        <p><?php echo $msg_info; ?></p>
        <div class="to-topPage">
          <?php if (isset($_SESSION['link_info']) && isset($_SESSION['pageName_info'])) : ?>
            <a href="<?php echo $_SESSION['link_info']; ?>"><i class="fas fa-angle-double-left"></i><?php echo $_SESSION['pageName_info']; ?></a>
          <?php else : ?>
            <a href="home.php"><i class="fas fa-angle-double-left"></i><span class="fas fa-home"></span>&ensp;トップページへ</a>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </div>
  <?php
  unset($_SESSION['msg_info']);
  unset($_SESSION['link_info']);
  unset($_SESSION['pageName_info']);
  ?>
  <?php
  require('footer.php');
  ?>