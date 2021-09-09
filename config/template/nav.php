<?php
$quantite=panierquantite();
?>


<a href="index.php" class="logo">Fanon</a>
<nav class="nav">
    <a class="nav-link" href="index.php">Accueil</a>
    <?php if(connecte()){?>
      <a class="nav-link" href="profil.php">Profil</a>
      <a class="nav-link" href="index.php?session=destroy">
        <img height="20px" width="20px" src="asset/img/logout.svg" class="logout-icon" alt="se-deconnecter-bouton">
    </a>
    <?php if($_SESSION['user']['statut'] === 'admin'){ ?>
      <a class="nav-link" href="admin.php">Admin</a>
    <?php } }else{ ?>  
      <a class="nav-link" href="login.php">Connexion</a>
    <?php } ?> 
    <a class="nav-link panier" href="panier.php">
      Panier
      <span><?= $quantite ?></span>
    </a>
</nav>