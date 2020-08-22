<?php

//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

$currentPage = '';        // 現在のページ番号
$currentPageHead = '';    // 現在のページの最小値
$category = '';           // selectボックスで選択したname(カテゴリ)のvalueの値
$sort = '';               // selectボックスで選択したname(検索順序)のvalueの値


$myInfo = array();        // プロフィールエリアのデータ
$searchWord = '';         // 検索ワード


$_SESSION['category'] = '';

$u_id = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';
$myInfo = (!empty($u_id)) ? getUserOpenData($u_id) : array();
debug('$myInfoの中身(空の場合は未ログインユーザー)：' . print_r($myInfo, true));

// 現在のページ番号にGETパラメータを代入。空の場合はデフォルトの１を代入
$currentPage = (!empty($_GET['p'])) ? $_GET['p'] : 1;
$currentPage = (int) $currentPage;
// int型にキャストした結果が０の場合（整数以外）はGETパラメータにデフォルトの１を -
// - 代入してトップページへリダイレクト
if ($currentPage === 0) :
  error_log('エラー発生：URLに不正な入力がありました。');
  $_GET['p'] = 1;
  header("Location:home.php");
  exit();
endif;

$url = empty($_SERVER["HTTPS"]) ? "http://" : "https://";
$url .= $_SERVER["REQUEST_URI"];


// GETパラメータを代入
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
$notSoldOut = (!empty($_GET['nt_soldout'])) ? $_GET['nt_soldout'] : '';
$notSoldOutSw = (!empty($_GET['nt_soldout_sw'])) ? $_GET['nt_soldout_sw'] : '';
$sch_radio = (!empty($_GET['sch_andor'])) ? $_GET['sch_andor'] : 1;

debug('$_GETの中身：' . print_r($_GET, true));


// 検索条件で検索の場合
if (!empty($_GET['push-submit']) && $_GET['push-submit'] === 'sch-condition') :
  $btn = 'sch-condition';
// フリーワード検索の場合
elseif (!empty($_GET['push-submit']) && $_GET['push-submit'] === 'sch-word') :
  $btn = 'sch-word';
  $searchWords = $_GET['free-word'];
  // 検索ワードの全角スペースを半角に置き換え後、配列化する
  if (!empty($searchWords)) :
    $searchWords = str_replace('　', ' ', $searchWords);
    $arraySchWords = [];
    $arraySchWords = explode(' ', $searchWords);
    debug('$arraySchWordsの中身：' . print_r($arraySchWords, true));
  endif;
else :
  // 不要かも
  $btn = '';
endif;

// １ページに表示する件数
$viewCount = 20;
// 現在のページに表示するレコードの先頭番号を算出
// 🌟 例 / １ページ目の場合、(1-1)*20 = 0, ページ目の場合、(2-1)*20 = 2

$currentPageHead = (($currentPage - 1) * $viewCount);
// フリーワード検索の場合
if (!empty($arraySchWords)) :
  $dbProductList = getProductListSchWord($currentPageHead, $arraySchWords, $sch_radio, $notSoldOutSw, $viewCount);
// 条件検索の場合
else :
  $dbProductList = getProductList($currentPageHead, $category, $sort, $notSoldOut, $viewCount);
endif;
$dbCategoryData = getCategory();

// パラメータに検索結果以上のページ数を指定した場合はリダイレクト
if (!empty($_GET['p']) && $dbProductList['total_page'] < $_GET['p']) :
  debug('パラメータ不正操作：総ページ数を超える入力がありました。');
  header("Location:home.php");
  exit();
endif;


// debug('$dbProductListの中身：' . print_r($dbProductList, true));
// debug('$dbCategoryDataの中身：' . print_r($dbCategoryData, true));

debug('POST送信はなし。GET送信のみ。');




debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>


<?php
$siteTitle = 'HOME';
require('head.php');
?>
<!-- カラム構造のクラスを指定 -->

<body class="page-home page-2colum">
  <!-- ヘッダー -->
  <?php
  require('header.php');
  ?>
  <!-- メインコンテンツ -->
  <div id="contents-wrap" class="site-width">
    <form action="" name="" method="get" class="side-left">

      <!-- 左サイドバー（検索欄） -->
      <section id="sidebar-left" class="sidebar">

        <!-- - - -  プロフィールエリア（ログインユーザーの場合は表示 ）- - - -->
        <?php if (!empty($u_id)) : ?>
        <div class="profile-wrap">
          <a href="profile.php">
            <div class="img-wrap">
              <img src="<?php if (!empty($myInfo)) echo showImage($myInfo['profPic']); ?>" alt="">
            </div>
            <p class="sub-name">
              <?php if (!empty($myInfo)) echo $myInfo['subName']; ?>
            </p>
          </a>
        </div>
        <?php endif; ?>

        <!-- カテゴリ選択 -->
        <p class="title">【カテゴリ】</p>

        <!-- - - -  カテゴリ ラジオボタン  - - - -->
        <div class="form-group radio-wrap">
          <label>
            <input type="radio" name="c_id" value="0">
            <p>すべて</p>
          </label>
          <?php foreach ($dbCategoryData as $key => $val) : ?>
          <label>
            <input type="radio" name="c_id" value="<?php echo sanitize($val['id']); ?>"
              <?php if ($val['id'] == getFormDataByGet('c_id')) echo 'checked'; ?>>
            <p><?php echo sanitize($val['name']); ?></p>
          </label>
          <?php endforeach; ?>
        </div>

        <!-- - - -  ソート ラジオボタン  - - - -->
        <p class="title">【表示順序】</ps>
          <div class="form-group radio-wrap">
            <label>
              <input type="radio" name="sort" value="0">
              <p>選択しない</p>
            </label>
            <label>
              <input type="radio" name="sort" value="1" <?php if (getFormDataByGet('sort') == 1) echo 'checked'; ?>>
              <p>金額が安い順</p>
            </label>
            <label>
              <input type="radio" name="sort" value="2" <?php if (getFormDataByGet('sort') == 2) echo 'checked'; ?>>
              <p>金額が高い順</p>
            </label>
          </div>

          <!-- サイドバー・在庫なしを除く -->
          <div class="form-group stock">
            <label>
              <input type="checkbox" name="nt_soldout" value="1"
                <?php if (!empty(getFormDataByGet('nt_soldout'))) echo 'checked'; ?>><span>在庫なしを除く</span>
            </label>
          </div>

          <!-- 検索ボタン -->
          <button type="submit" id="btn" name="push-submit" value="sch-condition">
            <i class="fas fa-search"></i>&ensp;検索&ensp;
          </button>
      </section>

      <!-- メインコンテンツ -->
      <!-- 検索結果表示バー -->
      <section id="main-wrap">
        <div class="search-wrap">
          <label class="search-word">
            <span class="note"><i class="fas fa-search" aria-hidden="true"></i>&ensp;ワード検索：スペースで区切って入力してください</span>
            <input name="free-word" class="valid-fw" cols="10" rows="1"
              value="<?php echo getFormDataByGet('free-word'); ?>">
          </label>
          <!-- 検索ボタン -->
          <button type="submit" id="btn" name="push-submit" value="sch-word">
            <i class="fas fa-search" aria-hidden="true"></i>&ensp;検索&ensp;
          </button>
          <!-- 検索条件 -->
          <div class="form-group and-or">
            <label class="sch">
              <input type="radio" name="sch_andor" value="1"
                <?php if (getFormDataByGet('sch_andor') == 1 || empty($_GET['sch_andor'])) echo 'checked'; ?>><span>すべて</span>
            </label>
            <label class="sch sch-or">
              <input type="radio" name="sch_andor" value="2"
                <?php if (getFormDataByGet('sch_andor') == 2) echo 'checked'; ?>><span>いずれかを含む</span>
            </label>
          </div>
          <!-- メイン・在庫なしを除く -->
          <div class="form-group stock">
            <label>
              <input type="checkbox" name="nt_soldout_sw" value="1"
                <?php if (!empty(getFormDataByGet('nt_soldout_sw'))) echo 'checked'; ?>><span>在庫なしを除く</span>
            </label>
          </div>
        </div>

        <div class="search-title">
          <h1>【 商品一覧 】</h1>
          <div class="search-left search-common">
            <?php if ($dbProductList['total_count'] != 0) : ?>
            <span class="total-num"><?php echo sanitize($dbProductList['total_count']) . '件 の商品が見つかりました'; ?></span>
            <?php endif; ?>
          </div>

          <?php if ($dbProductList['total_count'] != 0) : ?>
          <div class="search-right search-common">
            <span class="num"><?php echo $currentPageHead + 1 ?></span> - <span
              class="num"><?php echo $currentPageHead + count($dbProductList['getList']); ?></span>件 / <span
              class="num">全<?php echo sanitize($dbProductList['total_count']) ?></span>件中
          </div>
          <?php endif; ?>
        </div>

        <?php if ($dbProductList['total_count'] == 0) : ?>
        <p class="no-hit">該当する商品はありません。</p>
        <?php endif; ?>

        <!-- 商品一覧エリア -->
        <div class="product-list">
          <!-- - $dbProductLis は連想配列なので - -->
          <?php foreach ($dbProductList['getList'] as $key => $val) : ?>
          <a class="product-container"
            href="productDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam() . '&p_id=' . $val['id'] : '?p_id=' . $val['id']; ?>">
            <div class="product-img">
              <img src="<?php echo showImage($val['pic1']); ?>" alt="<?php sanitize($val['name']); ?>">
            </div>
            <div>
              <p class="product-name"><?php echo sanitize($val['name']); ?></p>
            </div>
            <div class="product-price">
              <p>¥<?php echo sanitize(number_format($val['price'])); ?></p>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
        <!-- - - -  ページネーション  - - - -->
        <!-- - - -   検索条件の状態をページネーション関数に引数で渡す - - - -->
        <?php
        // 検索条件の状態をページネーション関数に渡す引数を作成
        $param_category = '&c_id=' . $category;
        $param_sort = '&sort=' . $sort;
        $param_notSoldOut = '&nt_soldout=' . $notSoldOut;
        $param_notSoldOutSw = '&nt_soldout_sw=' . $notSoldOutSw;
        $param_sch = '&sch_andor=' . $sch_radio;
        $link = '';
        if (!empty($_GET['c_id'])) :
          $link .=  $param_category;
        endif;
        if (!empty($_GET['sort'])) :
          $link .=  $param_sort;
        endif;
        if (!empty($_GET['nt_soldout'])) :
          $link .=  $param_notSoldOut;
        endif;
        if (!empty($_GET['nt_soldout_sw'])) :
          $link .=  $param_notSoldOutSw;
        endif;
        if (!empty($_GET['sch_andor'])) :
          // if ($btn = 'sch-word' && !empty($_GET['sch_andor'])) :
          $link .=  $param_sch;
        endif;
        debug('$linkの中身：' . $link);
        ?>
        <?php pagination($currentPage, $dbProductList['total_page'], $link); ?>
      </section>
    </form>
  </div>

  <!-- footer -->
  <?php
  require('footer.php');
  ?>