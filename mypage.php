<?php

//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

$u_id = $_SESSION['user_id'];

// ユーザー情報取得
$myUserData = getUser($u_id);
debug('DB情報取得：Myユーザー情報（$myUserDataの中身）：' . print_r($myUserData, true));

debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>


<?php
$siteTitle = 'マイページ';
require('head.php');
?>

<body class="page-mypage page-2colum">
  <!-- ヘッダー -->
  <?php
  require('header.php');
  ?>
  <!-- JSアニメーション表示 -->
  <p id="js-show-msg" style="display: none" class="js-msg-slide">
    <?php echo getOnetimeSession('msg_success'); ?>
  </p>

  <!-- メインコンテンツ -->
  <div id="contents-wrap" class="site-width">
    <div class="main-side-wrap">
      <section id="main">
        <div class="form-wrap">
          <h1>マイページ</h1>
          <div class="myData-wrap mypage-mydata">
            <a href="profile.php">
              <div class="avatar">
                <img src="<?php echo showImage($myUserData['profPic']) ?>" alt="">
              </div>
              <h2><?php echo sanitize($myUserData['subName']); ?></h2>
            </a>
          </div>
          <div class="group-wrap transaction">
            <h2 class="title">お気に入りの商品</h2>
            <div class="page-other">
              <a href="history.php?his=7">お気に入り登録中の商品</a>
            </div>
          </div>

          <div class="group-wrap transaction">
            <h2 class="title">お取引中の商品</h2>
            <div class="page-other">
              <a href="history.php?his=1">出品した商品（お取引中）</a>
              <a href="history.php?his=2">購入した商品（お取引中）</a>
            </div>
          </div>
          <div class="group-wrap for-sale">
            <h2 class="title">出品・出品準備中の商品</h2>
            <div class="page-other">
              <a href="history.php?his=3">出品中の商品</a>
              <a href="history.php?his=4">出品準備中（下書き）の商品</a>
            </div>
          </div>
          <div class="group-wrap history">
            <h2 class="title">取引完了の商品</h2>
            <div class="page-other">
              <a href="history.php?his=5">販売済みの商品</a>
              <a href="history.php?his=6">購入済みの商品</a>
            </div>
          </div>
          <div class="group-wrap my-data">
            <h2 class="title">会員情報</h2>
            <div class="page-other">
              <a href="signup.php">会員情報の変更</a>
              <a href="profile.php">プロフィール編集</a>
            </div>
          </div>
        </div>
      </section>

      <!-- サイドバー（右） -->
      <?php
      require('sidebar_mypage.php');
      ?>
    </div>

    <!-- トップページへのリンク -->
    <section>
      <div class="link">
        <a href="home.php"><span class="fas fa-home"></span>&ensp;トップページへ</a>
      </div>
    </section>
  </div>
  <?php
  require('footer.php');
  ?>