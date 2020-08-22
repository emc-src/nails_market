<?php
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    ログ
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// 出力先ファイルにログを出力する
ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    デバッグ
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// デバッグフラグ （開発終了時はfalseにする）
$debug_flg = false;

// デバッグログ関数
function debug($str)
{
  global $debug_flg;
  if (!empty($debug_flg)) :
    error_log('デバッグ：：' . $str);
  endif;
}

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    セッションの準備と有効期限の設定
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// セッションファイルの置き場所を変更する（30日間保持)
session_save_path("/var/tmp/");
// ガーベージコレクションが削除するセッションの有効期限を設定
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
// cookieの有効期限を伸ばしてブラウザを閉じてもセッション保持する
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
// セッションスタートして、セッションを最新のものに更新してセキュリティ対策
session_start();
session_regenerate_id();

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    画面処理開始とログ出力関数
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function debugLogStart()
{
  debug('セッションstart。新しいセッションID：' . session_id());
  debug('セッション変数の中身' . print_r($_SESSION, true));
  debug('現在日時タイムスタンプ：' . time() . '：' . date(" Y/m/d H:i:s", time()));
  // ログイン記録がある場合はログイン有効期限をログに出力
  if (!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) :
    $limitFormat = $_SESSION['login_date'] + $_SESSION['login_limit'];
    debug('最終ログイン日時：[login_date]： ' . date(" Y/m/d H:i:s", time()) . '：' . $_SESSION['login_date']);
    debug('ログイン有効期限：[login_limit]：' . date(" Y/m/d H:i:s", $limitFormat) . '：'  . $limitFormat);
  endif;
}

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    定数の定義
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

//バリデーション用メッセージ
// バリデーション（未入力）
define('MSG01', '入力必須です');
// バリデーション（パスワード）
define('MSG02', 'パスワード（再入力）が一致していません');
define('MSG03', '古いパスワードが違います');
define('MSG04', '古いパスワードと同じです');
define('MSG05', 'メールアドレスまたはパスワードが違います');
// バリデーション（入力形式）
define('MSG11', '全角で入力してください');
define('MSG12', '全角カタカナで入力してください');
define('MSG13', '郵便番号の入力形式で入力してください');
define('MSG14', '携帯電話の入力形式で入力してください');
define('MSG15', 'Emailの形式で入力してください');
define('MSG16', '半角数字のみご利用いただけます');
define('MSG17', '半角英数字のみご利用いただけます');
define('MSG18', '半角カタカナは使用できません');

// バリデーション（文字数）
define('MSG21', '指定の文字数以内で入力してください');
define('MSG22', '6文字以上で入力してください');
define('MSG23', '255文字以内で入力してください');
// バリデーション（Email）
define('MSG31', 'ご指定のEmailアドレスは既に登録されています');
define('MSG32', 'ご指定のEmailアドレスは登録がありません');
// バリデーション（その他）
define('MSG41', '文字で入力してください');
define('MSG42', '入力が正しくありません');
define('MSG43', '有効期限が切れています');
define('MSG44', '円以上で入力してください');
define('MSG45', '円以下で入力してください');
define('MSG46', '出品した商品は購入できません');
define('MSG47', '入力必須です');
// その他メッセージ

// その他エラーメッセージ
define('MSG99', 'エラーが発生しました。しばらく経ってからやり直してください');
// その他JSメッセージ
define('SUC01', 'ユーザー登録しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'ご指定のEmailアドレスに認証キーを送信しました');
define('SUC04', 'パスワードを再発行しました');
define('SUC05', 'ユーザー情報を更新しました');
define('SUC06', '商品を出品しました');
define('SUC07', '出品情報を更新しました');
define('SUC08', '出品情報を下書に保存しました');
define('SUC09', '出品を削除しました');
define('SUC10', '商品を購入しました');
// インフォメーション用メッセージ
define('INFO1', 'インフォメーションはありません。');
define('INFO2', 'ご指定のメールアドレスへメールを送信いたしました。');
define('INFO3', 'パスワードを変更しました。');
define('INFO4', '退会しました。ご利用ありがとうございました。');
define('INFO5', 'サービスをご利用いただくにはログインが必要となります。');


// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    グローバル変数
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// エラーメッセージ格納配列
$err_msg = array();

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    サニタイズ関数
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function sanitize($str)
{
  return htmlspecialchars($str, ENT_QUOTES);
}

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    ログイン認証関数
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function isLogin()
{
  // ログインしている場合
  if (!empty($_SESSION['login_date'])) :
    debug('ログインユーザーです。');
    if (($_SESSION['login_date'] + $_SESSION['login_limit']) < time()) :
      debug('ログイン有効期限を超えています。');
      session_destroy();
      return false;
    else :
      debug('ログイン有効期限内です。');
      return true;
    endif;
  // ログインしていない場合
  else :
    debug('未ログインユーザーです。');
    return false;
  endif;
}


// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    セッション取得関数
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// セッションを１回だけ取得する
function getOnetimeSession($key)
{
  if (!empty($_SESSION[$key])) :
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  endif;
}

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    GETパラメータ生成取得関数
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// 現在のページURLからGETパラメータを取得。（🌟配列から取り出して文字列として連結して返す）
// 引数指定があるの場合は現在のGETパラメータから指定の引数のパラメータを削除した-
// -GETパラメータを生成する。引数なしの場合は現在のGETパラメータを連結して返す。
function appendGetParam($arrayDeleteKey = array())
{
  if (!empty($_GET)) :
    $str = '?';
    foreach ($_GET as $key => $val) :
      if (!in_array($key, $arrayDeleteKey, true)) :
        $str .= $key . '=' . $val . '&';
      endif;
    endforeach;
    // パラメータ末尾の'&'を削除
    $str = mb_substr($str, 0, -1, 'UTF-8');
    // debug('生成したGETパラメータ：' . $str);
    return  $str;
  endif;
}

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    GETパラメータ保持関数
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function keepGetParam($keyWord = array())
{
  if (!empty($_GET)) :
    foreach ($_GET as $key => $val) :
      if (in_array($key, $keyWord, true)) :
        debug('$_GETパラメータあり：' . print_r($val[$keyWord], true));
        return $val[$keyWord];
      else :
        return false;
      endif;
    endforeach;
  endif;
}


// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    送信ボタンフラグ取得関数
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function getSubmitName($str)
{
  switch ($str):
    case 'product-release':
      $submitName = 'release';
      break;
    case 'product-draft':
      $submitName = 'draft';
      break;
    case 'product-delete':
      $submitName = 'delete';
      break;
    default:
      $submitName = '';
  endswitch;
  debug('送信ボタンの名前：' . $submitName);
  return $submitName;
}


// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    バリデーション関数
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// バリデーション時にエラーが発生した際に格納されたエラーメッセージから
// keyと一致するものを返す
function getErrMessage($key)
{
  global $err_msg;
  if (!empty($err_msg[$key])) :
    return $err_msg[$key];
  endif;
}
// エラーメッセージが空白でなかった場合、エラーのCSSを適用させるための
// クラス名を返して表示
function getErrClass($key)
{
  global $err_msg;
  if (!empty($err_msg[$key])) :
    return 'err';
  endif;
}

// バリデーション・未入力チェック
function validRequired($str, $key)
{
  if ($str === '') :
    global $err_msg;
    $err_msg[$key] = MSG01;
  endif;
}
// バリデーション・全角チェック
function validFullWidth($str, $key)
{
  if (!preg_match("/^[^\x20-\x7e]*$/u", $str)) :
    global $err_msg;
    $err_msg[$key] = MSG11;
  endif;
  // /^(?:[ぁ-んァ-ヶー]|[\p{Han}][\x{E0100}-\x{E01EF}\x{FE00}-\x{FE02}]?)+$/u
}
// バリデーション・全角カタカナチェック
function validKatakana($str, $key)
{
  if (!preg_match("/^[ァ-ヾ]+$/u", $str)) :
    global $err_msg;
    $err_msg[$key] = MSG12;
  endif;
  // /^[ァ-ヶー]+$/u
  // "/^[ァ-ヾ０-９]+$/u"  ← 全角数字もＯＫ

}
// バリデーション・半角数字チェック
function validHalfNumber($str, $key)
{
  if (!preg_match("/^[0-9]+$/", $str)) :
    global $err_msg;
    $err_msg[$key] = MSG16;
  endif;
}
// バリデーション・半角英数字チェック
function validHalfTextNumber($str, $key)
{
  if (!preg_match("/^[0-9a-zA-Z]+$/", $str)) :
    global $err_msg;
    $err_msg[$key] = MSG17;
  endif;
}
// バリデーション・先頭後尾空白文字チェック
function validTopEndSpace($str, $key)
{
  if (!preg_match("^\s*|\s*$", $str)) :
    global $err_msg;
    $err_msg[$key] = MSG18;
  endif;
}
// バリデーション・最小文字数チェック
function validMinLen($str, $key, $min = 6)
{
  if (mb_strlen($str) < $min) :
    global $err_msg;
    $err_msg[$key] = MSG22;
  endif;
}
// バリデーション・最大文字数チェック
function validMaxLen($str, $key, $max = 255)
{
  if (mb_strlen($str) > $max) :
    global $err_msg;
    $err_msg[$key] = MSG23;
  endif;
}
// バリデーション・郵便番号チェック
function validZip($str, $key)
{
  if (!preg_match("/^\d{3}-\d{4}$/", $str)) :
    global $err_msg;
    $err_msg[$key] = MSG13;
  endif;
}
// バリデーション・携帯電話番号チェック
function validMobileTel($str, $key)
{
  // /(^090|^080|^070)[0-9]{8}$/i
  // if (!preg_match("/(^070|^080|^090)-\d{4}-\d{4}$", $str)) :
  if (!preg_match("/^0[1-9]0\-?\d{4}\-?\d{4}$/", $str)) :
    global $err_msg;
    $err_msg[$key] = MSG14;
  endif;
}
// バリデーション・パスワードチェック
function validPassword($str, $key)
{
  validHalfTextNumber($str, $key);
  validMinLen($str, $key);
  validMinLen($str, $key);
}
// バリデーション・パスワード同値チェック
function validMatch($str1, $str2, $key)
{
  if ($str1 !== $str2) :
    global $err_msg;
    $err_msg[$key] = MSG02;
  endif;
}
// バリデーション・金額, 送料
function validPriceCost($num, $key, $min, $max)
{
  validHalfNumber($num, $key);
  global $err_msg;
  if ($num < $min) :
    $err_msg[$key] = $min . MSG44;
  elseif ($num > $max) :
    $err_msg[$key] = number_format($max) . MSG45;
  endif;
}
// 半角カタカナチェック
function validHalfKana($str, $key)
{
  if (preg_match('/^[ｦ-ﾟｰ ]+$/u', $str)) :
    global $err_msg;
    $err_msg[$key] = MSG18;
  endif;
}

// バリデーション・Emailチェック
function validEmail($str, $key)
{
  if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)) :
    global $err_msg;
    $err_msg[$key] = MSG15;
  endif;
}
// バリデーション・セレクトボックスチェック
function validSelect($str, $key)
{
  global $err_msg;
  if ($str == 0) :
    $err_msg[$key] = MSG47;
  elseif (!preg_match("/^[0-9]+$/", $str)) :
    $err_msg[$key] = MSG42;
  endif;
}


// Email重複チェック
function validEmailDup($email)
{
  debug('Email重複チェックを行います。');
  global $err_msg;
  try {
    $dbh = dbConnect();
    $sql = 'SELECT count(*)
            FROM users
            WHERE email = :email AND delete_flg = 0
            ';
    $data = array(':email' => $email);
    $stmt = queryPost($dbh, $sql, $data, 'Email重複チェック');
    if ($stmt) :
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      debug('Email重複チェック情報取得成功。');
      debug('Email重複件数（$resultの中身）：' . print_r($result, true));
      if ($result['count(*)'] > 0) :
        debug('Email重複あり。');
        $err_msg['email'] = MSG31;
      else :
        debug('Email重複なし。');
      endif;
    else :
      debug('Email重複チェック情報取得失敗。失敗した関数：validEmailDup()');
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（Email重複チェック関数にて）：' . $e->getMessage());
    $err_msg['common'] = MSG99;
  }
}


// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    データベース接続・クエリ実行
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

//DB接続関数
function dbConnect()
{
  //DBへの接続準備

  // ● 開発用
  $dsn = 'mysql:dbname=nails_market;
          host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';

  $options = array(
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(指定した条件の情報を全件取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  // PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}

//  クエリ実行関数
function queryPost($dbh, $sql, $data, $from = '', $limitFlg = false)
{
  // SQL文がUPDATEまたはDELETEの場合は「LIMIT=1」を設定しているかチェック
  if ($limitFlg === false) :
  // $checkUpdate = (preg_match('/UPDATE/', $sql)) ? true : false;
  // $checkDelete = (preg_match('/DELETE/', $sql)) ? true : false;
  // $checkLimit = (preg_match('/LIMIT/', $sql)) ? true : false;
  // if (($checkUpdate || $checkDelete) && !$checkLimit) :
  //   debug('SQL文にUPDATEまたはDELETEが含まれています。LIMIT 1 を追記して全レコード削除を回避してください。');
  //   debug('クエリ中断。');
  //   debug('SQL文：' . $sql);
  //   debug('！！！！！ WHERE 句の有無も確認してください！！！！！！！！！！');
  //   return false;
  // endif;
  endif;
  // SQL文作成。サニタイズ自動
  $stmt = $dbh->prepare($sql);
  //プレースホルダに値をバインドしSQL文を実行
  if (!$stmt->execute($data)) {
    debug('queryPost関数にてクエリに失敗しました。呼び出し元：' . $from);
    debug('失敗したSQL：' . print_r($stmt, true));
    $err_msg['common'] = MSG99;
    return false;
  }
  debug('クエリ成功（queryPost関数）。');
  return $stmt;
}

// 🌟 整数の値をバインドしてクエリ実行する関数
function queryPostInt($dbh, $sql, $pgParamsArray)
{
  //クエリ作成
  $stmt = $dbh->prepare($sql);
  // 整数をバインド
  foreach ($pgParamsArray as $param_id => $value) {
    $stmt->bindValue($param_id, $value, PDO::PARAM_INT);
  }

  if (!$stmt->execute()) {
    debug('クエリに失敗しました。');
    debug('失敗したSQL：' . print_r($stmt, true));
    $err_msg['common'] = MSG99;
    return false;
  }
  debug('クエリ成功。');
  return $stmt;
}


// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    データベース 情報取得   - - ユーザー情報 - -
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// □ □ □ □ □ ユーザー情報取得 □ □ □ □ □
function getUser($u_id)
{
  debug('ユーザー情報を取得します。');
  try {
    $dbh = dbConnect();
    $sql = 'SELECT *
            FROM users
            WHERE id = :id AND delete_flg = 0
            ';
    $data = array(':id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data, 'ユーザー情報取得関数');
    if ($stmt) :
      // クエリ結果から１件のレコードを取り出して返す
      return $stmt->fetch(PDO::FETCH_ASSOC);
    else :
      debug('ユーザー情報取得失敗。失敗した関数：getUser()');
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（ユーザー情報取得関数にて）：' . $e->getMessage());
    $err_msg['common'] = MSG99;
  }
}

// □ □ □ □ □ 公開用ユーザー情報取得 □ □ □ □ □
function getUserOpenData($u_id)
{
  debug('公開用ユーザー情報を取得します。');
  try {
    $dbh = dbConnect();
    $sql = 'SELECT subName, profPic, profComment
            FROM users
            WHERE id = :id AND delete_flg = 0
            ';
    $data = array(':id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data, '公開用ユーザー情報取得関数');
    if ($stmt) :
      // クエリ結果から１件のレコードを取り出して返す
      return $stmt->fetch(PDO::FETCH_ASSOC);
    else :
      debug('ユーザー情報取得失敗。失敗した関数：getUserOpenData()');
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（公開用ユーザー情報取得関数にて）：' . $e->getMessage());
    $err_msg['common'] = MSG99;
  }
}

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    🌟 データベース 情報取得   - - お気に入り登録情報取得 - -
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function getFavoriteList($u_id)
{
  debug('お気に入り登録情報を取得します。');

  try {
    $dbh = dbConnect();
    $sql = 'SELECT f.product_id AS id, f.user_id, p.name, p.pic1
            FROM favorites AS f
            LEFT JOIN products AS p
            ON f.product_id = p.id
            WHERE f.user_id = :u_id AND p.delete_flg = 0
            ORDER BY f.update_date DESC
            ';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);
    if ($stmt) :
      debug('お気に入り登録情報の取得成功。');
      return $stmt->fetchAll();
    else :
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（お気に入り登録情報取得関数にて）：' . $e->getMessage());
    $err_msg['common'] = MSG99;
  }
}

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■     データベース 情報変更   - - 退会ID削除フラグtrue - -
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function userIdIsFalse($u_id, $tableName, $colName)
{
  try {
    $dbh = dbConnect();
    // お気に入りをすべて削除
    if ($tableName === 'favorites') :
      $sql = 'DELETE FROM ' . $tableName .
        ' WHERE ' . $colName . ' = :u_id
        ';
    // お気に入り以外は delete_flg = 1
    elseif ($tableName !== 'favorites') :
      $sql = 'UPDATE ' . $tableName .
        ' SET delete_flg = 1
          WHERE ' . $colName . ' = :u_id
          ';
    endif;

    debug('$sqlの中身：' . print_r($sql, true));
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data, '退会ID削除');
    if ($stmt) :
      debug('退会ID削除成功。 ' . 'DB：' . $tableName . ' カラム：' . $colName);
      return true;
    else :
      debug('退会ID削除に失敗しました。 ' . 'DB：' . $tableName . ' カラム：' . $colName);
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（退会ID削除フラグtrue関数にて）：' . $e->getMessage());
    $err_msg['common'] = MSG99;
  }
}


// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    🌟 データベース 情報取得   - - 履歴取得 - -
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function getHistory($u_id, $tranType, $tranFlg = false, $draftFlg = false, $dealFlg = false)
{
  debug('出品・購入の履歴情報を取得します。');

  try {
    // $sql = 'SELECT p.id, p.name, p.price, p.pic, p.sale_id, p.buy_id, p.tran_id,
    $dbh = dbConnect();
    $countSql = 'SELECT count(*)
                FROM products
                WHERE delete_flg = 0
                ';
    $dataSql = 'SELECT *
                FROM products
                WHERE delete_flg = 0
                ';

    $commonSql = '';
    // 出品下書き商品の場合
    if ($tranType === 'sale' && $draftFlg == true) :
      $commonSql .= ' AND sale_id = :u_id AND draft_flg = 1';
    // 出品した商品の場合
    elseif ($tranType === 'sale') :
      $commonSql .= ' AND sale_id = :u_id AND draft_flg = 0';
      // 売却済みの商品
      if ($dealFlg == true) :
        $commonSql .= ' AND deal_flg = 1';

      // お取引中の商品
      elseif ($tranFlg == true) :
        $commonSql .= ' AND tran_id IS NOT NULL AND deal_flg IS NULL';
      // お取引中ではない出品中の商品
      elseif ($tranFlg == false) :
        $commonSql .= ' AND tran_id IS NULL';
      endif;
    // 購入した商品の場合
    elseif ($tranType === 'buy') :
      $commonSql .= ' AND buy_id = :u_id';
      // 購入済みの商品
      if ($dealFlg == true) :
        $commonSql .= ' AND deal_flg = 1';
      // お取引中の商品
      elseif ($tranFlg == true) :
        $commonSql .= ' AND tran_id IS NOT NULL AND deal_flg IS NULL';
      endif;
    endif;
    // データ全件取得
    debug('$commonSqlの中身：' . $commonSql);
    $sql = $dataSql . $commonSql . ' ORDER BY update_date DESC';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);
    if ($stmt) :
      debug('出品・購入の履歴情報の取得成功。');
      return $stmt->fetchAll();
    else :
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（出品・購入の履歴情報取得関数にて）：' . $e->getMessage());
    $err_msg['common'] = MSG99;
  }
}




// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    データベース 情報取得   - -商品情報 - -
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// □ □ □ □ □ 自分が出品した商品IDの情報取得 □ □ □ □ □
function getProduct($u_id, $p_id)
{
  debug('商品情報を取得します。');
  debug('ユーザーID；' . $u_id);
  debug('商品ーID；' . $p_id);
  try {
    $dbh = dbConnect();
    $sql = 'SELECT *
            FROM products
            WHERE id = :id AND sale_id = :sale_id AND delete_flg = 0
            ';
    $data = array(':id' => $p_id, ':sale_id' => $u_id);
    debug('SQL文：' . $sql);
    debug('バインド値：' . print_r($data, true));
    $stmt = queryPost($dbh, $sql, $data, '商品情報取得関数');
    if ($stmt) :
      return $stmt->fetch(PDO::FETCH_ASSOC);
    else :
      debug('商品情報取得失敗。失敗した関数：getProduct()');
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（商品情報取得関数にて）：' . $e->getMessage());
  }
}

// □ □ □ □ □ 閲覧フリー用商品データ取得 □ □ □ □ □
function getProductRomFree($p_id)
{
  debug('閲覧可能な商品情報を取得します。');
  debug('商品ID；' . $p_id);
  try {
    $dbh = dbConnect();
    $sql = 'SELECT p.id, p.name, p.category_id, p.condition_id, p.detail, p.search_word, p.price,
            p.deli_cost, p.pic1, p.pic2, p.pic3, p.sale_id, p.buy_id, p.tran_id,
            p.pay_flg, p.get_flg, p.deal_flg, p.pay_date, p.get_date, p.dealEnd_date, p.draft_flg,
            p.delete_flg, p.create_date, p.update_date,
            cat.name AS categoryName, con.name AS conditionName, u.subName AS sellerName
            FROM products AS p
            INNER JOIN categorys AS cat
            ON p.category_id = cat.id
            INNER JOIN conditions AS con
            ON p.condition_id = con.id
            INNER JOIN users AS u
            ON p.sale_id = u.id
            WHERE p.id = :p_id AND p.draft_flg = 0 AND p.delete_flg = 0
            ';
    $data = array(':p_id' => $p_id);
    $stmt = queryPost($dbh, $sql, $data);
    if ($stmt) :
      return $stmt->fetch(PDO::FETCH_ASSOC);
    else :
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（閲覧可能の商品情報取得関数 getProductRom()にて）：' . $e->getMessage());
  }
}


// □ □ □ □ □ 出品商品の情報取得 □ □ □ □ □
function getProductRelease($u_id, $buy_id = '', $filter, $getDataCount)
{
  debug('出品した商品情報を取得します。');
  debug('ユーザーID（販売者ID）；' . $u_id);
  debug('抽出条件；' . $filter);
  try {
    $dbh = dbConnect();

    $sql = 'SELECT *
            FROM products
            WHERE sale_id = :sale_id AND delete_flg = 0
            ';
    $data = array(':sale_id' => $u_id);

    switch ($filter):
        // 出品した商品すべて（削除は除く）
      case 'allRelease':
        $sql = $sql;
        break;
        // 出品下書きのみ
      case 'only_draft_flg':
        $sql .= ' AND draft_flg = 1';
        break;
        // 取引完了した商品
      case 'tranEnd':
        $sql .= ' AND deal_flg = 1';
        break;
        // 取引中の商品
      case 'tranNow':
        $sql .= ' AND draft_flg = 0 AND deal_flg = 0 AND buy_id <> :buy_id';
        $data = array(':sale_id' => $u_id, ':buy_id' => 'NULL');
        break;
    endswitch;

    debug('SQL文：' . $sql);
    debug('バインド値：' . print_r($data, true));
    $stmt = queryPost($dbh, $sql, $data, '出品した商品の情報取得関数');

    if ($stmt) :
      return $stmt->fetchAll();
    else :
      debug('出品した商品の情報取得失敗。失敗した関数：getProductRelease()');
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（商品情報取得関数にて）：' . $e->getMessage());
  }
}


// □ □ □ □ □ 商品カテゴリ情報取得 □ □ □ □ □
function getCategory()
{
  debug('商品カテゴリの情報を取得します。');
  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM categorys
            WHERE delete_flg = 0
           ';
    $data = array();
    $stmt = queryPost($dbh, $sql, $data, '商品カテゴリ情報取得関数');
    if ($stmt) :
      return $stmt->fetchAll();
    else :
      debug('商品情報取得失敗。失敗した関数：getCategory()');
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（商品カテゴリリスト取得関数にて）：' . $e->getMessage());
  }
}

// □ □ □ □ 商品コンディション情報取得 □ □ □ □ □
function getCondition()
{
  debug('商品コンディションの情報を取得します。');
  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM conditions
            WHERE delete_flg = 0
           ';
    $data = array();
    $stmt = queryPost($dbh, $sql, $data, '商品コンディション情報取得関数');
    if ($stmt) :
      return $stmt->fetchAll();
    else :
      debug('商品情報取得失敗。失敗した関数：getCondition()');
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（商品コンディションリスト取得関数にて）：' . $e->getMessage());
  }
}


// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    データベース 情報取得   - - フリーワード検索 - -
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// フリーワード検索 クエリ実行
function queryPostSchWord($dbh, $sql, $pgParamsArray = array(), $arrayLike = array(), $arraySchWords)
{
  //クエリ作成
  $stmt = $dbh->prepare($sql);

  // 制すをバインド
  if (!empty($pgParamsArray)) :
    // 整数をバインド
    foreach ($pgParamsArray as $param_id => $value) {
      $stmt->bindValue($param_id, $value, PDO::PARAM_INT);
    }
  endif;

  // 文字列をバインド
  if (!empty($arrayLike)) :
    for ($i = 0; $i < count($arrayLike); $i++) :
      $bindLike = '%' . $arraySchWords[$i] . '%';
      debug('$bindLikeの中身：' . print_r($bindLike, true));
      $stmt->bindValue(':searchWord' . $i, $bindLike, PDO::PARAM_STR);
    // $stmt->bindValue(':searchWord', $bindLike, PDO::PARAM_STR);
    endfor;
  endif;

  if (!$stmt->execute()) {
    debug('クエリに失敗しました。');
    debug('失敗したSQL：' . print_r($stmt, true));
    $err_msg['common'] = MSG99;
    return false;
  }
  debug('クエリ成功。');
  return $stmt;
}


function getProductListSchWord($currentPageHead = 1, $arraySchWords, $sch_radio, $notSoldOut, $viewCount = 20)
{
  $arrayLike = [];
  $likeSql = '';
  debug('商品一覧表示用データを取得します。');
  try {
    $dbh = dbConnect();
    // 共通SQL文
    $commonSql = 'FROM products WHERE';
    if (!empty($notSoldOut)) :
      $commentSql1 = 'buy_id IS NULL AND';
    else :
      $commentSql1 = '';
    endif;
    $commentSql2 = '';
    // 受け取った検索ワードの配列の順にlike句を生成。プレースホルダに番号をつける
    // 同時にlike句のSQL文を連結していく
    for ($i = 0; $i < count($arraySchWords); $i++) :
      $arrayLike[$i] = ' search_word LIKE :searchWord' . $i;
      if ($i !== count($arraySchWords) - 1) :
        if ($sch_radio == 1) :
          $arrayLike[$i] .= ' AND';
        else :
          $arrayLike[$i] .= ' OR';
        endif;
      endif;

      $commentSql2 .= ' draft_flg = 0 AND delete_flg = 0 AND ' . $commentSql1 . $arrayLike[$i];

    endfor;
    debug('$arrayLikeの中身：' . print_r($arrayLike, true));
    debug('$connectSql2の中身：' . $commentSql2);
    $sql = 'SELECT count(*) ' . $commonSql . $commentSql2;
    debug('トータル件数のSQL文：' . $sql);

    // トータル件数取得
    $stmt = queryPostSchWord($dbh, $sql, $pgParams = array(), $arrayLike, $arraySchWords);

    if ($stmt) :
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $result['total_count'] = $result['count(*)'];
      // 総ページ数 ÷ １ページの表示件数 = 表示ページの母数 （あまりを表示するために切り上げ）
      $result['total_page'] = (int) ceil($result['total_count'] / $viewCount);
    else :
      return false;
    endif;

    // ページング用SQL文
    $pgSql = ' LIMIT :viewCount OFFSET :currentPageHead';
    // ワード検索の場合
    $sql = 'SELECT * ' . $commonSql . $commentSql2;
    $sql .= $pgSql;
    $pgParams = array(':viewCount' => $viewCount, ':currentPageHead' => $currentPageHead);
    debug('ページングのSQL文：' . $sql);
    debug('$pgParamsの中身：' . print_r($pgParams, true));
    // 整数をバインドする用の関数呼び出し
    $stmt = queryPostSchWord($dbh, $sql, $pgParams, $arrayLike, $arraySchWords);

    if ($stmt) :
      $result['getList'] = $stmt->fetchAll();
      return $result;
    else :
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（トップページの商品一覧情報取得関数にて）：' . $e->getMessage());
  }
}



// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    データベース 情報取得   - - メッセージ - -
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// □ □ □ □ 商品詳細ページのメッセージ取得 □ □ □ □
function getMsgProduct($p_id, $recordCount = false)
{
  try {
    $dbh = dbConnect();
    // レコード数取得の場合
    if ($recordCount) :
      debug('商品詳細ページのメッセージの件数を取得します。');
      $sql = 'SELECT count(*)
            FROM pdt_messages AS pm
            LEFT JOIN users AS u
            ON pm.send_id = u.id
            WHERE product_id = :p_id AND pm.delete_flg = 0
            ORDER BY pm.send_date ASC
          ';
      $data = array(':p_id' => $p_id);
      $stmt = queryPost($dbh, $sql, $data, '商品詳細ページメッセージ件数取得');
      if ($stmt) :
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count(*)'];
      else :
        return false;
      endif;

    // メッセージ取得の場合
    else :
      debug('商品詳細ページのメッセージを取得します。');
      $sql = 'SELECT pm.id, pm.product_id, pm.send_id, pm.send_date, pm.msg,
            u.id, u.subName, u.profPic
            FROM pdt_messages AS pm
            LEFT JOIN users AS u
            ON pm.send_id = u.id
            WHERE product_id = :p_id AND pm.delete_flg = 0
            ORDER BY pm.send_date ASC
          ';
      $data = array(':p_id' => $p_id);
      $stmt = queryPost($dbh, $sql, $data, '商品詳細ページ全メッセージ件数取得');
      if ($stmt) :
        return $stmt->fetchAll();
      else :
        return false;
      endif;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（商品詳細ページのメッセージ取得関数にて）：' . $e->getMessage());
  }
}

// □ □ □ □ お取引画面のメッセージ取得 □ □ □ □
function getMegTransaction($t_id, $recordCount = false)
{
  try {
    $dbh = dbConnect();
    // レコード数取得の場合
    if ($recordCount) :
      debug('お取引ページのメッセージの件数を取得します。');
      $sql = 'SELECT count(*)
              FROM tran_messages
              WHERE tran_id = :tran_id
              ORDER BY send_date ASC
              ';
      $data = array(':tran_id' => $t_id);
      $stmt = queryPost($dbh, $sql, $data, 'お取引画面メッセージ件数取得');
      if ($stmt) :
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count(*)'];
      else :
        return false;
      endif;

    // メッセージ取得の場合
    else :
      $sql = 'SELECT *
              FROM tran_messages
              WHERE tran_id = :tran_id
              ORDER BY send_date ASC
              ';
      $data = array(':tran_id' => $t_id);
      $stmt = queryPost($dbh, $sql, $data, 'お取引画面全メッセージ取得');
      if ($stmt) :
        return $stmt->fetchAll();
      else :
        return false;
      endif;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（お取引画面のメッセージ取得関数にて）：' . $e->getMessage());
  }
}

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    データベース 情報取得 お取引画面
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function getTransaction($t_id)
{
  debug('お取引情報を取得します。');
  try {
    $dbh = dbConnect();
    $sql = 'SELECT *
            FROM transactions
            WHERE id = :t_id AND delete_flg = 0
            ';
    $data = array(':t_id' => $t_id);
    $stmt = queryPost($dbh, $sql, $data, 'お取引画面');
    if ($stmt) :
      return $stmt->fetch(PDO::FETCH_ASSOC);
    else :
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（お取引情報取得関数にて）：' . $e->getMessage());
  }
}


// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    データベース 情報取得 トップページ商品一覧、ページング
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// 1.まずはページ読み込み時の全件数を取得（指定条件がる場合はその条件での件数）
// 2.ページング用のデータを取得し、指定の各ページに表示する

function getProductList($currentPageHead = 1, $category, $sort, $notSoldOut, $viewCount = 20)
{
  debug('商品一覧表示用データを取得します。');
  try {
    $dbh = dbConnect();
    // 共通SQL文
    $commonSql = 'FROM products
                  WHERE draft_flg = 0 AND delete_flg = 0
                  ';
    // 項目検索の場合
    if (!empty($notSoldOut)) :
      $commonSql .= ' AND buy_id IS NULL';
    endif;
    if (!empty($category)) :
      $commonSql .= ' AND category_id = ' . $category;
    endif;
    if (!empty($sort)) :
      switch ($sort):
          // case1は昇順、case2は降順、case０はソートなし
        case 1:
          $commonSql .= ' ORDER BY price ASC';
          break;
        case 2:
          $commonSql .= ' ORDER BY price DESC';
          break;
      endswitch;
    endif;
    // 表示する商品一覧の件数を取得
    $sql = 'SELECT count(*) ' . $commonSql;
    $data = array();

    // トータル件数取得
    debug('トータル件数のSQL文：' . $sql);
    $stmt = queryPost($dbh, $sql, $data, '商品一覧情報取得関数ートータル件数');
    if ($stmt) :
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $result['total_count'] = $result['count(*)'];
      // 総ページ数 ÷ １ページの表示件数 = 表示ページの母数 （あまりを表示するために切り上げ）
      $result['total_page'] = (int) ceil($result['total_count'] / $viewCount);
    else :
      return false;
    endif;

    // ページング用SQL文
    $sql = 'SELECT *  ' . $commonSql;
    $sql .= ' LIMIT :viewCount OFFSET :currentPageHead';
    $pgParams = array(':viewCount' => $viewCount, ':currentPageHead' => $currentPageHead);
    debug('ページングのSQL文：' . $sql);
    debug('$pgParamsの中身：' . print_r($pgParams, true));
    // 整数をバインドする用の関数呼び出し
    $stmt = queryPostInt($dbh, $sql, $pgParams, $data);
    if ($stmt) :
      $result['getList'] = $stmt->fetchAll();
      return $result;
    else :
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（トップページの商品一覧情報取得関数にて）：' . $e->getMessage());
  }
}


// ページネーション
function pagination($currentPage, $totalPage, $link = '', $indexViewCount = 5)
{
  // $totalPage・・・総ページ数
  // $indexViewCount・・・表示インデックス数
  // $indexMin・・・インデックス最小値
  // $indexMax・・・インデックス最小値

  // 総ページ数が表示するインデックス数よりも多い場合
  if ($totalPage > $indexViewCount) :
    // 現在のページが総ページ数と同じなら、左にリンク４個出す(最終ページ)
    if ($currentPage === $totalPage) :
      $indexMin = $currentPage - 4;
      $indexMax = $currentPage;
    // 現在のページが表示インデックス数の１ページ前なら、左にリンク３個、右に１個出す
    elseif ($currentPage === ($totalPage - 1)) :
      $indexMin = $currentPage - 3;
      $indexMax = $currentPage + 1;
    // 現在のページが表示インデックス数の2ページ前なら、左にリンク2個、右に2個出す
    elseif ($currentPage === ($totalPage - 2)) :
      $indexMin = $currentPage - 2;
      $indexMax = $currentPage + 2;
    // 現在のページが３ページ目なら、左にリンク2個、右に２個出す
    elseif ($currentPage === 3) :
      $indexMin = $currentPage - 2;
      $indexMax = $currentPage + 2;
    // 現在のページが2ページ目なら、左にリンク１個、右に３個出す
    elseif ($currentPage === 2) :
      $indexMin = $currentPage - 1;
      $indexMax = $currentPage + 3;
    // 現在のページが１ページ目なら、左にリンクなし、右に５個出す
    elseif ($currentPage === 1) :
      $indexMin = 1;
      $indexMax = $indexViewCount;
    endif;
  // 総ページ数が表示するインデックス数より少ない場合
  elseif ($totalPage <= $indexViewCount) :
    $indexMin = 1;
    $indexMax = $totalPage;
  // それ以外
  else :
  // $indexMin = $currentPage + 2;
  // $indexMax = $currentPage - 2;
  endif;


  // <!-- ページネーション -->
  echo '<div class="pagination-wrap">';
  echo '<ul class="pagination-list">';
  // １ページ目以外の場合は「<」に１ページのパラメータをつける
  if ($currentPage !== 1) :
    echo '<li class="list-item"><a href="?p=1' . $link . '"><i class="fas fa-angle-left"></i></a></li>';
  endif;

  for ($i = $indexMin; $i <= $indexMax; $i++) :
    // インデックスの先頭から終わりまでのlijタグを生成。
    // 生成したインデックスが現在のページならクラスactiveを追加。
    echo '<li class="list-item"><a ';
    if ($currentPage === $i) :
      echo 'class="active"';
    endif;
    echo 'href="?p=' . $i . $link . '">' . $i . '</a></li>';
  // echo 'href="' . $getParam . 'p=' . $i . '">' . $i . '</a></li>';
  endfor;
  if ($currentPage !== $indexMax && $indexMax > 1) :
    echo '<li class="list-item"><a href="?p=' . $indexMax . $link . '"><i class="fas fa-angle-right"></i></a></li>';
  endif;
  echo '</ul>';
  echo '</div>';
}


// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    データベース 情報取得 お気に入り登録の状況
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function getFavoriteInfo($u_id, $p_id)
{
  debug('お気に入り登録があるか確認します。');
  debug('ユーザーID：' . $u_id);
  debug('商品ID：' . $p_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT count(*)
            FROM favorites
            WHERE product_id = :p_id AND user_id = :u_id
            ';
    $data = array(':p_id' => $p_id, ':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data, 'お気に入り登録情報取得関数');
    if ($stmt) :
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($result['count(*)'] > 0) :
        debug('お気に入り登録しています。');
        return true;
      else :
        debug('お気に入りではありません。');
        return false;
      endif;
    else :
      debug('クエリ失敗。お気に入り登録情報を返せません。');
      return false;
    endif;
  } catch (Exception $e) {
    error_log('例外エラー発生（お気に入り登録情報取得関数にて）：' . $e->getMessage());
  }
}



// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    メール送信
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function sendMail($from, $to, $subject, $comment)
{
  if (!empty($to) && !empty($subject) && !empty($comment)) :
    // 文字化けしないように設定（お決まりパターン）
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");
    // メールを送信（結果はtrueまたはfalse）
    $result = mb_send_mail($to, $subject, $comment, "From: " . $from);
    if ($result) :
      debug('メール送信が成功しました。');
    else :
      debug('メール送信エラー：メール送信に失敗しました。');
    endif;
  endif;
}

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    乱数生成
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// □ □ □ □ □ 認証キー生成 □ □ □ □ □
function makeRandKey($length = 8)
{
  $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
  $str = '';
  for ($i = 0; $i < $length; $i++) :
    $str .= $chars[mt_rand(0, 61)];
  endfor;
  return $str;
}

// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    画像処理
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

// □ □ □ □ □ □ 登録画像表示（ない場合はno-imageを表示） □ □ □ □ □ □
function showImage($path)
{
  if (!empty($path)) :
    return sanitize($path);
  else :
    return sanitize('images/no_image2.png');
  endif;
}

// □ □ □ □ □ □ 画像アップロード □ □ □ □ □ □
// 引数 $file・・・$_FILE
function upLoadImg($file, $key)
{
  debug('画像アップロード開始。');
  debug('$_FILESの中身：' . print_r($file, true));
  // アップロード時のエラーコードがある場合
  // エラーコードは0〜8なので、issetで判定
  if (isset($file['error']) && is_int($file['error'])) :
    try {
      // 画像ファイルのバリデーション。$file['error']の中身を確認
      switch ($file['error']):
          // 0.エラーなしOK。エラーなしの場合のみbreakで抜ける
        case UPLOAD_ERR_OK:
          break;
          // 4. ファイル未選択
        case UPLOAD_ERR_NO_FILE:
          debug('ファイルが選択されていません。');
          throw new Exception('ファイルが選択されていません');
          // 1. php.iniで定義したファイル最大サイズ(サーバー)が超過した場合
        case UPLOAD_ERR_INI_SIZE:
          debug('サーバーに保管できるファイルのサイズが超過しています。');
          throw new RuntimeException('ファイルのサイズが超過しています。');
          // 2. HTMLで指定した最大ファイルサイズを超過した場合
        case UPLOAD_ERR_FORM_SIZE:
          debug('HTMLで指定したファイルのサイズが超過しています。');
          throw new RuntimeException('ファイルのサイズが超過しています。（3MBまで）');
          // その他のエラーの場合
        default:
          debug('その他のエラーが発生しました。');
          throw new RuntimeException('その他のエラーが発生しました。');
      endswitch;

      // MIMEタイプのチェック（ブラウザ側で偽装可能なためチェックしておく）
      // 指定したファイルタイプ以外の場合は例外をスローする
      $type = @exif_imagetype($file['tmp_name']);
      if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) :
        throw new RuntimeException('画像形式が未対応です。');
      endif;

      // SHA256でファイル名をハッシュして保存。
      // 「image_type_to_extension」は第２引数なしはデフォルトでtrue（拡張子の前にドットがつく）
      $path = 'uploads/' . hash_file('SHA256', $file['tmp_name']) . image_type_to_extension($type);
      debug('画像ファイルのパス：' . $path);
      // ファイルを一時保管場所から指定した保管場所へ移動する
      if (!move_uploaded_file($file['tmp_name'], $path)) :
        throw new RuntimeException('ファイル保存時にエラーが発生しました。');
      endif;

      // 保存したファイルのパーミッションを変更する
      if (!chmod($path, 0644)) :
        debug('パーミッション変更時にエラーが発生しました。');
        throw new RuntimeException('パーミッション変更時にエラーが発生しました。');
      else :
        debug('ファイルが正常にアップロードされました。');
        debug('ファイルパス：' . $path);
        return $path;
      endif;
    } catch (RuntimeException $e) {
      // 例外エラー時は自作した各エラーメッセージを格納して表示
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }

  endif;
}


// インフォメーションメッセージ
function getInfoMessage($str)
{
  if (!empty($str)) :
    switch ($str) {
      case 'パスワード変更メール':
        return INFO1;
        break;
      case 'パスワード変更':
        return INFO2;
        break;
      case '退会':
        return INFO3;
        break;
      default:
        return false;
        break;
    }
  endif;
}



// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
// ■    入力フォーム送信後の入力データ保持。
// ■    入力エラーがある場合、DBの情報または入力したデータを返して、
// ■    ページリロード後もフォーム送信前の入力データ保持。
// ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

function getFormData($key, $dbFormData = array())
{
  global $err_msg;

  // POST送信されていない場合(入力エラーはありえない)
  if (empty($_POST)) :
    // DBにデータがある場合はDBのデータを返す。データがない(未定義)の場合は何も返さない
    if (isset($dbFormData[$key])) :
      return sanitize($dbFormData[$key]);
    endif;
  // POST送信されている場合
  elseif (!empty($_POST)) :
    // 入力エラーがある場合はフォームのデータを返す
    if (!empty($err_msg[$key]) && isset($_POST[$key])) :
      return sanitize($_POST[$key]);
    // DBにデータがある場合
    elseif (isset($dbFormData[$key])) :
      // ※ 画像ファイルはPOST送信されないので$_POSTの未定義の条件も入れる
      if (isset($_POST[$key]) && $dbFormData[$key] !== $_POST[$key]) :
        return sanitize($_POST[$key]);
      else :
        return sanitize($dbFormData[$key]);
      endif;
    // POST送信あり、かつDBにデータがない場合はフォームのデータを返す
    // ※ 画像ファイルはPOST送信されないので$_POSTの未定義の条件も入れる
    elseif (isset($_POST[$key])) :
      return sanitize($_POST[$key]);
    endif;
  endif;
}

// 画像アップロード用のフォーム維持

function getFormDataImg($key, $delete_flg = false, $img = '', $dbFormData = array())
{
  //  POST送信されている場合
  if (!empty($_POST)) :

    // 画像削除をクリックしている場合
    if ($delete_flg === true) :
      if (!empty($img)) :
        return $img;
      else :
        return '';
      endif;

    // 画像削除をクリックしていない場合
    else :
      // DBにデータがあり、かつ画像アップロードしている場合は
      // アップロードのパスを返す
      if (!empty($dbFormData[$key])) :
        if (!empty($img) && ($dbFormData[$key] !== $img)) :
          return $img;
        else :
          // アップロードしていない場合はDBのパスを返す
          return $dbFormData[$key];
        endif;
      // データベースにデータがない場合はアップロードのパスを返す
      else :
        return $img;
      endif;
    endif;

  //POST送信していない場合
  else :
    // DBにデータがある場合はDBのパスを返す
    if (!empty($dbFormData[$key])) :
      return $dbFormData[$key];
    else :
      return $img;
    endif;
  endif;
}


// GET送信した場合、サニタイズして返す
function getFormDataByGet($key)
{
  if (isset($_GET[$key])) :
    return sanitize(($_GET[$key]));
  endif;
}