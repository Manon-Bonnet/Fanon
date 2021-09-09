<?php 
include 'config/template/head.php'; 
suppsessioncommane();
//Définition des variables contenent les erreurs comme étant vide au départ
$erreur="";
$backgroud="";

//si l'utilisateur n'est pas conneté il ne peut pas acceder à cette page il est donc rediriger vers la page login
if(!connecte()){
  header("location:login.php");
  die();
}

//Si on envoie le formulaire on va vérifier quelques informations
if(isset($_POST['changer'])){
  extract($_POST);
  //On regarde si le pseudo et le mdp saisis ne sont pas enregistrés dans notre base de donnée
  $erreur = changemdp($mdp_actuel, $mdp_nouveau, $_SESSION['user']['id']);
  $backgroud = 'style="background:tomato;padding:2%"';
  //Si l'enregistrement s'est bien passé on redirige vers la page profil
  if($erreur == ""){
    header("location:profil.php");
  }
}

?>
<header>
    <?php include 'config/template/nav.php'; ?>
</header>
  <section class="sectformulaire">
    <h1 class="text-center mt-5 mb-5">Changement de mot de passe</h1>
    <div <?=$backgroud?>><?=$erreur?></div>

    <form class="formulaire formulairemdp" action="mot_de_passe.php" method="post">
      <input type="password" name="mdp_actuel" id="mdp_actuel" class="filter" placeholder="Mot de passe actuel *">
      <input type="password" name="mdp_nouveau" id="mdp_nouveau" class="filter" placeholder="Nouveau mot de passe *">
      <input type="submit" value="Changer" name="changer">
    </form>

    <a href="profil.php">Annuler</a>
  </section>

<?php include 'config/template/footer.php'; ?>