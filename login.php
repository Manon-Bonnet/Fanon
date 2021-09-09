<?php 
include 'config/template/head.php';
suppsessioncommane();
//Définition des variables contenent les erreurs comme étant vide au départ
$content=""; 
$backgroud="";

//si l'utilisateur est pas conneté il ne peut pas acceder à cette page il est donc rediriger vers la page login
if(connecte()){
  header("location:profil.php");
  die();
}

//Si on envoie le formulaire on va vérifier quelques informations
if(isset($_POST['connexion'])){
  extract($_POST);
  $content = "";
  $erreur=verifMdpIdentifiant($pseudo,$mdp,$content);
  if($erreur == ""){
    //On regarde si le pseudo et le mdp saisis ne sont pas enregistrés dans notre base de donnée
    if(!verificationlogin($pseudo,$mdp)){
      //Si c'est le cas on affiche un message d'erreur
      //Et la redirection n'est pas faite
      $content="Erreur de connexion";
      $backgroud = 'style="background:tomato;padding:2%"';
    };
  } else{
    $content="Erreur de connexion";
    $backgroud = 'style="background:tomato;padding:2%"';
  }
  
}

?>
<header>
    <?php include 'config/template/nav.php'; ?>
</header>
  <section class="sectformulaire">
    <h1 class="text-center mt-5 mb-5">Connexion</h1>
    <div <?=$backgroud?>><?=$content?></div>

    <form class="formulaire formulaireconexion" action="login.php" method="post">
      <input type="text" name="pseudo" id="pseudo" class="filter" placeholder="Pseudo *">
      <input type="password" name="mdp" id="mdp" class="filter" placeholder="Mot de passe *">
      <input type="submit" value="Envoyer" name="connexion">
    </form>

    <a href="inscription.php" class="alink">Pas Inscrit ? Inscrivez vous</a>
  </section>

<?php include 'config/template/footer.php'; ?>