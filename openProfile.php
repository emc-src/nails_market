<?php

//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　プロフィール　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

debug('$_GETの中身:' . print_r($_GET, true));

if (!empty($_GET['prof_p'])) :
  $u_id = $_GET['prof_p'];
  $dbProfData = (!empty(getUserOpenData($u_id)) ? getUserOpenData($u_id) : '');
  if (empty($dbProfData)) :
    debug('GETパラメータのprof_pに該当するユーザーが存在しません。');
    header("Location:home.php");
    exit();
  endif;
endif;

debug('$dbProfDataの中身：' . print_r($dbProfData, true));

$return_page = (!empty($_SESSION['return_link'])) ? $_SESSION['return_link'] : 'home.php';
unset($_SESSION['return_link']);

debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>

<?php
$siteTitle = 'プロフィール';
require('head.php');
?>

<body class="page-openProfile page-1colum">
  <!-- ヘッダー -->
  <?php
  require('header.php');
  ?>
  <!-- メインコンテンツ -->
  <div id="contents-wrap" class="site-width">
    <form action="" name="" method="post">
      <section id="main-wrap">
        <div class="form-wrap openProfile">
          <h1 class="title">プロフィール</h1>
          <div class="myData-wrap">
            <div class="avatar">
              <img src="<?php echo showImage($dbProfData['profPic']); ?>" alt="">
            </div>
            <h2><?php echo sanitize($dbProfData['subName']); ?></h2>
          </div>
          <div class="prof-comment">
            <label>
              <span>○ ○ ○ 自己紹介 ○ ○ ○</span>
              <pre><textarea name="comment" id="" cols="50" rows="20" maxlength="500" disabled><?php echo sanitize($dbProfData['profComment']); ?></textarea></pre>
            </label>
          </div>
        </div>

        <!-- トップページへのリンク -->
        <div class="link">
          <ul>
            <li><a href="<?php echo $return_page . appendGetParam(array('prof_p')); ?>">&lt;&ensp;前のページへ戻る</a>
            </li>
            <li><a href="home.php"><span class="fas fa-home"></span>&ensp;トップページへ</a></li>
          </ul>
        </div>
      </section>
    </form>
  </div>
  <?php
  require('footer.php');
  ?>