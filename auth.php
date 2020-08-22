<?php
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    ログイン認証・自動ログアウト
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// 🌟 最終ログイン日時：$_SESSION['login_date']
// 🌟 ログイン有効期限日時：$_SESSION['login_limit'] ・・・最終ログイン日時 + 有効期限
// $_SERVER['PHP_SELF']・・・ドメインからのパスを返す
//（http://localhost:8888/nails_market/login.php → パス：nails_market/login.php）
// basename・・・パスからファイル名だけを取り出す

// ログイン認証確認
$login = isLogin();

// ログインしていない場合はログインページへ
if (!$login) :
  if (basename($_SERVER['PHP_SELF']) !== 'login.php') :
    header("Location:login.php");
    exit();
  endif;
else :
  //ログイン済みの場合でログイン画面から呼び出している場合はマイページへ
  $_SESSION['login_date'] = time();
  if (basename($_SERVER['PHP_SELF']) === 'login.php') :
    header("Location:mypage.php");
    exit();
  endif;
endif;



// // ログインしている場合
// if (!empty($_SESSION['login_date'])) :
//   debug('ログイン済みユーザーです。');
//   // 現在日時がログイン有効期限（最終ログイン日時 + 有効期限） を超えていた場合
//   if (time() > ($_SESSION['login_date'] + $_SESSION['login_limit'])) :
//     debug('ログイン有効期限を超えています。');
//     // セッションを削除してログアウト状態にし、ログインページへ遷移する
//     session_destroy();
//     header("Location:login.php");
//     exit();
//   else :
//     // ログイン有効期限内の場合。最終ログイン日時を現在日時に更新する
//     debug(('ログイン有効期限内です。'));
//     $_SESSION['login_date'] = time();
//     // このファイルの呼び出し元が「login.php」の場合、login.phpを呼び出すと
//     // 無限ループになってしまうため、呼び出し元がlogin.phpの場合はマイページへ遷移させる。
//     // それ以外はページ遷移なし。
//     // $_SERVER['PHP_SELF']・・・ドメインからのパスを返す（nails_market/login.php）
//     // basename・・・パスからファイル名だけを取り出す
//     if (basename($_SERVER['PHP_SELF']) === 'login.php') :
//       debug('マイページへ遷移します。');
//       header("Location:mypage.php");
//       exit();
//     endif;
//   endif;
// // 未ログインユーザーの場合
// else :
//   debug('未ログインユーザーです。');
//   if (basename($_SERVER['PHP_SELF']) !== 'login.php') :
//     header("Location:login.php");
//   endif;
// endif;