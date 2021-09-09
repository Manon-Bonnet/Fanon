<?php include 'config/template/head.php'; 
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
if(isset($_POST['valider'])){
  extract($_POST);
  //On vérififie que toute les informations renseignées dans le formulaire n'on pas d'erreurs
  //Si il y a des erreurs un texte informatif est enregistré indiquant l'erreur
  $erreur=changementerreur($pseudo,$tel,$mail,$ville);
  //Pour afficher les erreurs en rouge
  $backgroud = 'style="background:tomato;padding:2%"';
  //Si il n'y a pas eu d'erreur dans la saisie des informations dans le formulaire alors on va pouvoir enregistrer les données dans la base de donnée
  if($erreur == ""){
    //On met a jour la base de donnée et les variables session
    $erreur = changementbasededonnee($_SESSION['user']['id'],$pseudo,$tel,$mail,$numrue, $rue, $cp, $ville, $civil);
  }
}

?>
<header>
    <?php include 'config/template/nav.php'; ?>
</header>
<section class="sectformulaire">
    <h1 class="text-center mt-5">Bonjour <?= $_SESSION['user']['pseudo'] ?> !</h1>
    <p class="text-center mb-5">Vous êtes <?= $_SESSION['user']['statut'] ?></p>
    <div <?=$backgroud?>><?=$erreur?></div>

    <form action="profil.php" method="post" class="formulaireprofil">
      <ul class="formulaireprofil">
        <li>
          <label for="pseudo">Pseudo :</label>
          <input type="text" class="hidden" name="pseudo" id="pseudo" value="<?= $_SESSION['user']['pseudo'] ?>">
          <p class="profil-element"><?= $_SESSION['user']['pseudo'] ?></p>
        </li>
        <li>
          <p>Civilité :</p>
          <div class="btnradio" >
            <div>
              <input type="radio" class="radiocivil hidden" id="civ0" name="civil" value="0">
              <label for="mme" class="hidden">Mme</label>
            </div>
            <div>
              <input type="radio" class="radiocivil hidden" id="civ1" name="civil" value="1">
              <label for="mr" class="hidden">Mr</label>
            </div>
            <div>
              <input type="radio" class="radiocivil hidden" id="civ2" name="civil" value="2">
              <label for="autre" class="hidden">Autre</label>
            </div>
        </div>
          <input type="hidden" id="civnum" value="<?=$_SESSION['user']['civilnombre']?>"></input>
          <p class="profil-element" id="motcivil"><?= $_SESSION['user']['civil'] ?></p>
        </li>
        <li>
          <p>Adresse postale :</p>
          <label for="numrue" class="hidden">Numéro de rue :</label>
          <input type="number" class="hidden" name="numrue" id="numrue" value="<?= $_SESSION['user']['numrue'] ?>">
          <label for="rue" class="hidden">Nom de rue :</label>
          <input type="text" class="hidden" name="rue" id="rue" value="<?= $_SESSION['user']['nomrue'] ?>">
          <label for="cp" class="hidden">Code postal :</label>
          <input type="number" class="hidden" name="cp" id="cp" value="<?= $_SESSION['user']['cp'] ?>">
          <label for="ville" class="hidden">Ville :</label>
          <input type="text" class="hidden" name="ville" id="ville" value="<?= $_SESSION['user']['ville'] ?>">
          <p class="profil-element"><?= $_SESSION['user']['numrue'] . ' ' .
          $_SESSION['user']['nomrue'] . ', ' . 
          $_SESSION['user']['cp'] . ' ' .
          $_SESSION['user']['ville'] ?></p>
        </li>
        <li>
          <label for="tel">Numéro de téléhpone :</label>
          <input type="tel" class="hidden" name="tel" id="tel" value="<?= $_SESSION['user']['tel'] ?>">
          <p class="profil-element"><?= $_SESSION['user']['tel'] ?></p>
        </li>
        <li>
          <label for="mail">Adresse mail :</label>
          <input type="email" class="hidden" name="mail" id="mail" value="<?= $_SESSION['user']['email'] ?>">
          <p class="profil-element"><?= $_SESSION['user']['email'] ?></p>
        </li>
      </ul>
      <a id="modifier-profil" class="btnclassique alink profil-element modify-user-profil" value="hidden">Modifier</a>
      <input type="submit" class="hidden" value="Valider" name="valider">
      <a href="profil.php" class="hidden">Annuler</a>
      <a href="mot_de_passe.php" class="alink">Changer mon mot de passe</a>
    </form>

    
</section>

<script src="asset/script/profil.js"></script>
<?php include 'config/template/footer.php'; ?>