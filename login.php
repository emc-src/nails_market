<?php

//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

// POST送信サれていた場合
if (!empty($_POST)) :
  debug('POST送信があります。');
  // 変数にフォームデータを代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;
  // バリデーション未入力
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  if (empty($err_msg)) :
    // バリデーションEmail
    validEmail($email, 'email');
    validEmail($email, 'email');
    // バリデーションパスワード
    validPassword($pass, 'pass');
    if (empty($err_msg)) :

      try {
        $dbh = dbConnect();
        // ログイン認証に必要なパスワードとユーザーidを取得
        $sql = 'SELECT password, id
                FROM users
                WHERE email = :email AND delete_flg = 0
                ';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh, $sql, $data, 'ログインページ');
        if ($stmt) :
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          // パスワードを照合。一致の場合
          if (!empty($result) && password_verify($pass, $result['password'])) :
            // if (!empty($result) && password_verify($pass, array_shift($result))) :
            debug('パスワードがマッチしました。');
            // セッションのログイン情報を更新する
            $sesLimit = 60 * 60;
            $_SESSION['login_date'] = time();
            // ログイン保持チェックがある場合
            if ($pass_save) :
              // ログイン有効期限を60分✕24h✕30日に更新する
              $_SESSION['login_limit'] = $sesLimit * 24 * 30;
            else :
              // 保持なしの場合はデフォルトの１時間を代入
              $_SESSION['login_limit'] = $sesLimit;
            endif;
            // ログイン成功なのでユーザーIDを格納しマイページへ遷移する
            $_SESSION['user_id'] = $result['id'];
            debug('セッション変数の中身：' . print_r($_SESSION, true));
            debug('ログイン成功。マイページへ遷移します。');
            header("Location:mypage.php");
            exit();
          else :
            debug('パスワードが一致しません。');
            $err_msg['common'] = MSG05;
          endif;
        endif;
      } catch (Exception $e) {
        error_log('例外エラー発生（ログインページにて）：' . $e->getMessage());
        $err_msg['common'] = MSG99;
      }
    endif;
  endif;
endif;


debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>


<?php
$siteTitle = 'ログイン';
require('head.php');
?>

<body class="page-login page-1colum">
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
        <h1 class="title">ログイン</h1>
        <!-- 入力フォーム -->
        <form action="" method="post" class="form">
          <!-- 共通のエラーメッセージ -->
          <div class="area-msg common-msg">
            <?php echo getErrMessage('common'); ?>
          </div>
          <!-- メールアドレス -->
          <div class="form-group">
            <label class=""><span class="title">メールアドレス</span>
              <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('email'); ?>
            </div>
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="error"><span class="title">パスワード</span>
              <input type="password" name="pass" value="<?php echo getFormData('pass'); ?>">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('pass'); ?>
            </div>
          </div>
          <!-- 次回ログイン -->
          <div class="form-group pass-save">
            <label>
              <input type="checkbox" name="pass_save"><span>次回ログインを省略する</span>
            </label>
          </div>
          <!-- パスワード再発行 -->
          <div class="form-group pass-remind">
            パスワードを忘れた方は&ensp;<a href="passRemindSend.php">こちら</a>
          </div>
          <!-- 登録ボタン -->
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="ログイン">
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