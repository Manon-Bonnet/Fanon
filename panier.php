<?php include 'config/template/head.php'; 
suppsessioncommane();

if(!panierquantite()==0){
  //On enregistre le prix total de tous les articles
  $prixtotal=montantpanier();

  $urlerreur="";
  $backgroudurl="";
  $backgroudqt="";
  $qterreur="";

  //On vérifie qu'il y ait supprimer dans l'url
  if(isset($_GET['supprimer'])){
    //On vérifie qu'il y a un produit avec l'id demande enregistre dans session[panier]
    //C'est pour éviter que quelqu'un tape n'importe que dans l'url et qu'une eerreur appraraise
    $idsupprimer=preg_replace('~\D~', '', $_GET['supprimer']);
    if(isset($_SESSION['panier'][$idsupprimer])){
      //On appel la fonction supprimerpanier avec l'id du produit en question en paramètre
      supprimerpanier($idsupprimer);
      //on remet à jour le montant total
      $prixtotal=montantpanier();
    }
    else{
      //si elle n'existe pas c'est que quelqu'un a modifié l'url alors on affiche une erreur
      $urlerreur="Merci de ne pas toucher à l'url";
      $backgroudurl="style='background:tomato;padding:2%'";
    }
  }
  //On vérifie qu'il y ait moins dans l'url
  if(isset($_GET['moins'])){
    //On vérifie qu'il y a un produit avec l'id demande enregistre dans session[panier]
    //C'est pour éviter que quelqu'un tape n'importe que dans l'url et qu'une eerreur appraraise
    $idmoins=preg_replace('~\D~', '', $_GET['moins']);
    if(isset($_SESSION['panier'][$idmoins])){
      //On appel la fonction moins avec l'id du produit en question en paramètre
      moins($idmoins);
      //on remet à jour le montant total
      $prixtotal=montantpanier();
    }
    //si elle n'existe pas c'est que quelqu'un a modifié l'url alors on affiche une erreur
    else{
      $urlerreur="Merci de ne pas toucher à l'url";
      $backgroudurl="style='background:tomato;padding:2%'";
    }
  }
  //On vérifie qu'il y ait plus dans l'url
  if(isset($_GET['plus'])){
    //On vérifie qu'il y a un produit avec l'id demande enregistre dans session[panier]
    //C'est pour éviter que quelqu'un tape n'importe que dans l'url et qu'une eerreur appraraise
    $idplus=preg_replace('~\D~', '', $_GET['plus']);
    if(isset($_SESSION['panier'][$idplus])){
      //On appel la fonction plus avec l'id du produit en question en paramètre
      $resultat=plus($idplus);
      //Si la fonction n'a rencontré aucune erreur => le nombre de produit demandé est inferieur au stock donc on peut ajouter ce produit au panier
      if($resultat==false){
        //on remet à jour le montant total
        $prixtotal=montantpanier();
      }
      //Sinon cela veut dire qu'il n'y a pas assez de stock donc qu'on ne peut pas ajouter ce produit au panier
      else{
        //On affiche un message d'errur pour informer l'utilisateur
        $qterreur="Il n'y a pas assez de stock pour le produit : ".$_SESSION['panier'][$idplus]['nom'];
        $backgroudqt="style='background:tomato;padding:2%'";
      }
    }
    //si elle n'existe pas c'est que quelqu'un a modifié l'url alors on affiche une erreur
    else{
      $urlerreur="Merci de ne pas toucher à l'url";
      $backgroudurl="style='background:tomato;padding:2%'";
    }
  }
  if(isset($_SESSION['user']['id'])){
    $idcommandearray=recupereIDcommandeBDD($_SESSION['user']['id']);
    $idcommande=$idcommandearray[0]['id_commande'];
  }else{
    $idcommande="";
  }
}


?>
<header>
    <?php include 'config/template/nav.php'; ?>
</header>
<section class="sectionar">
    <h1 class="text-center mt-5 mb-5">Page panier</h1>
    <?php if(panierquantite()==0){?>
      <p class="text-center">Votre panier est vide</p>
      <a href="index.php#produits" class="btnclassique mr-auto ml-auto mb-4">Nos acticles</a> 
    <?php }else{?>
      <div <?=$backgroudurl?>><?=$urlerreur?></div>
      <div <?=$backgroudqt?>><?=$qterreur?></div>
      <ul class="panier-liste">
        <?php foreach($_SESSION['panier'] AS $idproduit){?>
          <li class="panier-produit p-3 <?= $idproduit['nom'] ?>">
              <div class="ml-3">
                  <h2><?= $idproduit['nom'] ?></h2>
                  <p><?= $idproduit['prix'] ?> €</p>
                    <div class="panier-quantite">
                    <a href="panier.php?plus=id<?=$idproduit['id']?>" class="quantitemodif">+</a>
                      <p class="pr-2 pl-2"><?= $idproduit['quantite'] ?></p>
                      <a href="panier.php?moins=id<?=$idproduit['id']?>" class="quantitemodif">-</a>
                      <a href="panier.php?supprimer=id<?=$idproduit['id']?>" class="supression_produit btnclassique">Supprimer</a>
                    </div>
              </div>
              <img src="<?= $idproduit['photo']?>" alt="<?= $idproduit['nom']?>">
          </li>
        <?php } ?>
      </ul>

      <div class="panier-total text-center mb-5">
          <div class="d-flex justify-content-between mt-3">
              <p>Prix total :</p>
              <p><?= $prixtotal ?> €</p>
          </div>
          <form action="payement.php" method='post'>
              <input class="w-100" type="submit" name="acheter" value="Passer à l'achat">
              <input name="idcommande" value="<?=$idcommande?>" class="hidden">
          </form>
      </div>
    <?php }?>
    
</section>

<?php include 'config/template/footer.php'; ?>