<a href="index.php" class="logo">Fanon</a>
<nav class="nav">
    <a class="nav-link" href="index.php?access=<?php if(isset($_GET['access'])){ echo $_GET['access']; } ?>">Accueil</a>
    <?php if(!isset($_SESSION['user'])){ ?>
    <a class="nav-link" href="inscription.php?access=<?php if(isset($_GET['access'])){ echo $_GET['access']; } ?>">Inscription</a>
    <?php }
    else if (isset($_SESSION['user']) && $_GET['access'] == 'forbidden'){ ?>
    <a class="nav-link" href="login.php?access=<?= $_GET['access'] ?>">Login</a>
    <?php }
    else {?>
    <a class="nav-link" href="profil.php?access=<?php if(isset($_GET['access'])){ echo $_GET['access']; } ?>">Profil</a>
    <?php } ?>
    <a class="nav-link" href="panier.php?access=<?php if(isset($_GET['access'])){ echo $_GET['access']; } ?>">Panier</a>
</nav>