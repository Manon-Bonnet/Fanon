
<?php include 'config/template/head.php';
suppsessioncommane();
//On récupère l'id du produit qu'on a mis dans l'url
$idurl = $_GET['id'];
$id= preg_replace('~\D~', '', $idurl);

//Définition des variables contenent les erreurs comme étant vide au départ
$content="";
$backgroud="";


//On récupère l'id_produit demandé dans la base de donnée
$existeid=verifurlficheproduit($id);
//Si il n'y a pas $existeid est vide, donc qu'elle n'existe pas
if(!isset($existeid['id_produit'])){
  //si elle n'existe pas c'est que quelqu'un a modifié l'url alors on redirige
  header("location:index.php");
}else{
  //On appel une fonction qui nous retourne sous forme de tableau la liste des informations du produit concerné
  $unproduit = infosproduits($id);


  //On regarde si quelqu'un a cliqué sur le btn ajouter au panier
  if(isset($_POST['ajout_panier'])){
    //On enregistre la session avec les informations du produit et on affiche un messag pour informer l'utilisateur
    $content= setProduit($unproduit, $id);
    //Si le message d'info n'est pas celui de l'erreur alors le fond du message d'information sera vert
    //Donc si il ya du stock =succes
    if($content!="Il n'y a plus de stock, votre produit ne peut être ajouter au panier"){
      $backgroud="style='background:chartreuse;padding:2%'";
    }
    //Si le message d'erreur est celui de l'erreur alors le fond du message d'information sera vert
    //Donc si il ya du plus de stock =echec
    else{
      $backgroud="style='background:tomato;padding:2%'";
    }
  }
}

?>

<header>
    <?php include 'config/template/nav.php'; ?>
</header>
<nav class="ariane">
    <a href="./index.php">Accueil </a> 
    <p> > <?= $unproduit[0]['nom_produit'] ?></p>
</nav>
<section class="produit-header">
    <div>
        <h1 class="text-center mt-0 mb-5"><?=$unproduit[0]['nom_produit']?></h1>
        <p class="prix">Prix : <?=$unproduit[0]['prix']?> €</p>
        <p>Stock : <?=$unproduit[0]['stock']?></p>
        <h2>Description</h2>
        <p><?=$unproduit[0]['description_produit']?></p>
        <form action="fiche_produit.php?id=id<?=$id?>" method="post">
          <div <?=$backgroud?>><?=$content?></div>
          <input type="submit" value="Ajouter au panier" name="ajout_panier">
        </form>
    </div>
    <div class="images-produit">
        <?php if(isset($unproduit[0]['url_image'])){ ?>
        <img class="main_produit_img" src="<?=$unproduit[0]['url_image']?>" alt="<?=$unproduit[0]['nom_produit']?>">
        <figure>
            <!--On crée une boucle for pour afficher toute les photos restantes-->
            <?php
            for($j=0; $j<count($unproduit); $j++){
            ?>
              <img class='second_produit_img' id="<?=$j?>" src="<?=$unproduit[$j]['url_image']?>" alt="<?=$unproduit[0]['nom_produit']?>">
            <?php
            }
            ?>
        </figure>
        <?php } else{ ?>
        <img class="main_produit_img" src="asset/img/produits/no-pic.png" alt="no-picture-available-for-product">
        <?php } ?>
    </div>
</section>

<script src="asset/script/ficheproduit.js"></script>
<?php include 'config/template/footer.php'; ?>