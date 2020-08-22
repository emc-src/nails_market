<header>
  <div class="header-width">
    <h1>
      <a href="index.php">nails market</a>
    </h1>
    <nav id="top-nav">
      <ul>
        <!-- ログインしている場合 -->
        <?php if (!empty($_SESSION['user_id'])) : ?>
        <li><a href="logout.php "><i class="fas fa-sign-out-alt"></i>&ensp;ログアウト</a></li>
        <li><a href="mypage.php"><i class="fas fa-user"></i>&ensp;マイページ</a></li>
        <!-- ログインしていない場合 -->
        <?php else : ?>
        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i>&ensp;ログイン</a></li>
        <li><a href="signup.php "><i class="fas fa-id-card-alt"></i>&ensp;ユーザー登録</a></li>
        <?php endif; ?>
        <li><a href="home.php "><span class="fas fa-home"></span>&ensp;トップページ</a></li>
      </ul>
    </nav>
  </div>
</header>


<!-- <?php
      if (empty($_SESSION['user_id'])) :
      ?>
          <li><a href="signup.php "></a>新規登録</li>
          <li><a href="login.php"></a>ログイン</li>
        <?php
      else :
        ?>
          <li><a href="mypage.php">マイページ</a></li>
          <li><a href="logout.php">ログアウト</a></li>
        <?php endif; ?> -->