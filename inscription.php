<?php 
include 'config/template/head.php';
suppsessioncommane();
//Définition des variables contenent les erreurs comme étant vide au départ
$erreur="";
$backgroud="";

//si l'utilisateur est pas conneté il ne peut pas acceder à cette page il est donc rediriger vers la page login
if(connecte()){
  header("location:profil.php");
  die();
}

//Si on envoie le formulaire on va vérifier quelques informations
if(isset($_POST['inscription'])){
  extract($_POST);
  //On vérififie que toute les informations renseignées dans le formulaire n'on pas d'erreurs
  //Si il y a des erreurs un texte informatif est enregistré indiquant l'erreur
  $erreur=erreurinscription($pseudo,$mdp,$mdpconfirmation,$telephone,$mail,$ville,$codepostal,$numrue,$rue);
  //Pour afficher les erreurs en rouge
  $backgroud = 'style="background:tomato;padding:2%"';
  //Si il n'y a pas eu d'erreur dans la saisie des informations dans le formulaire alors on va pouvoir enregistrer les données dans la base de donnée
  if($erreur == ""){
    //On vérifie que aucun autre avec le même pseudo ou le mail e-mail s'est déja inscrit
    $erreur=enregistrementbasededonnee($pseudo,$mdp,$telephone,$mail,$numrue, $rue, $codepostal, $ville, $civil);
    //Si l'enregistrement s'est bien passé on redirige vers la page profil
    if($erreur == ""){
      header("location:profil.php");
    }
  }
}
?>




<header>
    <?php include 'config/template/nav.php'; ?>
</header>
<section class="sectformulaire">
    <h1 class="text-center mt-5 mb-5">Inscription</h1>
    <div <?=$backgroud?>><?=$erreur?></div>

    <form class="formulaire formulaireinscription" action="inscription.php" method="post">
      <div class="deuxparties">
        <div class="partiesformulaire premierepartie">
          <label for="pseudo"></label>
          <input type="text" name="pseudo" id="pseudo" class="filter" placeholder="Pseudo *" value="<?php if(isset($pseudo)){ echo $pseudo; } ?>">
          <label for="telephone"></label>
          <input type="tel" name="telephone" id="telephone" class="filter" placeholder="Téléphone *" value="<?php if(isset($telephone)){ echo $telephone; } ?>">
          <label for="numrue"></label>
          <input type="number" name="numrue" id="numrue" class="filter" placeholder="Numéro de rue *" value="<?php if(isset($numrue)){ echo $numrue; } ?>">
          <label for="codepostal"></label>
          <input type="number" name="codepostal" id="codepostal" class="filter" placeholder="Code Postal *" value="<?php if(isset($codepostal)){ echo $codepostal; } ?>">
          <label for="ville"></label>
          <input type="text" name="ville" id="ville" class="filter" placeholder="Ville *" value="<?php if(isset($ville)){ echo $ville; } ?>">
        </div>
        <div class="partiesformulaire deuxiemepartie">
          <label for="mdp"></label>
          <input type="password" name="mdp" id="mdp" class="filter" placeholder="Mot de passe (entre 10 et 20 caractères) *">
          <label for="mdpconfirmation"></label>
          <input type="password" name="mdpconfirmation" id="mdpconfirmation" class="filter" placeholder="Confirmation du mot de passe *">
          <label for="mail"></label>
          <input type="email" name="mail" id="mail" class="filter" placeholder="Email *" value="<?php if(isset($mail)){ echo $mail; } ?>">
          <label for="rue"></label>
          <input type="text" name="rue" id="rue" class="filter" placeholder="Nom de rue *" value="<?php if(isset($rue)){ echo $rue; } ?>">
          <div class="divcivilite">
          <p class="civilite">Civilité :</p>
          <div class="btnradio">
            <div>
              <input type="radio" id="mme" name="civil" value="0" checked>
              <label for="mme">Mme</label>
            </div>
            <div>
              <input type="radio" id="mr" name="civil" value="1">
              <label for="mr">Mr</label>
            </div>
            <div>
              <input type="radio" id="autre" name="civil" value="2">
              <label for="autre">Autre</label>
            </div>
          </div>
        </div>
        </div>
      </div>

      <input type="submit" value="Envoyer" name="inscription">
    </form>

    <a href="login.php" class="alink">Déjà Inscrit ?</a>
  </section>

<?php include 'config/template/footer.php'; ?>