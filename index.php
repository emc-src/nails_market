<!DOCTYPE html>

<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <title>Entrance | nails market</title>
  <link rel="stylesheet" type="text/css" href="style.css" />
  <!-- googleフォント -->
  <link href="https://fonts.googleapis.com/css?family=Ruda:400,900&display=swap" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,800&display=swap" rel="stylesheet"
    type="text/css" />
  <link href="https://fonts.googleapis.com/css?family=Kosugi+Maru&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=ABeeZee&family=Caveat&family=Indie+Flower&display=swap"
    rel="stylesheet">

  <link
    href="https://fonts.googleapis.com/css2?family=ABeeZee&family=Caveat&family=Indie+Flower&family=Kalam:wght@300&display=swap"
    rel="stylesheet">
  <!-- フォントアイコン -->
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet" />
</head>

<body>
  <div class="entrance-container site-width">
    <div class="entrance">
      <h1 class="entrance__title js-title">nails market</h1>
      <div class="entrance__item">
        <p class="entrance__item-en entrance__item-en-1 js-item-en-1">Nail Tips</p>
        <p class="entrance__item-en entrance__item-en-2 js-item-en-2">and</p>
        <p class="entrance__item-en entrance__item-en-3 js-item-en-3">Accessories</p>
      </div>
      <p class="entrance__item-ja-1 js-item-ja-1">ネイル用品・ハンドメイドアクセサリー販売</p>
      <p class="entrance__item-ja-2 js-item-ja-2">マーケットへ移動します</p>
    </div>
  </div>

  <script src="js/jquery.min.js"></script>

  <script>
  $(window).on('load', function() {
    setTimeout(function() {
      $('.js-title').delay(600).animate({
        opacity: 1
      }, 3000);
      $('.js-item-en-1').delay(1600).animate({
        opacity: 1
      }, 2500);
      $('.js-item-en-2').delay(2400).animate({
        opacity: 1
      }, 2500);
      $('.js-item-en-3').delay(3200).animate({
        opacity: 1
      }, 2500);
      $('.js-item-ja-1').delay(4000).animate({
        opacity: 1
      }, 2500);
      $('.js-item-ja-2').delay(5300).animate({
        opacity: 1,
        'font-size': 24
      }, 2500);
      $('body').delay(7800).animate({
        opacity: 0
      }, 2500);
      // homeへリダイレクト
      setTimeout(function() {
        location.href = "home.php"
      }, 10500)
    })
  });
  </script>
</body>

</html>