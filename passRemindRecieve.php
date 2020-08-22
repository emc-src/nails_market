<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// 認証キーがない場合はメール送信ページへリダイレクト
if (!isset($_SESSION['auth_key'])) :
  debug('認証キーなし。passRemindSend.phpへリダイレクト。');
  header("Location:passRemindSend.php");
  exit();
endif;

// POST送信された場合
if (!empty($_POST)) :
  debug('POST送信があります。');
  $auth_key = $_POST['auth_key'];
  // 未入力チェック
  validRequired($auth_key, 'auth_key');
  if (empty($err_msg)) :
    debug('未入力チェックOK。');
    validHalfTextNumber($auth_key, 'auth_key');
    validMaxLen($auth_key, 'auth_key');

    // 未入力チェックOKの場合
    if (empty($err_msg)) :
      debug('バリデーションOK。');
      // 認証キー同値チェック
      if ($auth_key !== $_SESSION['auth_key']) :
        debug('入力した認証キーが一致しません。');
        $err_msg['common'] = MSG42;
      endif;
      // 認証キー有効期限切れチェック
      debug('現在日時タイムスタンプ：' . date(" Y/m/d H:i:s", time()) . '：' . time());
      debug('認証キー有効期限日時：　' . date(" Y/m/d H:i:s", $_SESSION['auth_key_limit']) . '：' . $_SESSION['auth_key_limit']);
      if ($_SESSION['auth_key_limit'] < time()) :
        debug('認証キーが有効期限切れです。');
        $err_msg['common'] = MSG44;
      endif;
      // エラーメッセージなしの場合
      if (empty($err_msg)) :
        debug('認証OK。新しいパスワードを発行します。');
        // 新パスワード生成
        $pass = makeRandKey();
        try {
          $dbh = dbConnect();
          $sql = 'UPDATE users
                SET password = :pass
                WHERE email = :email
                AND delete_flg = 0
                LIMIT 1
                ';
          $data = array(
            ':email' => $_SESSION['auth_email'],
            ':pass' => password_hash($pass, PASSWORD_DEFAULT)
          );
          $stmt = queryPost($dbh, $sql, $data, 'パスワード再発行');
          if ($stmt) :
            debug('パスワード再発行完了しました。');
            // パスワード変更メール送信
            $from = 'info@test.com';
            $to = $_SESSION['auth_email'];
            $subject = '【パスワード再発行完了】送信メール ｜ nails market';
            // ヒアドキュメントでメール本文を作成
            $comment = <<<EOT

本メールアドレス宛にパスワード再発行をいたしました。
下記のURLにて再発行パスワードをご入力いただき、ログインしてください。
※ 本メールにお心当たりがない場合は、お手数ですがお問い合わせフォームにてご連絡をお願いいたします。

ログインページ : http://nailsmarket.emickey-pgnail.net/nails_market/login.php

再発行パスワード : {$pass}

※ ログイン後はパスワードのご変更をお願いいたします。

□ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □
nails market 自動メール配信
URL : http://nailsmarket.emickey-pgnail.net/nails_market/home.php
E-mail : info@test.com
□ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □
EOT;
            sendMail($from, $to, $subject, $comment);

            // セッション削除
            session_unset();
            $_SESSION['msg_success'] = SUC04;
            debug('セッション変数の中身：' . print_r($_SESSION, true));
            header("Location:login.php");
            exit();
          else :
            debug('メール送信エラーまたはクエリ失敗');
          endif;
        } catch (Exception $e) {
          error_log('例外エラー発生（認証キー入力ページにて）：' . $e->getMessage());
          $err_msg['common'] = MSG99;
        }
      endif;
    endif;
  endif;
endif;

debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>

<?php
$siteTitle = 'パスワード再発行認証';
require('head.php');
?>

<body class="page-passRecieve page-1colum">
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
    <section id="main-wrap">
      <div class="form-wrap">
        <h1 class="title">パスワード再発行認証</h1>
        <!-- 入力フォーム -->
        <form action="" method="post" class="form">
          <p>ご指定のメールアドレスへ送付いたしました【パスワード再発行認証】メール内にある「認証キー」をご入力ください。</p>
          <!-- 共通のエラーメッセージ -->
          <div class="area-msg common-msg">
            <?php echo getErrMessage('common'); ?>
          </div>
          <!-- 認証キー入力欄 -->
          <div class="form-group">
            <label class="<?php echo getErrClass('auth_key'); ?>"><span class="title">認証キー</span>
              <input type="text" name="auth_key" value="<?php echo getFormData('auth_key'); ?>">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('auth_key'); ?>
            </div>
            <!-- 送信ボタン -->
            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value=" 送信する">
            </div>
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