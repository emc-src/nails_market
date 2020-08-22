<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　プロフィール　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

// 画像はPOST送信されないので初期化しておく
$profPic = '';


$u_id = $_SESSION['user_id'];

// ユーザー情報取得
$myUserData = getUser($u_id);


if (!empty($_POST)) :
  $subName = $_POST['subName'];
  $profComment = $_POST['profComment'];
  // ■ 画像アップロード
  // 　画像はPOST送信されないので、変数にアップロードしたパスを格納する
  // 　画像をアップロードしている場合はパスを格納
  $profPic = (!empty($_FILES['profPic']['name']) ? uploadImg($_FILES['profPic'], 'profPic') : '');
  // $profPic = (empty($profPic) && !empty($myUserData['profPic']) ? $myUserData['profPic'] : $profPic);


  // 未入力チェック
  validRequired($subName, 'subName');

  if (empty($err_msg)) :
    debug('未入力チェックOKです。');
    // フォームの内容とDBの内容が一致していない場合はデータ変更のため
    // フォームの内容をバリデーションチェックする
    if ($myUserData['subName'] !== $subName) :
      validFullWidth($subName, 'subName');
      validMaxLen($subName, 'subName');
    endif;
    if ($myUserData['profComment'] !== $profComment) :
      validHalfKana($profComment, 'profComment');
      validMaxLen($profComment, 'profComment', 250);
    endif;
    if (empty($err_msg)) :
      debug('バリデーションOKです。');
      try {
        $dbh = dbConnect();
        $sql = 'UPDATE users
                SET subName = :subName, profComment = :profComment, profPic = :profPic
                WHERE id = :u_id
                LIMIT 1
                ';
        $data = array(
          ':u_id' => $u_id, ':subName' => $subName, ':profComment' => $profComment,
          ':profPic' => $profPic
        );
        $stmt = queryPost($dbh, $sql, $data, 'プロフィール編集画面');
        if ($stmt) :
          $_SESSION['msg_success'] = SUC02;
          debug('プロフィール編集成功。');
          debug('マイページへ遷移します。。');
          header("Location:mypage.php");
          exit();
        endif;
      } catch (Exception $e) {
        error_log('例外エラー発生（プロフィール編集画面にて）：' . $e->getMessage());
        $err_msg['common'] = MSG99;
      }
    endif;
  endif;
endif;

debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>


<?php
$siteTitle = 'プロフィール編集';
require('head.php');
?>

<body class="page-profile page-2colum">
  <!-- ヘッダー -->
  <?php
  require('header.php');
  ?>
  <!-- メインコンテンツ -->
  <div id="contents-wrap" class="site-width">
    <div class="main-side-wrap">
      <section id="main">
        <div class="form-wrap">
          <h1 class="title">プロフィール編集</h1>
          <!-- 入力フォーム -->
          <form action="" method="post" class="form" enctype="multipart/form-data" onsubmit="return submitCheck();">
            <!-- 共通のエラーメッセージ -->
            <div class="area-msg common-msg">
              <?php echo getErrMessage('common'); ?>
            </div>

            <!-- ニックネーム -->
            <div class="form-group">
              <label class="error"><span class="required">必須</span><span class="title">ニックネーム：</span><span
                  class="comment">全角入力、20文字以内</span>
                <input type="text" name="subName" maxlength="20"
                  value="<?php echo getFormData('subName', $myUserData); ?>">
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('subName'); ?>
              </div>
            </div>

            <!-- コメント -->
            <div class="form-group js-counter">
              <label class="<?php echo getErrClass('profComment'); ?>"><span class="any"">任意</span><span class="
                  title">自己紹介など：</span><span class="comment">250文字以内</span>
                <pre><textarea name="profComment" id="js-count" cols="30" rows="20" maxlength="250"><?php echo getFormData('profComment', $myUserData) ?></textarea></pre>
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('profComment'); ?>
              </div>
              <p class="js-text-counter"><span id="js-count-view">0</span>/250文字</p>

            </div>
            <!-- プロフィール画像 -->
            <div class="form-group prof">
              <span class="any">任意</span><span class=" title">プロフィール画像：</span><span>画像を選択（3MBまで）</span>
              <label class="<?php echo getErrClass('profPic'); ?> area-drop">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="profPic" class="fileInput-area" value=""><span>ドラッグ&ドロップ<br />またはタップして選択</span>
                <?php // プロフ画像がある場合は画像を表示
                if (!empty(getFormData('profPic', $myUserData))) :
                ?>
                <img src="<?php echo getFormDataImg('profPic', $myUserData); ?>" alt="" class="img-area img-src1">
                <?php endif; ?>

              </label>
              <div class="area-msg">
                <?php echo getErrMessage('profPic'); ?>
              </div>
              <p class="pic-delete js-delete-pic1">画像を削除</p>
            </div>

            <!-- 登録ボタン -->
            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="変更する">
            </div>
          </form>
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