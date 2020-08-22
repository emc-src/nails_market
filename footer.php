<footer id="footer">
  Copyright &copy; <a href="index.php">nails market</a>．All Rights Reserved.
</footer>

<script src="js/jquery.min.js"></script>

<script>
$(function() {

  var errMsgAjax01 = 'データを更新しました。';
  var errMsgAjax02 = 'データを更新できませんでした。';
  var errMsgAjax03 = 'エラーが発生しました。しばらく経ってからやり直してください';

  // 余分なスペースを削除
  function replaceDust(replaceText) {
    return replaceText.replace(/^[\s　]+|[\s　]+$/g, "");
  }

  var $win = $(window);
  //  ○ ○ ○ ○ ○ フッターを最下部に固定 ○ ○ ○ ○ ○
  var $ftr = $('#footer');
  if ($win.innerHeight() > $ftr.offset().top + $ftr.outerHeight()) {
    $ftr.attr({
      'style': 'position:fixed; top:' + ($win.innerHeight() - $ftr.outerHeight()) + 'px;'
    });
  }

  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
  // ■    アニメーションメッセージ表示
  // ■    デフォルトはdisplay:noneにしておき、slideToggleで非表示を表示にする
  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

  var $jsShowMsg = $('#js-show-msg');
  var msg = replaceDust($jsShowMsg.text());

  // HTML内の余計なゴミ(タブ、スペース)を取り除き、正しいメッセージのみにしてから判定
  if (msg.length) {
    $jsShowMsg.slideToggle('slow');
    setTimeout(function() {
      $jsShowMsg.slideToggle('slow');
    }, 3000);
  }

  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
  // ■    テキストカウンター
  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

  // ○ ○ ○ ○ ○ テキストカウンター ○ ○ ○ ○ ○
  function textCounter(jsCount, jsCountView) {
    var $countUp = $(jsCount),
      $countView = $(jsCountView);
    $countUp.on('keyup', function(e) {
      $countView.html($(this).val().length);
    });
  };

  //  ○ ○ ○ ○ ○ DBから取得したテキストの文字数取得 ○ ○ ○ ○ ○
  function dbTextCounter(jsCountView, jsCount) {
    var $countView = $(jsCountView); //$テキストエリア取得
    textLength = $(jsCount).text().length;
    $countView.html(textLength);
  };

  // call : テキストカウンター関数
  textCounter('#js-count', '#js-count-view');
  textCounter('#js-count2', '#js-count-view2');

  // call : DBから取得したテキストの文字数取得関数呼び出し
  dbTextCounter('#js-count-view', '#js-count');
  dbTextCounter('#js-count-view2', '#js-count2');



  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
  // ■    画像ライブプレビュー
  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

  // 画像ライブプレビュー
  var $dropArea = $('.area-drop');
  var $fileInput = $('.fileInput-area');
  $dropArea.on('dragover', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '3px #ccc dashed');
  });
  $dropArea.on('dragleave', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', 'none');
  });
  $fileInput.on('change', function(e) {
    $dropArea.css('border', 'none');
    var file = this.files[0], // 2. files配列にファイルが入っています
      $img = $(this).siblings('.img-area'), // 3. jQueryのsiblingsメソッドで兄弟のimgを取得
      fileReader = new FileReader(); // 4. ファイルを読み込むFileReaderオブジェクト

    // 5. 読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
    fileReader.onload = function(e) {
      // 読み込んだデータをimgに設定
      $img.attr('src', e.target.result).show();
    };

    // 6. 画像読み込み
    fileReader.readAsDataURL(file);
  });

  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
  // ■    画像削除
  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

  function picDelete(imgSrc, deletePic, fileInputArea) {
    var $srcImg = $(imgSrc),
      $picDelete = $(deletePic),
      $inputAreaFile = $(fileInputArea);
    $picDelete.on('click', function(e) {
      $srcImg.attr('src', '');
      $inputAreaFile.val('');

      $.ajax({
        type: "POST",
        url: "ajaxRegistProduct.php",
        // dataType: 'json',
        data: {
          picDeleteFlg: true,
          pushButton: $picDelete.text(),
        }
      }).done(function(data) {

      }).fail(function(msg) {
        alert('AjaxError');
      });

    });
  };

  // call : 画像削除関数呼び出し
  picDelete('.img-src1', '.js-delete-pic1', '.fileInput-area');
  picDelete('.img-src2', '.js-delete-pic2', '.fileInput-area');
  picDelete('.img-src3', '.js-delete-pic3', '.fileInput-area');



  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
  // ■    メイン画像とサブ画像の切り替え
  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

  var $switchImgMain = $('.js-switch-img-main'),
    $switchImgSub = $('.js-switch-img-sub');
  $switchImgSub.on('click', function(e) {
    $switchImgMain.attr('src', $(this).attr('src'));
  });



  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
  // ■    取引進捗状況更新
  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

  var $okPayment = $('.js-payment-ok'),
    $getProduct = $('.js-product-get'),
    $tranEnd = $('.js-tran-end'),
    $createDate1 = $('.create-date1'),
    $createDate2 = $('.create-date2'),
    $createDate3 = $('.create-date3'),
    $concludeMsg = $('.conclude-msg'),
    $sendMsg = $('.send-message'),
    $btnMsgSend = $('.btn-msgSend');


  // ○ ○ ○ ○ ○ 取引進捗状況 - 入金確認 ○ ○ ○ ○ ○

  $okPayment.on('click', function(e) {

    var $iconClick = $('js-icon-click1'),
      $this = $(this);
    // 余分なスペースを削除したテキストにて比較
    var replTextPay = replaceDust($this.text()),
      replTextEnd = replaceDust($tranEnd.text());
    if (replTextPay === 'クリックして入力（出品者入力）') {
      if (confirm('入金確認済みに変更します。よろしいですか？\n※ 変更したら元に戻せなくなります。')) {
        $.ajax({
          type: "POST",
          url: "ajaxTransaction.php",
          dataType: 'json',
          data: {
            tranChange: "payment",
          }
        }).done(function(data) {
          if (data['change_user'] === 'no_match') {
            alert('出品者のみ入力できます。');
          } else if (data['result'] === true) {
            $this.text('出品者がご入金を確認しました');
            $this.addClass('entered-payment');
            $iconClick.css('display', 'none');
            $createDate1.text(data['date']);
            alert(errMsgAjax01);
            // } else if (data['change_user'] === 'no_match') {
            //   alert('出品者のみ入力できます。');
          } else {
            alert(errMsgAjax02);
          }
        }).fail(function(msg) {
          alert(errMsgAjax03);
        });
      }
    } else if (replTextEnd === '出品者がお取引を完了しました') {
      alert('この商品はお取引が完了しています');
    } else if (replTextPay === '出品者がご入金を確認しました') {
      alert('入金確認ずみです');
    }
  });


  //  ○ ○ ○ ○ ○ 取引進捗状況 - 商品受領確認 ○ ○ ○ ○ ○

  $getProduct.on('click', function(e) {

    var $iconClick = $('js-icon-click2'),
      $this = $(this);
    // 余分なスペースを削除したテキストにて比較
    var replTextGet = replaceDust($this.text());
    var replTextPay = replaceDust($okPayment.text());
    var replTextEnd = replaceDust($tranEnd.text());

    if (replTextPay === 'クリックして入力（出品者入力）') {
      alert('ご入金の確認が入力されていないため入力できません');
    } else if (replTextPay === '出品者がご入金を確認しました' && replTextGet === 'クリックして入力（購入者入力）') {
      if (confirm('商品受領済みに変更します。よろしいですか？\n※ 変更したら元に戻せなくなります。')) {
        $.ajax({
          type: "POST",
          url: "ajaxTransaction.php",
          dataType: 'json',
          data: {
            tranChange: "getProduct",
          }
        }).done(function(data) {
          if (data['result'] === true) {
            $this.text('購入者が商品を受け取りました');
            $this.addClass('entered-getProduct');
            $iconClick.css('display', 'none');
            $createDate2.text(data['date']);
            alert(errMsgAjax01);
          } else if (data['change_user'] === 'no_match') {
            alert('購入者のみ入力できます。');
          } else {
            alert(errMsgAjax02);
          }
        }).fail(function(msg) {
          alert(errMsgAjax03);
        });
      }
    } else if (replTextEnd === '出品者がお取引を完了しました') {
      alert('この商品はお取引が完了しています');
    } else if (replTextGet === '購入者が商品を受け取りました') {
      alert('商品受領確認済みです');
    }
  });

  //  ○ ○ ○ ○ ○ 取引進捗状況 - 取引終了確認 ○ ○ ○ ○ ○

  $tranEnd.on('click', function(e) {

    var $iconClick = $('js-icon-click3'),
      $this = $(this);

    // 余分なスペースを削除する
    var replTextEnd = replaceDust($this.text());
    var replTextGet = replaceDust($getProduct.text());
    var replTextPay = replaceDust($okPayment.text());

    if (replTextGet === 'クリックして入力（購入者入力）') {
      alert('商品の受け取り確認ができていないため入力できません');
    } else if (replTextGet === '購入者が商品を受け取りました' && replTextEnd === 'クリックして入力（出品者入力）') {
      if (confirm('お取引を完了します。よろしいですか？\n※ 変更したら元に戻せなくなります。')) {
        $.ajax({
          type: "POST",
          url: "ajaxTransaction.php",
          dataType: 'json',
          data: {
            tranChange: "tranEnd",
          }
        }).done(function(data) {
          if (data['result'] === true) {
            $this.text('出品者がお取引を完了しました');
            $this.addClass('entered-tranEnd');
            $iconClick.css('display', 'none');
            $createDate3.text(data['date']);
            $concludeMsg.css('display', '');
            $sendMsg.css('display', 'none');
            $btnMsgSend.css('display', 'none');
            alert(errMsgAjax01);
          } else if (data['change_user'] === 'no_match') {
            alert('出品者のみ入力できます。');
          } else {
            alert(errMsgAjax02);
          }
        }).fail(function(msg) {
          alert(errMsgAjax03);
        });
      }
    } else if (replTextEnd === '出品者がお取引を完了しました') {
      alert('この商品はお取引が完了しています');
    }
  });


  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
  // ■    商品出品・編集画面 ボタン
  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

  var $submitDelete = $('.js-product-delete');
  $submitDelete.on('click', function(e) {
    $(this).blur();
    var checked = confirm('この商品を削除します。よろしいですか？\n※ 削除すると商品一覧と履歴に表示されなくなります。');
    if (checked == true) {
      return true;
    } else {
      return false;
    }
  });

  //  ○ ○ ○ ○ ○ 出品者の場合は商品詳細のボタンから商品編集画面への移動可 ○ ○ ○ ○ ○

  var $toDetail = $('.js-to-detail'),
    $toEditPage = $('.js-to-editPage');
  $toDetail.on({
    'mouseenter': function() {
      $toEditPage.text('▶ 商品編集ページへ');
    },
    'mouseleave': function() {
      $toEditPage.text('▶ 商品を編集する');
    }
  });


  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
  // ■    メッセージ欄
  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

  //  ○ ○ ○ ○ ○ メッセージ未入力チェック ○ ○ ○ ○ ○

  // ＊入力ボックスの内容はtextではなくvalで取得
  function submitNoMsg(submitText, pushSubmit) {
    var $submitText = $(submitText),
      $pushSubmit = $(pushSubmit);
    $pushSubmit.on('click', function(e) {
      $(this).blur();
      if ($submitText.val().length === 0) {
        alert('メッセージの入力がありません');
        return false;
      };
    });
  }

  // call: メッセージ未入力チェック関数呼び出し
  submitNoMsg('.js-input-cmt', '.js-btn-cmt');
  submitNoMsg('.js-input-msg', '.js-btn-msg');


  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■
  // ■    お気に入り登録削除
  // ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■

  // 動的に作成するノードはnull判定してから変数に代入する
  // null、undefined・・・数値の０をtrue判定する(０はfalseと判定されるため)
  // undefinedは判定には使用可だが変数への代入は使用しないようにする

  var $favorite,
    $clickFav,
    $favText,
    favProductId;
  $favorite = $('.js-click-favo') || null;
  $clickFav = $('.js-favo');
  $favText = $('.js-favo-text');
  favProductId = $clickFav.data('productid') || null;
  //このif判定は不要だが学習のために入れてある
  if (favProductId !== undefined && favProductId !== null) {
    $favorite.on('click', function() {
      var $this = $(this);
      $.ajax({
        type: "POST",
        url: "ajaxFavorite.php",
        data: {
          productId: favProductId,
        }
      }).done(function(data) {
        $clickFav.toggleClass('active');
        $favText.toggleClass('active');
        if ($favText.text() === 'お気に入りに登録') {
          $favText.text('お気に入りを削除');
        } else if ($favText.text() === 'お気に入りを削除') {
          $favText.text('お気に入りに登録');
        }
      }).fail(function(msg) {
        alert('errMsgAjax03');
      });
    });
  }



  // ■ ■ jQuery最後のとじカッコ ■ ■
});

// JavaScript
function submitCheck() {
  document.activeElement.blur();
  var submitFlag = confirm("送信してもよろしいですか？");
  return submitFlag;
}

function submitCheckOne() {
  document.activeElement.blur();
  var checked = confirm("送信してもよろしいですか？");
  if (checked == true) {
    return true;
  } else {
    return false;
  }
}


// ここまで
</script>
</body>

</html>