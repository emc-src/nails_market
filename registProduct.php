<?php
//共通変数・関数ファイルを読込み
require('function.php');
debug('');
debug('＜ ＜ ＜　 画面処理開始 ここから　 ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜ ＜');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「　　商品出品　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

$_SESSION['pic_delete'] = (!empty($_SESSION['pic_delete'])) ? $_SESSION['pic_delete'] : false;
$_SESSION['pic1_delete'] = (!empty($_SESSION['pic1_delete'])) ? $_SESSION['pic1_delete'] :  '';
$_SESSION['pic2_delete'] = (!empty($_SESSION['pic2_delete'])) ? $_SESSION['pic2_delete'] :  '';
$_SESSION['pic3_delete'] = (!empty($_SESSION['pic3_delete'])) ? $_SESSION['pic3_delete'] :  '';


$u_id = $_SESSION['user_id'];
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
$myProductData = (!empty($p_id)) ? getProduct($u_id, $p_id) : array();
debug('商品ID：' . $p_id);
debug('商品データ（$myProductData）の中身：' . print_r($myProductData, true));
// GETパラメータで受けとった商品IDがDBに登録なしの場合、GETパラメータを
// 改ざんしたということなので、マイページへリダイレクト
if (!empty($p_id) && empty($myProductData)) :
  debug('GETパラメータの商品IDがDBに登録がありません。');
  debug('マイページへ遷移します。');
  header("Location:mypage.php");
  exit();
endif;
// GETパラメータで受けとった商品IDが削除されている場合、GETパラメータを
// 改ざんしたということなので、マイページへリダイレクト
if (!empty($myProductData['delete_flg'])) :
  debug('GETパラメータの商品IDは削除されています。');
  header("Location:mypage.php");
  exit();
endif;
$dbCategoryData = getCategory();
$dbConditionData = getCondition();
debug('カテゴリデータ（$dbCategoryData）の中身：' . print_r($dbCategoryData, true));
debug('商品状態データ（$dbConditionData）の中身：' . print_r($dbConditionData, true));
// 商品IDがあれば編集、なければ新規登録
$edit_flg = (!empty($p_id)) ? true : false;

$pic1 = (!empty($myProductData['pic1'])) ? $myProductData['pic1'] : '';
$pic2 = (!empty($myProductData['pic2'])) ? $myProductData['pic2'] : '';
$pic3 = (!empty($myProductData['pic3'])) ? $myProductData['pic3'] : '';

// POST送信されている場合
if (!empty($_POST)) :
  // 送信ボタン名取得
  $submitName = getSubmitName($_POST['push-submit'], $edit_flg);
  if (!empty($submitName)) :
    debug('POST送信があります。');
    debug('$_POSTの中身：' . print_r($_POST, true));
    debug('$_FILESの中身：' . print_r($_FILES, true));
    // 変数にフォームのデータを代入
    $category_id = $_POST['category_id'];
    $condition_id = $_POST['condition_id'];
    $name = $_POST['name'];
    $price = (empty($_POST['price']) ? 0 : $_POST['price']);
    $deli_cost = (empty($_POST['deli_cost']) ? 0 : $_POST['deli_cost']);
    $detail = $_POST['detail'];
    $search_word = $_POST['search_word'];
    // 画像をアップロードしパスを格納
    $pic1 = (!empty($_FILES['pic1']['name']) ? upLoadImg($_FILES['pic1'], 'pic1') : '');
    // 入力フォームに画像のパスがない（アップロードなし）かつDBに画像がある場合はDBの画像のパスを格納。
    // 入力フォームに画像のパスがない、かつDBにも画像がない場合は空を格納。（画像なし）
    $pic2 = (!empty($_FILES['pic2']['name']) ? upLoadImg($_FILES['pic2'], 'pic2') : '');
    $pic3 = (!empty($_FILES['pic3']['name']) ? upLoadImg($_FILES['pic3'], 'pic3') : '');
    // $pic3 = (empty($pic3) && !empty($myProductData['pic3'])) ? $myProductData['pic3'] : $pic3;

    $pic1 = getFormDataImg('pic1', $_SESSION['pic1_delete'], $pic1, $myProductData);
    $pic2 = getFormDataImg('pic2', $_SESSION['pic2_delete'], $pic2, $myProductData);
    $pic3 = getFormDataImg('pic3', $_SESSION['pic3_delete'], $pic3, $myProductData);

    // 入力チェック
    validSelect($category_id, 'category_id');
    validSelect($condition_id, 'condition_id');
    validRequired($name, 'name');
    validFullWidth($name, 'name');
    validPriceCost($price, 'price', 200, 15000);
    validPriceCost($deli_cost, 'deli_cost', 0, 1000);
    validHalfKana($detail, 'detail');
    validHalfKana($search_word, 'search_word');

    if (empty($err_msg)) :
      debug('未入力チェックOK。');
      try {
        $dbh = dbConnect();
        // if ($submitName) :
        // 登録済み商品データ編集の場合
        if ($edit_flg) :
          $sql = 'UPDATE products
                  SET name = :name, category_id = :category_id, condition_id = :condition_id,
                  detail = :detail, search_word = :search_word, price = :price, deli_cost = :deli_cost,
                  pic1 = :pic1, pic2 = :pic2, pic3 = :pic3
                  ';
          // 送信ボタン判定
          switch ($submitName):
              // 出品公開する場合
            case 'release':
              $sql .= ', draft_flg = 0';
              break;
              // 出品下書き保存の場合
            case 'draft':
              $sql .= ', draft_flg = 1';
              break;
              // 削除の場合
            case 'delete':
              $sql = 'UPDATE products
                      SET delete_flg = 1
                      ';
              break;
          endswitch;
          // SQL文追記（３ボタン共通）
          $sql .= ' WHERE id = :p_id AND sale_id = :u_id LIMIT 1';
          // 出品と下書きのプレースホルダーバインド
          if ($submitName === 'release' || $submitName === 'draft') :
            $data = array(
              ':name' => $name, ':category_id' => $category_id, ':condition_id' => $condition_id,
              ':detail' => $detail, ':search_word' => $search_word, ':price' => $price, ':deli_cost' => $deli_cost,
              ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3,
              ':p_id' => $p_id, ':u_id' => $u_id
            );

          // 削除の場合
          else :
            $data = array(':p_id' => $p_id, ':u_id' => $u_id);
          endif;

        // 新規登録の場合
        else :
          $sql = 'INSERT INTO products
                  (name, category_id, condition_id, detail, search_word, price, deli_cost,
                  pic1, pic2, pic3, sale_id, draft_flg, delete_flg, create_date)
                  VALUES (
                  :name, :category_id, :condition_id, :detail, :search_word, :price, :deli_cost,
                  :pic1, :pic2, :pic3, :sale_id
                  ';
          // 送信ボタン判定
          switch ($submitName):
              // 出品公開する場合
            case 'release':
              $sql .= ', 0, 0, :create_date';
              break;
              // 出品下書き保存の場合
            case 'draft':
              $sql .= ', 1, 0, :create_date';
              break;
          endswitch;
          $sql .= ')';
          $data = array(
            ':name' => $name, ':category_id' => $category_id, ':condition_id' => $condition_id,
            ':detail' => $detail, ':search_word' => $search_word, ':price' => $price, ':deli_cost' => $deli_cost,
            ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':sale_id' => $u_id,
            ':create_date' => date('Y-m-d H:i:s')
          );
        endif;
        debug('SQL文：' . $sql);
        debug('バインド値：' . print_r($data, true));
        $stmt = queryPost($dbh, $sql, $data, '商品登録編集ページ');
        // クエリ成功の場合
        if ($stmt) :
          switch ($submitName):
              // 出品公開する場合
            case 'release':
              $_SESSION['msg_success'] = SUC06;
              break;
              // 出品下書き保存の場合
            case 'draft':
              $_SESSION['msg_success'] = SUC08;
              break;
              // 削除の場合
            case 'delete':
              $_SESSION['msg_success'] = SUC09;
              break;
          endswitch;
          debug('クエリ成功。マイページへ遷移します。');

          // セッション削除
          unset($_SESSION['pic_delete']);
          unset($_SESSION['pic1_delete']);
          unset($_SESSION['pic2_delete']);
          unset($_SESSION['pic3_delete']);
          debug('$_SESSIONの中身：' . print_r($_SESSION, true));

          // 新規の場合は$p_idに追加した商品IDを代入
          $p_id = (!$edit_flg) ? $dbh->lastInsertId() : $p_id;
          if ($submitName === 'release') :
            header("Location:productDetail.php?p_id=" . $p_id);
            exit();
          else :
            header("Location:mypage.php");
            exit();
          endif;
        endif;
      } catch (Exception $e) {
        error_log('例外エラー発生（商品登録編集ページにて）：' . $e->getMessage());
        $err_msg['common'] = MSG99;
      }
    endif;
  endif;
endif;

// 送料が空白の場合は0、空白でない場合はgetFormData関数の結果を代入
$deli_cost = (empty($deli_cost)) ? 0 : getFormData('deli_cost', $myProductData);


debug('＞ ＞ ＞ ＞ ＞　 画面処理終了 ここまで　 ＞ ＞ ＞ ＞ ＞ ＞ ＞');
?>


<?php
$siteTitle = ($edit_flg) ? '登録商品編集' : '商品新規登録';
require('head.php');
?>

<body class="page-productEdit page-2colum">
  <!--  - -ヘッダー - - -->
  <?php
  require('header.php');
  ?>

  <!-- - - メインコンテンツ - - -->
  <div id="contents-wrap" class="site-width">
    <div class="main-side-wrap">
      <section id="main">
        <div class="form-wrap">
          <h1 class="title">商品登録編集</h1>
          <!-- - - 入力フォーム - - -->
          <form action="" method="post" class="form" enctype="multipart/form-data">
            <!-- - - 共通のエラーメッセージ - - -->
            <div class="area-msg common-msg">
              <?php echo getErrMessage('common', $myProductData); ?>
            </div>

            <!-- - - カテゴリ - - -->
            <div class="form-group category">
              <label class="<?php echo getErrClass('category_id'); ?>"><span class="required">必須</span><span
                  class="title">商品カテゴリ</span>
                <select name="category_id" id="">
                  <option value="0" <?php if (getFormData('category_id', $dbCategoryData) === 0) : echo 'selected';
                                    endif; ?>>選択してください</option>
                  <!-- - - カテゴリデータからリストを取り出しすべて表示 - - -->
                  <?php foreach ($dbCategoryData as $key => $val) : ?>
                  <!-- カテゴリデータからリストを取り出しoptionに追加していく -->
                  <option value="<?php echo $val['id']; ?>" <?php
                                                              if ($val['id'] === getFormData('category_id', $myProductData)) :
                                                                echo 'selected';
                                                              endif;
                                                              ?>>
                    <!-- カ- - テゴリ名を表示 - - -->
                    <?php echo $val['name']; ?>
                  </option>
                  <?php endforeach;  ?>
                </select>
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('category_id'); ?>
              </div>
            </div>

            <!-- - - 商品の状態 - - -->
            <div class="form-group condition">
              <label class="<?php echo getErrClass('condition_id'); ?>"><span class="required">必須</span><span
                  class="title">商品の状態</span>
                <select name="condition_id" id="">
                  <option value="0" <?php if (getFormData('condition_id', $dbConditionData) === 0) : echo 'selected';
                                    endif; ?>>選択してください</option>
                  <!-- - -カテゴリデータからリストを取り出しすべて表示 - - -->
                  <?php foreach ($dbConditionData as $key => $val) : ?>
                  <!-- - - カテゴリデータからリストを取り出しoptionに追加していく - - -->
                  <option value="<?php echo $val['id']; ?>" <?php
                                                              if ($val['id'] === getFormData('condition_id', $myProductData)) :
                                                                echo 'selected';
                                                              endif;
                                                              ?>>
                    <!-- - - カテゴリ名を表示 - - -->
                    <?php echo $val['name']; ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('condition_id'); ?>
              </div>
            </div>

            <!-- - - 商品名 - - -->
            <div class="form-group">
              <label class="<?php getErrClass('name'); ?>"><span class="required">必須</span><span
                  class="title">商品名：</span><span class="comment">全角入力（30文字以内）</span>
                <input type="text" name="name" value="<?php echo getFormData('name', $myProductData); ?>"
                  maxlength="30">
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('name'); ?>
              </div>
            </div>

            <!-- - - 価格・送料 - - -->
            <div class="form-group form-group-price">
              <div class="price-cost">
                <!-- - - 価格 - - -->
                <div class="sale-price">
                  <label class="<?php echo getErrClass('price'); ?>"><span class="required">必須</span><span
                      class="title">価格：</span></span><span class="comment">200円〜15,000円</span>
                    <input type="number" name="price" value="<?php echo getFormData('price', $myProductData); ?>"
                      min="200" max="15000"><span class="yen">円</span>
                  </label>
                </div>
                <div class="area-msg">
                  <?php echo getErrMessage('price'); ?>
                </div>
                </br>
                <!-- - - 送料 - - -->
                <div class="deli-cost">
                  <label class="<?php echo getErrClass('deli_cost'); ?>"><span class="required">必須</span><span
                      class="title">送料：</span><span class="comment">1,000以内。無料の場合は0を入力。</span>
                    <input type="number" name="deli_cost" min="0" max="1000" value="<?php echo $deli_cost; ?>"><span
                      class="yen">円</span>
                  </label>
                </div>
                <div class="area-msg">
                  <?php echo getErrMessage('deli_cost'); ?>
                </div>
              </div>
            </div>
            <!-- - - 商品の説明 - - -->
            <div class="form-group js-counter">
              <label class="<?php echo getErrClass('detail'); ?>"><span class="any"">任意</span><span class="
                  title">商品の説明：</span><span class="comment">1000文字以内</span>
                <pre><textarea name="detail" id="js-count" cols="50" rows="20" maxlength="1000"><?php echo getFormData('detail', $myProductData); ?></textarea></pre>
              </label>
              <div class="area-msg">
                <?php echo getErrMessage('detail'); ?>
              </div>
              <p class="js-text-counter"><span id="js-count-view">0</span>/1000文字</p>
            </div>

            <!-- - - 検索ワード - - -->
            <div class="form-group search-word js-counter">
              <label class="<?php echo getErrClass('search_word') ?>"><span class="any"">任意</span><span class="
                  title">検索ワード：</span><span class="comment">スペースで区切って入力してください。100文字以内</span>
                <pre><textarea name="search_word" id="js-count2" cols="50" rows="3" maxlength="100"><?php echo getFormData('search_word', $myProductData); ?></textarea></pre>
              </label>
              <div class=" area-msg">
                <?php echo getErrMessage('search_word'); ?>
              </div>
              <p class="js-text-counter"><span id="js-count-view2">0</span>/100文字</p>
            </div>

            <!-- - - 商品画像 - - -->
            <div class="productPic-wrap">
              <!-- - - 商品画像-1 - - -->
              <div class="form-group pic1">
                <span class="any"">任意</span><span class=" title">画像１</span>
                <label class="error area-drop">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic1" class="fileInput-area"
                    value=""><span>ドラッグ&ドロップ<br />またはタップして選択<br />３MBまで</span></span><img
                    src="<?php echo getFormDataImg('pic1', $_SESSION['pic1_delete'], $pic1, $myProductData); ?>" alt=""
                    class="img-area img-src1">
                </label>
                <div class="area-msg">
                  <?php echo getErrMessage('pic1'); ?>
                </div>
                <p class="pic-delete js-delete-pic1">画像１を削除</p>
              </div>
              <!-- - - 商品画像-2 - - -->
              <div class="form-group pic2">
                <span class="any"">任意</span><span class=" title">画像２</span>
                <label class="error area-drop">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic2" class="fileInput-area"
                    value=""><span>ドラッグ&ドロップ<br />またはタップして選択<br />３MBまで</span><img
                    src="<?php echo getFormDataImg('pic2', $_SESSION['pic2_delete'], $pic2, $myProductData); ?>" alt=""
                    class="img-area img-src2">
                </label>
                <div class="area-msg">
                  <?php echo getErrMessage('pic2'); ?>
                </div>
                <p class="pic-delete js-delete-pic2">画像２を削除</p>
              </div>
              <!-- - - 商品画像-3 - - -->
              <div class="form-group pic3">
                <span class="any"">任意</span><span class=" title">画像３</span>
                <label class="error area-drop">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic3" class="fileInput-area"
                    value=""><span>ドラッグ&ドロップ<br />またはタップして選択<br />３MBまで</span></span><img
                    src="<?php echo getFormDataImg('pic3', $_SESSION['pic3_delete'], $pic3, $myProductData); ?>" alt=""
                    class="img-area img-src3">
                </label>
                <div class="area-msg">
                  <?php echo getErrMessage('pic3'); ?>
                </div>
                <p class="pic-delete js-delete-pic3">画像３を削除</p>
              </div>
            </div>
            <!-- - - 送信ボタン - - -->
            <div class="btn-wrap">
              <!-- - - 商品出品ボタン  - - -->
              <div class="btn-container btn-productEdit btn-release">
                <?php if (empty($myProductData['buy_id'])) : ?>
                <button type="submit" id="product-release" name="push-submit" value="product-release"
                  onClick="return submitCheckOne()">商品を出品する</button>
                <?php endif; ?>
              </div>

              <!-- - - 商品登録（下書き）ボタン - - -->
              <div class="btn-container btn-productEdit btn-draft">
                <?php if (empty($myProductData['buy_id'])) : ?>
                <button type="submit" id="product-draft" name="push-submit" value="product-draft"
                  onClick="return submitCheckOne()">下書きに保存する</button>
                <?php endif; ?>
              </div>
              <?php if (!empty($myProductData['buy_id'])) : ?>
              <p class="note-edit">こちらの商品は販売済み商品のため内容を編集できません。</p>
              <?php endif; ?>

              <!-- - - 商品登録削除ボタン - - -->
              <?php if (($edit_flg === true && empty($myProductData['buy_id'])) || (!empty($myProductData['tran_id']) && !empty($myProductData['deal_flg']))) : ?>
              <div class="btn-container btn-productEdit btn-delete">
                <button type="submit" id="product-delete" class="js-product-delete" name="push-submit"
                  value="product-delete">商品を削除する</button>
              </div>
              <?php endif; ?>
              <?php if (!empty($myProductData['tran_id']) && empty($myProductData['deal_flg'])) : ?>
              <p class="note-delete">こちらの商品はお取引中です。<br />削除する場合はお取引を完了してください。</p>
              <?php endif; ?>
            </div>

          </form>
        </div>
      </section>

      <!-- - - サイドバー（右） - - -->
      <?php
      require('sidebar_mypage.php')
      ?>
    </div>
    <!-- - - トップページへのリンク - - -->
    <section>
      <div class="link">
        <a href="home.php"><span class="fas fa-home"></span>&ensp;トップページへ</a>
      </div>
    </section>
  </div>
  <?php
  require('footer.php');
  ?>