<?php

//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


// ログイン済みユーザーの場合はDBからユーザー情報を読み込み
if (!empty($_SESSION['user_id'])) :
  $u_id = $_SESSION['user_id'];
  $myUserData = getUser($u_id);
  debug('DB情報取得：Myユーザー情報（$myUserDataの中身）：' . print_r($myUserData, true));
  $edit_flg = true;
  $process = 'ログイン済みユーザーによるユーザー情報更新';
else :
  $myUserData = array();
  $edit_flg = false;
  $process = '新規ユーザー登録';
endif;

// POST送信されていた場合（されていない場合は新規状態を表示)
if (!empty($_POST)) :
  // 変数に入力フォームの内容を代入
  $userName = $_POST['userName'];
  $userName_ruby = $_POST['userName_ruby'];
  $subName = $_POST['subName'];
  $zip = $_POST['zip'];
  $address1 = $_POST['address1'];
  $address2 = $_POST['address2'];
  $tel_mobile = $_POST['tel_mobile'];
  $email = $_POST['email'];
  // 新規登録の場合のみパスワードチェック
  if ($edit_flg === false) :
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];
  endif;

  // 未入力チェック
  validRequired($userName, 'userName');
  validRequired($userName_ruby, 'userName_ruby');
  validRequired($subName, 'subName');
  validRequired($zip, 'zip');
  validRequired($address1, 'address1');
  validRequired($tel_mobile, 'tel_mobile');
  validRequired($email, 'email');
  // 新規登録の場合のみパスワードチェック
  if ($edit_flg === false) :
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');
  endif;

  // 未入力チェックOKの場合
  if (empty($err_msg)) :
    // 名前のチェック
    validFullWidth($userName, 'userName');
    validMaxLen($userName, 'userName');
    // ふりがなチェック
    validKatakana($userName_ruby, 'userName_ruby');
    validMaxLen($userName_ruby, 'userName_ruby');
    // ニックネームチェック
    validFullWidth($subName, 'subName');
    validMaxLen($subName, 'subName');
    // 郵便番号チェック
    validZip($zip, 'zip');
    // 住所１チェック
    validFullWidth($address1, 'address1');
    validMaxLen($address1, 'address1');
    // 住所２チェック
    validFullWidth($address2, 'address2');
    validMaxLen($address2, 'address2');
    // 携帯電話チェック
    validMobileTel($tel_mobile, 'tel_mobile');
    // Eメールアドレスチェック
    validEmail($email, 'email');

    // 新規登録の場合のみEメール重複とパスワードチェック
    if ($edit_flg === false) :
      validEmailDup($email, 'email');
      validPassword($pass, 'pass');
    endif;

    // バリデーションOKの場合
    if (empty($err_msg)) :
      // 新規登録の場合のみパスワードチェック
      if ($edit_flg === false) :
        validMatch($pass, $pass_re, 'pass_re');
      endif;
      // すべてのバリデーションがOKの場合、DBへ新規ユーザー情報を登録
      if (empty($err_msg)) :
        debug('$_POSTの中身：' . print_r($_POST, true));
        try {
          $dbh = dbConnect();
          // ユーザー情報更新の場合
          if ($edit_flg === true) :
            debug('ログイン済みユーザーによるDBの情報を更新。');
            $sql = 'UPDATE users
                    SET userName = :userName, userName_ruby = :userName_ruby, subName = :subName,
                    zip = :zip, address1 = :address1, address2 = :address2, tel_mobile = :tel_mobile,
                    email = :email, login_time = :login_time
                    WHERE id = :id
                    LIMIT 1
                    ';
            $data = array(
              ':userName' => $userName, ':userName_ruby' => $userName_ruby, ':subName' => $subName,
              ':zip' => $zip, ':address1' => $address1, ':address2' => $address2,
              ':tel_mobile' => $tel_mobile, ':email' => $email, ':login_time' => date('Y-m-d H:i:s'),
              ':id' => $u_id
            );
          // 新規ユーザーの場合
          else :
            debug('DBへ新規ユーザー登録。');
            $sql = 'INSERT INTO users
                  (userName, userName_ruby, subName, zip, address1, address2, tel_mobile,
                  email, password, delete_flg, login_time, create_date)
                  VALUES
                  (:userName, :userName_ruby, :subName, :zip, :address1, :address2, :tel_mobile,
                  :email, :password, :delete_flg, :login_time, :create_date)
                  ';
            $data = array(
              ':userName' => $userName, ':userName_ruby' => $subName, ':subName' => $subName,
              ':zip' => $zip, ':address1' => $address1, ':address2' => $address2, ':tel_mobile' => $tel_mobile,
              ':email' => $email, ':password' => password_hash($pass, PASSWORD_DEFAULT),
              ':delete_flg' => 0, ':login_time' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s')
            );
          endif;

          // クエリ実行
          $stmt = queryPost($dbh, $sql, $data, $process);

          // クエリ成功の場合、セッションにログイン情報を代入
          if ($stmt) :
            if ($edit_flg === false) :
              // ログイン有効期限を１時間に設定（新規なので自動ログインチェックなし）
              $sesLimit = 60 * 60;
              // 最終ログイン日時に現在日時（タイムスタンプ）を代入
              $_SESSION['login_date'] = time();
              // ログイン有効期限を代入
              $_SESSION['login_limit'] = $sesLimit;
              // ユーザーIDを代入
              $_SESSION['user_id'] = $dbh->lastInsertId();
              $_SESSION['msg_success'] = SUC01;
              debug('セッション変数の中身：' . print_r($_SESSION, true));
            endif;
            $_SESSION['msg_success'] = SUC05;
            header("Location:mypage.php");
            exit();

          endif;
        } catch (Exception $e) {
          error_log('例外エラー発生（.' . $process . '）：' . $e->getMessage());
        }
      endif;
    endif;
  endif;
endif;


debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>

<?php
if ($edit_flg === true) :
  $siteTitle = 'ユーザー情報編集';
else :
  $siteTitle = '新規登録';
endif;
require('head.php');
?>

<body class="page-signup page-1colum">
  <!-- ヘッダー -->
  <?php
  require('header.php');
  ?>
  <!-- メインコンテンツ -->
  <div id="contents-wrap" class="site-width">
    <section id="main-wrap">
      <div class="form-wrap">
        <h1 class="title"><?php if ($edit_flg === true) : echo 'ユーザー情報編集';
                          else : echo '新規登録';
                          endif; ?></h1>
        <!-- 入力フォーム -->
        <form action="" method="post" class="form" onsubmit="return submitCheck();">
          <!-- 共通のエラーメッセージ -->
          <div class="area-msg common-msg">
            <?php echo getErrMessage('common'); ?>
          </div>
          <!-- お名前 -->
          <div class="form-group">
            <label class="<?php echo getErrClass('userName'); ?>"><span class="required">必須</span><span
                class="title">お名前：</span><span class="comment">全角入力</span>
              <input type="text" name="userName" value="<?php echo getFormData('userName', $myUserData); ?>">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('userName'); ?>
            </div>
          </div>
          <!-- フリガナ -->
          <div class="form-group">
            <label class="<?php echo getErrClass('userName_ruby') ?>"><span class="required">必須</span><span
                class="title">フリガナ：</span><span class="comment">全角カタカナ入力</span>
              <input type="text" name="userName_ruby" value="<?php echo getFormData('userName_ruby', $myUserData); ?>">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('userName_ruby'); ?>
            </div>
          </div>
          <!-- ニックネーム -->
          <div class="form-group">
            <label class="<?php echo getErrClass('subName'); ?>"><span class="required">必須</span><span
                class="title">ニックネーム：</span><span class="comment">全角入力、20文字以内</span>
              <input type="text" name="subName" maxlength="20"
                value="<?php echo getFormData('subName', $myUserData); ?>">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('subName'); ?>
            </div>
          </div>

          <!-- 郵便番号 -->
          <div class="form-group zip">
            <label class="<?php echo getErrClass('zip'); ?>"><span class="required">必須</span><span
                class="title">郵便番号：</span><span class="comment">例
                / 999-9999（半角数字）</span>
              <input type="text" name="zip" value="<?php echo getFormData('zip', $myUserData); ?>" maxlength="8">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('zip'); ?>
            </div>
          </div>
          <!-- 住所 -->
          <div class="form-group">
            <div class="address-1">
              <label class="<?php echo getErrClass('address1'); ?>"><span class="required">必須</span><span
                  class="title">住所１：</span><span class="comment">都道府県・市区町村・番地</span>
                <input type="text" name="address1" value="<?php echo getFormData('address1', $myUserData); ?>">
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('address1'); ?>
              </div>
            </div>
            <div class="address-2">
              <label class="<?php echo getErrClass('address2'); ?>"><span class="any">任意</span><span
                  class="title">住所２：</span><span class=" comment">建物名</span>
                <input type="text" name="address2" value="<?php echo getFormData('address2', $myUserData); ?>">
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('address2'); ?>
              </div>
            </div>
          </div>
          <!-- 携帯TEL -->
          <div class="form-group tel">
            <label class="<?php echo getErrClass('tel_mobile'); ?>"><span class="required">必須</span><span
                class="title">携帯電話番号：</span><span class="comment">例 / 090-0000-0000（半角数字）</span>
              <input type="tel" name=" tel_mobile" value="<?php echo getFormData('tel_mobile', $myUserData); ?>">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('tel_mobile'); ?>
            </div>
          </div>
          <!-- Eメール -->
          <div class="form-group">
            <label class="<?php echo getErrClass('email') ?>"><span class="required">必須</span><span
                class="title">Eメールアドレス</span><span class="comment"></span>
              <input type="email" name="email" value="<?php echo getFormData('email', $myUserData); ?>">
            </label>
            <div class="area-msg">
              <?php echo getErrMessage('email'); ?>
            </div>
          </div>
          <!-- パスワード -->
          <?php if (!$edit_flg) : ?>
          <div class="form-group">
            <div class="password-1">
              <label class="<?php echo getErrClass('pass') ?>"><span class="required">必須</span><span
                  class="title">パスワード：</span><span class="comment">半角英数字６文字以上</span>
                <input type="password" name="pass" value="<?php echo getFormData('pass'); ?>">
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('pass'); ?>
              </div>
            </div>
            </br>
            <div class="password-2">
              <label class="<?php echo getErrClass('pass_re') ?>"><span class="required">必須</span><span
                  class="title">パスワード再入力</span><span class=" comment"></span>
                <input type="password" name="pass_re" value="<?php echo getFormData('pass_re'); ?>">
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('pass_re'); ?>
              </div>
            </div>
          </div>
          <?php endif; ?>
          <!-- 登録ボタン -->
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="<?php if ($edit_flg === true) : echo 'ユーザー情報を更新';
                                                            else : echo '新規登録';
                                                            endif; ?>">
          </div>
        </form>
      </div>
      <!-- トップページへのリンク -->
      <div class="link">
        <ul>
          <li><a href="home.php"><i class="fas fa-home"></i>&ensp;トップページへ</a></li>
        </ul>
      </div>
    </section>
  </div>
  <?php
  require('footer.php');
  ?>