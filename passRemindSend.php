<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


if (!empty($_POST)) :
  debug('POST送信があります。');
  debug('$_POSTの中身：' . print_r($_POST, true));
  $email = $_POST['email'];
  validRequired($email, 'email');
  if (empty($err_msg)) :
    debug('未入力チェックOK');
    validEmail($email, 'email');
    validMaxLen($email, 'email');
    if (empty($err_msg)) :
      try {
        $dbh = dbConnect();
        $sql = 'SELECT email
                FROM users
                WHERE email = :email
                AND delete_flg = 0
                ';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh, $sql, $data, 'パスワード再発行メール送信');
        if ($stmt) :
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          if ($result) :
            debug('Email登録情報取得成功。');
            debug('Email登録件数（$resultの中身）：' . print_r($result, true));
            $_SESSION['msg_success'] = SUC03;
            $auth_key = makeRandKey();  // 認証キー生成

            // 認証キー送信メール送信
            $from = 'info@test.com';
            $to = $email;
            $subject = '認証キー送信メール ｜ nails market';
            // ヒアドキュメントでメール本文を作成
            $comment = <<<EOT

本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力いただくとパスワードが再発行されます。
※ 本メールにお心当たりがない場合は、お手数ですがお問い合わせフォームにてご連絡をお願いいたします。

パスワード再発行認証キー入力専用ページ : http://nailsmarket.emickey-pgnail.net/nails_market/passRemindRecieve.php

認証キー : {$auth_key}

※ 認証キーの有効期限は30分となります。

認証キーが再発行されない場合は下記ページより再度発行をお願いいたします。

認証キー再発行専用ページ : http://nailsmarket.emickey-pgnail.net/nails_market/passRemindSend.php


□ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □
nails market 自動メール配信
URL : http://nailsmarket.emickey-pgnail.net/nails_market/home.php
E-mail : info@test.com
□ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □ * □
EOT;
            sendMail($from, $to, $subject, $comment);

            // 認証に必要な情報をセッションに保存
            $_SESSION['auth_key'] = $auth_key;
            $_SESSION['auth_email'] = $email;
            $_SESSION['auth_key_limit'] = time() + (60 * 30);
            debug('セッション変数の中身：' . print_r($_SESSION, true));
            // 認証キー入力ページへ
            header("Location:passRemindRecieve.php");
            exit();
          else :
            debug('DBに登録のないEmailアドレスが入力されました。');
            $err_msg['common'] = MSG32;
          endif;
        else :
          debug('クエリに失敗しました。');
          $err_msg['common'] = MSG99;
        endif;
      } catch (Exception $e) {
        error_log('例外エラー発生（パスワードメール送信ページにて）：' . $e->getMessage());
        $err_msg['common'] = MSG99;
      }
    endif;
  endif;
endif;

debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>

<?php
$siteTitle = 'パスワード再発行メール送信';
require('head.php');
?>

<body class="page-passSend page-1colum">
  <!-- ヘッダー -->
  <?php
  require('header.php');
  ?>
  <!-- メインコンテンツ -->
  <div id="contents-wrap" class="site-width">
    <section id="main-wrap">
      <div class="form-wrap">
        <h1 class="title">パスワード再発行メール送信</h1>
        <!-- 入力フォーム -->
        <form action="" method="post" class="form">
          <p>ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送りいたします。</p>
          <!-- 共通のエラーメッセージ -->
          <div class="area-msg common-msg">
            <?php echo getErrMessage('common'); ?>
          </div>
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="<?php echo getErrClass('email'); ?>"><span class="title">メールアドレス</span>
              <input type="text" name="email" value="<?php echo getFormData('email'); ?>"> </label>
            <div class="area-msg">
              <?php echo getErrMessage('email'); ?>
            </div>
          </div>
          <!-- 送信ボタン -->
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value=" 送信する">
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