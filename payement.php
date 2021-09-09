<?php include 'config/template/head.php';
//Si on arrive sur cette page pour la première fois
if(!isset($_SESSION['commande']) AND isset($_POST['idcommande'])){
  $idcommandepost=$_POST['idcommande'];
  $idcommandeok=verifidcommande($idcommandepost);
  if(!isset($idcommandeok['id_user'])){
    header("location:panier.php");
    print_r($idcommandeok);
    die();
  }
  $_SESSION['commande'] = $idcommandepost;
  $idcommande = $_SESSION['commande'];
}
//Si on actualise la page
else{
  $idcommande = $_SESSION['commande'];
}
//Si on touche l'url
if(!isset($_SESSION['commande'])){
  header("location:panier.php");
  die();
}

$prixtotal=montantfinal($idcommande);
$commande=payement($idcommande);
$usercommande =personnecommande($idcommande);
?>

<header>
<?php include 'config/template/nav.php'; ?>
</header>

<section class="sectionpayment">
    <h1 class="text-center mt-5 mb-5">Paiement Effectué</h1>
    <section>
      <h2 class="mt-5 mb-5">Recap de la commande :</h2>
      <ul>
        <?php foreach($commande AS $unproduit){?>
          <li class="payement-produit p-3 <?= $unproduit['nom_produit'] ?>">
              <div class="ml-3 produitpayement">
                <p class="pr-2 pl-2"><?= $unproduit['quantite'] ?> x "<?= $unproduit['nom_produit'] ?>" à <?= $unproduit['prix'] ?> €</p>
                <p class="pr-2 pl-2 soustotal"> <?= $unproduit['quantite']*$unproduit['prix'] ?></p>
              </div>
          </li>
        <?php } ?>
      </ul>
      <div class="mb-5 ml-5 mt-3">
        <p>Prix total :</p>
        <p><?= $prixtotal['total_commande'] ?> €</p>
      </div>
    </section>
    <section>
      <h2 class="mt-5 mb-5">Personne qui a commande :</h2>
      <ul>
        <?php foreach($usercommande AS $userinfos){?>
          <li>Commandé par : <?= $userinfos['pseudo'] ?></li>
          <li>Doit être livré à cette adresse : <?= $userinfos['numero_rue'].' '.$userinfos['nom_rue'].' '.$userinfos['cp'].' '.$userinfos['ville'] ?></li>
          <li>Adresse mail : <?= $userinfos['email'] ?></li>
          <li>Téléphone : <?= $userinfos['tel']?></li>
        <?php } ?>
      </ul>
    </section>
</section>

<?php include 'config/template/footer.php'; ?>