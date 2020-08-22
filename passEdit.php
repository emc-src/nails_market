<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　パスワード変更　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

$u_id = $_SESSION['user_id'];
// ユーザー情報取得
$myUserData = getUser($u_id);
debug('DB情報取得：Myユーザー情報（$myUserDataの中身）：' . print_r($myUserData, true));

if (!empty($_POST)) :
  debug('POST送信があります。');
  // 変数にフォームの情報を代入
  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  // 未入力チェック
  validRequired($pass_old, 'pass_old');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');
  // 未入力なしの場合
  if (empty($err_msg)) :
    debug('未入力チェックOK。');
    // パスワードのバリデーションチェック
    validPassword($pass_old, 'pass_old');
    validPassword($pass_new, 'pass_new');
    // 古いパスワードとDBのパスワードを照合。一致しない場合はエラーメッセージ格納
    if (!password_verify($pass_old, $myUserData['password'])) :
      $err_msg['pass_old'] = MSG03;
    endif;
    // 古いパスワードと新しいパスワードを照合。一致の場合はエラーメッセージ格納
    if ($pass_old === $pass_new) :
      $err_msg['pass_new'] = MSG04;
    endif;
    // 新しいパスワードとパスワード再入力を照合
    validMatch($pass_new, $pass_new_re, 'pass_new_re');
    // バリデーションOKの場合、DBのパスワードを更新
    if (empty($err_msg)) :
      debug('パスワード再入力チェックOK。');
      try {
        $dbh = dbConnect();
        $sql = 'UPDATE users
                SET password = :pass
                WHERE id = :u_id
                LIMIT 1
                ';
        $data = array(':u_id' => $u_id, ':pass' => password_hash($pass_new, PASSWORD_DEFAULT));
        $stmt = queryPost($dbh, $sql, $data, 'パスワード変更');

        if ($stmt) :
          debug('パスワードを更新しました。');
          $_SESSION['msg_info'] = INFO3;
          // パスワード変更メールを送信
          $user_name = $myUserData['userName'];
          $from = 'info@test.com';
          $to = $myUserData['email'];
          $subject = 'パスワード変更通知メール ｜ nails market';
          // ヒアドキュメントでメール本文を作成
          $comment = <<<EOT
{$user_name}　様
パスワードが変更されました。

□ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □
nails market 自動メール配信
URL : http://nailsmarket.emickey-pgnail.net/nails_market/home.php
E-mail : info@test.com
□ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □
EOT;
          sendMail($from, $to, $subject, $comment);
          $_SESSION['msg_info'] = INFO3;
          $_SESSION['link_info'] = 'mypage.php';
          $_SESSION['pageName_info'] = '&ensp;マイページへ';
          header("Location:information.php");
          exit();
        endif;
      } catch (Exception $e) {
        error_log('例外エラー発生（パスワード変更にて）：' . $e->getMessage());
        $err_msg['common'] = MSG99;
      }
    endif;
  endif;
endif;

debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>

<?php
$siteTitle = 'パスワード変更';
require('head.php');
?>

<body class="page-passEdit page-1colum">
  <!-- ヘッダー -->
  <?php
  require('header.php');
  ?>
  <!-- メインコンテンツ -->
  <div id="contents-wrap" class="site-width">
    <section id="main-wrap">
      <div class="form-wrap">
        <h1 class="title">パスワード変更</h1>
        <!-- 入力フォーム -->
        <form action="" method="post" class="form" onsubmit="return submitCheck();">
          <!-- 共通のエラーメッセージ -->
          <div class="area-msg common-msg">
            <?php echo getErrMessage('common'); ?> </div>
          <!-- 古いパスワード -->
          <div class="form-group">
            <label class="<?php echo getErrClass('pass_old'); ?>"><span class="title">古いパスワード</span>
              <input type="password" name="pass_old" value="<?php echo getFormData('pass_old') ?>">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('pass_old'); ?>
            </div>
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="<?php echo getErrClass('pass_new'); ?>"><span class="title">新しいパスワード</span>
              <input type="password" name="pass_new" value="<?php echo getFormData('pass_new') ?>">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('pass_new'); ?>
            </div>
          </div>
          <!-- パスワード再入力 -->
          <div class="form-group">
            <label class="<?php echo getErrClass('pass_new_re'); ?>"><span class="title">パスワード再入力</span>
              <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re') ?>">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('pass_new_re'); ?>
            </div>
          </div>

          <!-- 登録ボタン -->
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="パスワードを変更する">
          </div>
        </form>
      </div>
      <!-- トップページへのリンク -->
      <div class="link">
        <a href="home.php"><span class="fas fa-home"></span>&ensp;トップページへ</a>
      </div>
    </section>
  </div>
  <?php
  require('footer.php');
  ?>