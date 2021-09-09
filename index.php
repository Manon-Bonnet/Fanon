<?php include 'config/template/head.php'; 
suppsessioncommane();
$produit = infosproduits(0);

?>
<header>
    <?php include 'config/template/nav.php'; ?>
</header>

<section class="hero-full-image">
  <h1>Fanon</h1>
  <a href="#produits" class="btnclassique">Nos produits</a>
</section>

<section id="produits" class="nosproduits">
  <h2>Nos produits</h2>
  <div class="troisarticles">
    <!--On crée une boucle for pour afficher répeter a chaque fois la même suite d'opérations avec les bons id-->
    <?php
      for($k=0; $k<count($produit); $k++){
    ?>
    <article>
      <figure>
        <?php if(isset($produit[$k]['url_image'])){ ?>
        <img class="articleimg" src="<?=$produit[$k]['url_image']?>" alt="<?=$produit[$k]['nom_produit']?>">
        <?php } else{ ?>
        <img class="articleimg" src="asset/img/produits/no-pic.png" alt="no-picture-available-for-product">
        <?php } ?>
        <figcaption><h3><?=$produit[$k]['nom_produit']?></h3></figcaption>
      </figure>
      <p>Prix : <?=$produit[$k]['prix']?> €</p>
      <p>Stock : <?=$produit[$k]['stock']?></p>
      <!-- modifier L'ID !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! -->
      <a href="fiche_produit.php?id=id<?=$produit[$k]['id_produit']?>" class="btnclassique">Voir l'article</a>
    </article>
    <?php
      }
    ?>
  </div>
</section>

<?php include 'config/template/footer.php'; ?>