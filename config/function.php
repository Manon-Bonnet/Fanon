<?php
///////////////////////////______________________________________CONNEXION
//___FONCTION___fonction qui permt de voir si quelqu'in est connecté
function connecte(){
  if(isset($_SESSION['user'])){
    return true;
  }else{
    return false;
  }
}
//___FONCTION___fonction qui permet de supprimeer la session commande si elle existe
function suppsessioncommane(){
  if(isset($_SESSION['commande'])){
    unset($_SESSION['commande']);
  }
}

///////////////////////////______________________________________PANIER
//___FONCTION___fonction qui permet de voir la quantité d'articles dans le panier
function panierquantite(){
  //On regarde si le tableau panier session est cré ou non
  if(!isset($_SESSION['panier'])){
    //Si il n'est pas cré auccun produit n'a été selectionné donc le nb de prodiut est de 0
    $nombreProduits = 0;
  }
  //Sinon cela veut dire qu'il y a des produis selectionnés
  else{
    //On va parcourir le tableau et récupérre la quantité indiqué dans chauque id des produits
    $nombreProduits=0;
    foreach($_SESSION['panier'] AS $idproduit){
      $quantite= $idproduit['quantite'];
      $nombreProduits= $nombreProduits + $quantite;
    }
  }
  //On retourne le nombre de produits
  return $nombreProduits;
}
//___FONCTION___fonction qui permet de voir le montant total du panier
function montantpanier(){
  $prixtotal =0;
  foreach($_SESSION['panier'] AS $idproduit){
  $prixtotal = $prixtotal+ ($idproduit['quantite']*$idproduit['prix']);
  }
  if(isset($_SESSION['user'])){
    $commandeencours=recupereIDcommandeBDD($_SESSION['user']['id']);
    //Ajout du contenu de SESSION[panier] dans la base de donnée
    montantcommande($commandeencours, $_SESSION['user']['id']);
  }
  return $prixtotal;
}
//___FONCTION___fonction qui permet d'augmenter la quantité
function plus($id){
  $erreur="";
  if($_SESSION['panier'][$id]['stockactuel']>$_SESSION['panier'][$id]['quantite']){
    $_SESSION['panier'][$id]['quantite'] = $_SESSION['panier'][$id]['quantite'] +1;
    if(isset($_SESSION['user'])){
      $commandeencours=recupereIDcommandeBDD($_SESSION['user']['id']);
      //Ajout du contenu de SESSION[panier] dans la base de donnée
      miseajourUneLigneCommandeBDD($commandeencours, $id);
    }
    $erreur=false;
  }
  else{
    $erreur=true;
  }
  return $erreur;
}
//___FONCTION___fonction qui permet de diminuer la quantité
function moins($id){
  $_SESSION['panier'][$id]['quantite'] = $_SESSION['panier'][$id]['quantite'] -1;
  if(isset($_SESSION['user'])){
    $commandeencours=recupereIDcommandeBDD($_SESSION['user']['id']);
    //Ajout du contenu de SESSION[panier] dans la base de donnée
    miseajourUneLigneCommandeBDD($commandeencours, $id);
  }
  if($_SESSION['panier'][$id]['quantite'] ==0){
    supprimerpanier($id);
  }
}
//___FONCTION___fonction qui permet de supprimer le produit
function supprimerpanier($id){
  unset($_SESSION['panier'][$id]);
  if(isset($_SESSION['user'])){
    $commandeencours=recupereIDcommandeBDD($_SESSION['user']['id']);
    //Ajout du contenu de SESSION[panier] dans la base de donnée
    supprimerligneBDDcommande($commandeencours, $id);
  }
  if(!$_SESSION['panier']){
    header("location:panier.php");
    die();
  }
}

//___FONCTION___
function supprimerligneBDDcommande($commandeencours, $id){
  global $pdo;

  $queryInssert = "DELETE FROM hetic21_ligne_commande WHERE id_commande = :id_commande AND id_produit = :idproduit";
  $reqins = $pdo->prepare($queryInssert);
  $reqins->execute(
    [
      'id_commande' => $commandeencours[0]['id_commande'],
      'idproduit'=> $id
    ]
  );
}

//FUNCTION
function montantcommande($commandeencours, $userid){
  global $pdo;
  $total=0;
  $querySelect = "SELECT quantite, prix FROM hetic21_ligne_commande WHERE id_commande = :idcommande";
  $reqsel = $pdo->prepare($querySelect);
  $reqsel->execute(
    [
      'idcommande' => $commandeencours[0]['id_commande']
    ]
  );
  $prixquantite= $reqsel->fetchAll(PDO::FETCH_ASSOC);
  foreach($prixquantite AS $soustotal){
    $total=$total+($soustotal['quantite']*$soustotal['prix']);
  }

  //On mets à jour la quantité et le prix actuel du produit selectionné
  $queryUpdate = "UPDATE hetic21_commande SET total_commande = :total WHERE id_commande = :idcommande AND id_user = :iduser";
  $reqPrep = $pdo->prepare($queryUpdate);
  $reqPrep->execute(
    [
      'total' => $total,
      'idcommande' => $commandeencours[0]['id_commande'],
      'iduser' => $userid
    ]
  );

}


// user.id, user.nom....
// panier[0].id, panier[0].qte, panier[0].prix...



///////////////////////////______________________________________PAGE INDEX

//___FONCTION___Pour les élèments d'un produit ou la liste de tous les éléments des produits___
function infosproduits($id){
  global $pdo;
  if($id == 0){
    //On sélectionne tous les élements de la base que l'on a besoin d'afficher sur notre page d'id $id
    $req = $pdo->query('SELECT prod.id_produit, prod.nom_produit, prod.description_produit, prod.prix, prod.stock, ptprod.url_image
    FROM hetic21_produit AS prod
    LEFT JOIN hetic21_photos_produit AS ptprod ON(prod.id_produit=ptprod.id_produit AND ptprod.principal=1)');
  } else{
    $req = $pdo->prepare('SELECT prod.nom_produit, prod.description_produit, prod.prix, prod.stock, ptprod.url_image
    FROM hetic21_produit AS prod
    LEFT JOIN hetic21_photos_produit AS ptprod ON(prod.id_produit=ptprod.id_produit)
    WHERE prod.id_produit = :id');
    $req->bindValue(':id', $id);
    $req->execute();
  }
  $produit = $req->fetchAll(PDO::FETCH_ASSOC);
  return $produit;
}


///////////////////////////______________________________________PAGE PRODUITS

//___FONCTION___
//Pour verifier l'id_produit deonné dans l'url
function verifurlficheproduit($id){
  global $pdo;
  //On selectionne l'id_produit dans la tabble produit ou normalemeent celui-ci est egal à celui dans l'url
  $querySelect = "SELECT id_produit FROM hetic21_produit WHERE id_produit = :idproduit";
  $reqsel = $pdo->prepare($querySelect);
  $reqsel->execute(
    [
      'idproduit' => $id
    ]
  );
  $existeid= $reqsel->fetch(PDO::FETCH_ASSOC);
  //On retourne le tableau contennant l'id_produit
  return $existeid;
}

//Pour enregistrer les valeurs dans la session pour le panier
function setProduit($unproduit, $id){
  if(isset($_SESSION['user'])){
    $commandeencours=recupereIDcommandeBDD($_SESSION['user']['id']);
    if(count($commandeencours)==0){
      //On crée une nouvelle ligne dans hetic21_commande
      creationCommandeBDD($_SESSION['user']['id']);
      //On récupère l'id_commande de la ligne que l'on vient de crer
      $commandeencours=recupereIDcommandeBDD($_SESSION['user']['id']);
    }
  }
  //Si le produit slectionné n'a jamais été slectionné au paravant on enregistre toutes ses informationi
  if(!isset($_SESSION['panier'][$id])){
    //on vérifie s'il y a du stock
    if($unproduit[0]['stock']>0){
      $_SESSION['panier'][$id]['id']= $id;
      $_SESSION['panier'][$id]['nom']= $unproduit[0]['nom_produit'];
      $_SESSION['panier'][$id]['description']= $unproduit[0]['description_produit'];
      $_SESSION['panier'][$id]['prix']= $unproduit[0]['prix'];
      $_SESSION['panier'][$id]['stockactuel']= $unproduit[0]['stock'];
      $_SESSION['panier'][$id]['quantite']= 1;
      if(isset($unproduit[0]['url_image'])){
        $_SESSION['panier'][$id]['photo']= $unproduit[0]['url_image'];
      }
      //on enregistre un texte pour le 1er enregistrement
      $action="Votre produit a été ajouté au panier";

      if(isset($_SESSION['user'])){
        //Ajout du contenu de SESSION[panier] dans la base de donnée
        ajouterUneLigneCommandeBDD($commandeencours,$id);
      }

    }
    else{
      $action="Il n'y a plus de stock, votre produit ne peut être ajouter au panier";
    }
  }
  //Si le produit a deja été slectionné on change simplement la quantité
  else{
    if(($unproduit[0]['stock']-$_SESSION['panier'][$id]['quantite'])>0){
      $_SESSION['panier'][$id]['quantite']++;

      if(isset($_SESSION['user'])){
        //Ajout du contenu de SESSION[panier] dans la base de donnée
        miseajourUneLigneCommandeBDD($commandeencours, $id);
      }

      //on enregistre un autre texte si ce n'est pas le 1er enregistrement
      $action="Votre produit a été ajouté au panier (".$_SESSION['panier'][$id]['quantite']." ".$_SESSION['panier'][$id]['nom']." au panier)";
    }
    else{
      $action="Il n'y a plus de stock, votre produit ne peut être ajouter au panier";
    }
  }
  if(isset($_SESSION['user'])){
    $commandeencours=recupereIDcommandeBDD($_SESSION['user']['id']);
    //Ajout du contenu de SESSION[panier] dans la base de donnée
    montantcommande($commandeencours, $_SESSION['user']['id']);
  }
  //on retourne le texte d'information pour l'utilisateur
  return $action;
}

//___FONCTION___
function ajouterUneLigneCommandeBDD($commandeencours,$id){
  global $pdo;

  //On ajoute une ligne/produit dans la table commande ligne
  $queryInssert = "INSERT INTO hetic21_ligne_commande(id_commande, id_produit, quantite, prix) VALUES (:id_commande, :idproduit, :quantite, :prix)";
  $reqins = $pdo->prepare($queryInssert);
  $reqins->execute(
    [
      'id_commande' => $commandeencours[0]['id_commande'],
      'idproduit'=> $id,
      'quantite'=> $_SESSION['panier'][$id]['quantite'],
      'prix'=> $_SESSION['panier'][$id]['prix']
    ]
  );

}
//___FONCTION___
function miseajourUneLigneCommandeBDD($commandeencours,$id){
  global $pdo;

  //On mets à jour la quantité et le prix actuel du produit selectionné
  $queryUpdate = "UPDATE hetic21_ligne_commande SET quantite = :quantite, prix = :prix WHERE id_produit = :id AND id_commande = :id_commande";
  $reqPrep = $pdo->prepare($queryUpdate);
  $reqPrep->execute(
    [
      'quantite'=> $_SESSION['panier'][$id]['quantite'],
      'prix'=> $_SESSION['panier'][$id]['prix'],
      'id'=> $id,
      'id_commande' => $commandeencours[0]['id_commande']
    ]
  );
}


///////////////////////////______________________________________PAGE INSCRIPTION

//___FONCTION___
// Traitement si il ya des erreurs dans le renseignement des champs
function erreurinscription($pseudo,$mdp,$mdpconfirmation,$telephone,$mail,$ville,$codepostal,$numrue,$rue){
  $content="";
  // on vérifie que le pseudo enregistré est bbien compris entre 2 et 255 caractères
  $content = verifMdpIdentifiant($pseudo, $mdp, $content);
  // on vérifie que le mot de passe inscrit et le mot de passe de confirmation sont les mêmes
  if($mdp!=$mdpconfirmation){
    $content .= 'Votre mot de passe et sa confirmation ne correspondent pas.</br>';
  }
  // on vérifie que le téléphone enregistré est bien composé que de 10 chiffres
  if (1 !== preg_match('~^[\+]?[(]?[0-9]{3}[)]?[0-9]{3}?[0-9]{4,6}$~', $telephone)) {
    $content .= 'Le numéro de téléphone n\'est pas valide. Il doit être contenir 10 chiffres.</br>';
  }
  // on vérifie que l'e-mail enregistré est bien valide
  if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
    $content .= 'L\'email est invalide.</br>';
  }
  // on vérifie que le nom de la ville enregistré est composé que de lettres
  // ^[a-zA-Z- _-]{3,30}$
  if (1 !== preg_match('~^[a-zA-Z- _-]{3,30}$~', $ville)) {
    $content .= 'Le nom de la ville n\'est pas valide. Les chiffres et les caractères spéciaux ne sont pas acceptés.</br>';
  }
  // on vérifie que le code postal comporte bien 5 chiffres uniquement
  if (1 !== preg_match('~^[0-9]{5}$~', $codepostal)) {
    $content .= 'Le code postal doit comporter 5 chiffres.</br>';
  }
  // on vérifie que le numéro de chiffres comporte bien des chiffres uniquement
  if (1 !== preg_match('~^[0-9]{1,3}$~', $numrue)) {
    $content .= 'Le numéro de rue est invalide.</br>';
  }
  // on vérifie que le nom de la rue enregistré est composé que de lettres
  if (1 !== preg_match('~^[a-zA-Z- _-]{2,50}$~', $rue)){
    $content .= 'Le nom de rue est invalide, il ne doit comporter que des lettres minuscules et majuscules.</br>';
  }
  return $content;
}

function verifMdpIdentifiant($pseudo, $mdp, $content){
  if (1 !== preg_match('~^[a-zA-Z0-9- _-]{2,20}$~', $pseudo)){
    $content .= 'Votre pseudo doit ne peut contenir que des minuscules, majuscules, - et chiffre (les espaces ou carctètes spéciaux ne sont pas autorisés).</br>';
    $content .= 'Votre pseudo doit contenir entre 2 et 20 caractères.</br>';
  }
  // on vérifie que le mot de passe enregistré est bien compris entre 10 et 20 caractères, 
  if (1 !== preg_match('~^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[$%?!]).{10,20}$~', $mdp)) {
    $content .= 'Votre mot de passe doit contenir au minimum une majuscule, une minuscule, un chiffre et un carctère spéciale ($%?!).</br>';
    $content .= 'Votre mot de passe doit être compris entre 10 et 20 caractères.</br>';
  }
  return $content;
}

//___FONCTION___
//Function pour enregistre les informations du formulaire dans la base de donées
function enregistrementbasededonnee($pseudo,$mdp,$telephone,$mail,$numrue, $rue, $codepostal, $ville, $civil){
  global $pdo;
  $erreur="";

  //on va commence par vérifier si il existe un utilisateur possédant le meme pseudo ou le même e-mail dans notre bbase de données
  //Requete dans la tabble user pour saisir les pseudo ou e-mail qui qont similaire à ceux insrits dans le formulaire
  $querySelect = "SELECT id_user FROM hetic21_user WHERE(pseudo = :pseudo OR email = :email) LIMIT 1";
  $req = $pdo->prepare($querySelect);
  $req->execute(
    [
      'pseudo' => $pseudo,
      'email' => $mail
    ]
  );
  $dejainscrit= $req->fetchAll(PDO::FETCH_ASSOC);
  //Si il ya au moins 1 réponse cela veut dire qu'il existe un utilisateur possédant le meme pseudo ou le même e-mail
  //Dans ce cas on enregistre pas les données et on enregitre un message d'erreur
  if(count($dejainscrit)>0){
    $erreur="Un utilisateur à ce pseudo/e-mail est déja inscrit";
  }
  //Sinon il n'y a pas de doublons donc on peut enreidtre les informations dans la base de donnée
  else{
    //on hash le mot de passe avant de l'enregistrer dans la base de donnée
    $mdpCrypt = password_hash($mdp, PASSWORD_DEFAULT);

    //on dit dans quelle table et quelles informations on souhaite enregistrer
    $queryInsert = "INSERT INTO hetic21_user (pseudo, mdp, tel, email, numero_rue, nom_rue, cp, ville, civilite)
    VALUES (:pseudo, :mdp, :tel, :email, :numero_rue, :nom_rue, :cp, :ville, :civilite)";

    $reqPrep = $pdo->prepare($queryInsert);
    $reqPrep->execute(
      [
        'pseudo' => $pseudo,
        'mdp' => $mdpCrypt,
        'tel' => $telephone,
        'email' => $mail,
        'numero_rue' => $numrue,
        'nom_rue' => $rue,
        'cp' => $codepostal,
        'ville' => $ville,
        'civilite' => $civil
      ]);
    }
  // On connecte directement à la page profil
  verificationlogin($pseudo,$mdp);
  return $erreur;
}


///////////////////////////______________________________________PAGE CONNEXION

//___FONCTION___
//fonction pour vérifier si le pseudo et le mot de passe enregistré sont correctes
function verificationlogin($pseudo,$mdp){
  global $pdo;
  //Requete dans la table user pour récupérer les éléments qui correspondent au pseudo inscrit dans le formulaire login
  $querySelect = "SELECT * FROM hetic21_user WHERE pseudo = :pseudo";
  $reqPrep = $pdo->prepare($querySelect);
  $reqPrep->execute(
    [
      'pseudo' => $pseudo
    ]
  );
  $result = $reqPrep->fetchAll(PDO::FETCH_ASSOC);
  //S'il n'y a aucun résultat qui correspond au même pseudo alors on retrourne false
  if(count($result)!=1){
    return false;
  }
  //Sinon il y a donc un pseudo qui correspon, on vérifie alors que le mot de passe correspond à ce pseudo
  else{
    //si le mot de passee inscrit est different de celui enregitré dans la base de donnée alors on retrourne false
    if(!password_verify($mdp, $result[0]['mdp'])){
      return false;
    }
    //Sinon on retrourne vrai et on appel la fonction setSession
    else{
      //On appel la fonnction setSession() pour mettre à jour les varibbales de session
      setSession($result);
      return true;
    }
  }
  
}

//___FONCTION___
//fonction qui permet d'enregitrer toutes les infoamrions de l'utilisateur dans la session
//$result contient les informations d'enregistrement de l'utilisateur (=de la base de donnée hetic21_user )
function setsession($result){
    global $pdo;
    //Nous avons enregistrer la civilité sous forme de nombre dans notre base de donnée (lee 0 coorespond à femmen, le 1 à homme et 2 à autre)
    //Ici pour afficheer un contenu compréhensible à l'utilisateur on faiit afficher le texte qui correspond au numéro enregistré
    switch($result[0]['civilite']){
      case 0:
        $civil = 'Madame';
        break;
      case 1:
        $civil = 'Monsieur';
        break;
      case 2:
        $civil = 'Autre';
        break;
    }
  
    //on enregistre toutes les valeurs du formulaires dans nos variales session

    $statut = $result[0]['statut'] == 0 ? 'simple membre': 'admin';
  
    $_SESSION['user']['id'] = $result[0]['id_user'];
    $_SESSION['user']['pseudo'] = $result[0]['pseudo'];
    $_SESSION['user']['tel'] = $result[0]['tel'];
    $_SESSION['user']['email'] = $result[0]['email'];
    $_SESSION['user']['numrue'] = $result[0]['numero_rue'];
    $_SESSION['user']['nomrue'] = $result[0]['nom_rue'];
    $_SESSION['user']['cp'] = $result[0]['cp'];
    $_SESSION['user']['ville'] = $result[0]['ville'];
    $_SESSION['user']['civilnombre'] = $result[0]['civilite'];
    $_SESSION['user']['civil'] = $civil;
    $_SESSION['user']['statut'] = $statut;

    $commandeencours=recupereIDcommandeBDD($_SESSION['user']['id']);

    //Si une SESSION[panier] existe et que l'utilisateur est connecter à sa SESSION[user] alors on va enregsitre les infos du panier dan la base de donnée pour les garder en mémoire la prochaine fois
    if(isset($_SESSION['panier'])){
      if(count($commandeencours)==0){
        //On crée une nouvelle ligne dans hetic21_commande
        creationCommandeBDD($_SESSION['user']['id']);
        //On récupère l'id_commande de la ligne que l'on vient de crer
        $commandeencours=recupereIDcommandeBDD($_SESSION['user']['id']);
      }
      //Ajout du contenu de SESSION[panier] dans la base de donnée
      ajouterLigneCommandeBDD($commandeencours);

      //On vide SESSION[panier]
      //On ajjoute les infos de la bdd dans SESSION[panier]
      initialisationSESSIONpannieravecBDD($commandeencours);
    }
    //Si aucune SESSION[panier] existe mais qu'il ya une ancienne commmande non payée enregsitrée dans la base de donnée alors on la récupère
    else{
      //Si un panier correspond dans la base de donnée
      if(count($commandeencours)==1){
        initialisationSESSIONpannieravecBDD($commandeencours);
      }
    }

    //On redirige vers profil une fois tout cela terminé
    header('location:profil.php');
    exit();
}

//___FONCTION___
//fonction pour récupérer l'id_commande de la commande non ternimé de l'utilisateur dans la table hetic21_commande
function recupereIDcommandeBDD($userid){
  global $pdo;
  //on recupère id_commande de la table hetic21_commande lorsque l'on trouve un ligne pour lequelle id-user correespond à la personne connectée + que la commande n'a pas été finalisée
    $querySelect = "SELECT id_commande FROM hetic21_commande WHERE id_user = :iduser AND type = :typecommande";
    $req = $pdo->prepare($querySelect);
    $req->execute(
      [
        'iduser'=> $userid,
        'typecommande'=> 0
      ]
    );
    $commandeencours= $req->fetchAll(PDO::FETCH_ASSOC);
    return $commandeencours;
}
//___FONCTION___
//fonction pour créer une nouvelle commande associé au client actuellement connecté
function creationCommandeBDD($userid){
  global $pdo;
  //On crée une nouvelle ligne dans hetic21_commande
  $queryInssert = "INSERT INTO hetic21_commande(type, id_user, total_commande) VALUES (:typecommande, :iduser, :total)";
  $req = $pdo->prepare($queryInssert);
  $req->execute(
    [
      'typecommande'=> 0,
      'iduser' => $userid,
      'total'=>  0
    ]
  );
}

//___FONCTION___
//fonction pour récupérer dans un tableau tous les id_produits déja présent dans la table hetic21_ligne_commande avec en id_commande l'id actuel
function recupererIdProduitLigneCommandeBDD($commandeencours){
  global $pdo;
  $querySelect = "SELECT id_produit FROM hetic21_ligne_commande WHERE id_commande = :idcommande";
    $req = $pdo->prepare($querySelect);
    $req->execute(
      [
        'idcommande'=> $commandeencours[0]['id_commande']
      ]
    );
    $idproduitbdd= $req->fetchAll(PDO::FETCH_ASSOC);
    //on retourne le tableau
    return $idproduitbdd;
}

function TraceDebug($Info){
global $ModeTrace;
if ($ModeTrace==1){
  print_r($Info);
}

  
};

//___FONCTION___
//function pour ajouter/mettre à jour les produits dans la base de donée
function ajouterLigneCommandeBDD($commandeencours){
  global $pdo;
  //On récupère un tableau de tous les id_produits déja présent dans la table hetic21_ligne_commande
  $idproduitbdd=recupererIdProduitLigneCommandeBDD($commandeencours);
  
  //on parcours tout nos produits présents dans SESSION[panier] pour pouvoir les ajouter/mettre à jour dans la BDD
  foreach($_SESSION['panier'] AS $key){
    //On initialise notre variable de doublon à false
    $doublon=false;
    //On parcours maintenant tous les id_produits présents dans notre tableau idproduitbdd
    for($i=0; $i<count($idproduitbdd); $i++){
      //si l'id de SESSION[panier] est présent dans notre tableau idproduitbdd
      if($key['id']==$idproduitbdd[$i]['id_produit']){
        //Si c'est le cas on passe notre variable de doublon à true
        $doublon=true;
        //On sort de la boucle
        break;
      }
    }
    //Si l'id_produit actuel est déja enregistré dans la base de donnée on va juste mettre a jour les infos
    if($doublon==true){
      //On récupère la quantité qui est déjà enregistré dans la base de donnée pour ce produit
      $querySelect="SELECT quantite FROM hetic21_ligne_commande WHERE id_produit = :id";
      $req = $pdo->prepare($querySelect);
      $req->execute(
        [
          'id'=> $key['id']
        ]
      );
      $anciennequantite= $req->fetch(PDO::FETCH_ASSOC);

      //On mets à jour la quantité et le prix actuel du produit selectionné
      $queryUpdate = "UPDATE hetic21_ligne_commande SET quantite = :quantite, prix = :prix WHERE id_produit = :id AND id_commande = :id_commande";
      $reqPrep = $pdo->prepare($queryUpdate);
      $reqPrep->execute(
        [
          'quantite'=> $anciennequantite['quantite'] + $key['quantite'],
          'prix'=> $key['prix'],
          'id'=> $key['id'],
          'id_commande' => $commandeencours[0]['id_commande']
        ]
      );
    }
    //Sinon cela veut dire que le produit n'est pas du tout enregistré dans la table hetic21_ligne_commande donc on enregistre toutes ses infos présentent dans session[panier]
    else{
      $queryInssert = "INSERT INTO hetic21_ligne_commande(id_commande, id_produit, quantite, prix) VALUES (:id_commande, :idproduit, :quantite, :prix)";
      $reqins = $pdo->prepare($queryInssert);
      $reqins->execute(
        [
          'id_commande' => $commandeencours[0]['id_commande'],
          'idproduit'=> $key['id'],
          'quantite'=> $key['quantite'],
          'prix'=> $key['prix']
        ]
      );
    }
  }
}

//___FONCTION___
function initialisationSESSIONpannieravecBDD($commandeencours){
  global $pdo;
  //Vider le SESSION[panier] actuel
  unset($_SESSION['panier']);
  $querySelect="SELECT LCD.id_produit,PD.nom_produit, PD.description_produit, PD.prix, PD.stock, LCD.quantite, PDP.url_image 
  FROM hetic21_ligne_commande AS LCD 
      INNER JOIN hetic21_produit AS PD ON(LCD.id_produit=PD.id_produit) 
      INNER JOIN hetic21_photos_produit AS PDP ON(PD.id_produit=PDP.id_produit AND PDP.principal=1) 
  WHERE LCD.id_commande = :idcommande";
  $req = $pdo->prepare($querySelect);
  $req->execute(
    [
      ':idcommande' => $commandeencours[0]['id_commande']
    ]
  );
  $produitspanier= $req->fetchAll(PDO::FETCH_ASSOC);

  foreach($produitspanier AS $unproduit){
    $_SESSION['panier'][$unproduit['id_produit']]['id']= $unproduit['id_produit'];
    $_SESSION['panier'][$unproduit['id_produit']]['nom']= $unproduit['nom_produit'];
    $_SESSION['panier'][$unproduit['id_produit']]['description']= $unproduit['description_produit'];
    $_SESSION['panier'][$unproduit['id_produit']]['prix']= $unproduit['prix'];
    $_SESSION['panier'][$unproduit['id_produit']]['stockactuel']= $unproduit['stock'];
    $_SESSION['panier'][$unproduit['id_produit']]['quantite']= $unproduit['quantite'];
    if(!empty($unproduit['url_image'])){
      $_SESSION['panier'][$unproduit['id_produit']]['photo']= $unproduit['url_image'];
    }
  }

}

///////////////////////////______________________________________PAGE PROFIL

//___FONCTION___
// Traitement si il ya des erreurs de saisie dans le formualaire
function changementerreur($pseudo,$telephone,$mail,$ville){
  $content="";
  if (1 !== preg_match('~^[a-zA-Z0-9- _-]{2,20}$~', $pseudo)){
    $content .= 'Votre pseudo doit ne peut contenir que des minuscules, majuscules, - et chiffre (les espaces ou carctètes spéciaux ne sont pas autorisés).</br>';
    $content .= 'Votre pseudo doit contenir entre 2 et 20 caractères.</br>';
  }
  // on vérifie que le téléphone enregistré est bien composé que de 10 chiffres
  if (1 !== preg_match('~^[\+]?[(]?[0-9]{3}[)]?[0-9]{3}?[0-9]{4,6}$~', $telephone)) {
    $content .= 'Le numéro de téléphone n\'est pas valide. Il doit être contenir 10 chiffres.</br>';
  }
  // on vérifie que l'e-mail enregistré est bien valide
  if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
    $content .= 'L\'email est invalide.</br>';
  }
  // on vérifie que le nom de la ville enregistré est composé que de lettres
  // ^[a-zA-Z- _-]{3,30}$
  if (1 !== preg_match('~^[a-zA-Z- _-]{3,30}$~', $ville)) {
    $content .= 'Le nom de la ville n\'est pas valide. Les chiffres et les caractères spéciaux ne sont pas acceptés.</br>';
  }
  return $content;
}  

//___FONCTION___
//Function pour enregistre les informations du formulaire dans la base de donées
function changementbasededonnee($id,$pseudo,$telephone,$mail,$numrue, $rue, $codepostal, $ville, $civil){
  global $pdo;
  $erreur="";

  //on va commence par vérifier si il existe un utilisateur possédant le meme pseudo ou le même e-mail dans notre bbase de données
  //Requete dans la tabble user pour saisir les pseudo ou e-mail qui qont similaire à ceux insrits dans le formulaire mais avec un id different de l'utilisateur actuel
  $querySelect = "SELECT id_user FROM hetic21_user WHERE(pseudo = :pseudo OR email = :email) AND (id_user != :id) LIMIT 1";
  $req = $pdo->prepare($querySelect);
  $req->execute(
    [
      'pseudo' => $pseudo,
      'id' => $id,
      'email' => $mail
    ]
  );
  $dejainscrit= $req->fetchAll(PDO::FETCH_ASSOC);
  //Si il ya au moins 1 réponse cela veut dire qu'il existe un utilisateur possédant le meme pseudo ou le même e-mail
  //Dans ce cas on enregistre pas les données et on enregitre un message d'erreur
  if(count($dejainscrit)>0){
    $erreur="Un utilisateur à ce pseudo/e-mail est déja inscrit".$dejainscrit[0]['id_user'];
  }
  else{
    //on fait une requette pour dire dans quelle table et quelles informations on souhaite mettre à jour
    $queryUpdate = "UPDATE hetic21_user 
    SET pseudo = :pseudo, tel = :tel, email = :email, numero_rue = :numero_rue, nom_rue = :nom_rue, cp = :cp, ville = :ville, civilite = :civilite
    WHERE id_user = :id";
    $reqPrep = $pdo->prepare($queryUpdate);
    $reqPrep->execute(
      [
        'id'=> $id,
        'pseudo' => $pseudo,
        'tel' => $telephone,
        'email' => $mail,
        'numero_rue' => $numrue,
        'nom_rue' => $rue,
        'cp' => $codepostal,
        'ville' => $ville,
        'civilite' => $civil
      ]
    );

    //on fait une requette pour selectionner les informations que l'on souhaite récupérer
    //C'est à dires les dernières informations que l'on vient d'enregistre juste au-dessus
    $querySelect = "SELECT * FROM hetic21_user WHERE id_user = :id";
    $reqPrep = $pdo->prepare($querySelect);
    $reqPrep->execute(
      [
        'id' => $id
        ]
    );
    $result = $reqPrep->fetchAll(PDO::FETCH_ASSOC);
    //On appel la fonnction setSession() pour mettre à jour les varibbales de session
    setSession($result);
  }
  return $erreur;
}


//___FONCTION___
//Function pour changer le stock par l'admin

function changeProduit($produitAChanger, $newStock, $newPrix, $newNom){
  global $pdo;
  $querySelect = "UPDATE hetic21_produit SET stock = :stock, nom_produit = :nom, prix = :prix
  WHERE id_produit = :id";
  $reqPrep = $pdo->prepare($querySelect);
  $reqPrep->execute(
    [
      'stock' => $newStock,
      'nom' => $newNom,
      'prix' => $newPrix,
      'id' => $produitAChanger
    ]
  );
}

//___FONCTION___
// ajouter un nouveau produit
function addNewProduct($nom, $description, $prix, $stock){
  global $pdo;
  $erreur="";

  $queryInsert = "INSERT INTO hetic21_produit (nom_produit, description_produit, prix, stock)
  VALUES (:nom, :descrip, :prix, :stock)";

  $req = $pdo->prepare($queryInsert);
  $req->execute(
    [
      'nom' => $nom,
      'descrip' => $description,
      'prix' => $prix,
      'stock' => $stock
    ]
  );
  return $erreur;
}

//___FONCTION___
// ajouter un dossier pour les images du nouveau produit
function createDirForImages($newProductId, $files){
  mkdir('asset/img/produits/produit' . $newProductId);
  
  // télécharger l'image
  $uploaddir = 'asset/img/produits/produit' . $newProductId . '/';

  if(isset($files)){
  
    foreach($files as $file){
      $uploadfile = $uploaddir . basename($file['name']);
      move_uploaded_file($file['tmp_name'], $uploadfile);
      if($files['image-url-0'] === $file){
        addPicture($uploadfile, $newProductId, true);
      }
      addPicture($uploadfile, $newProductId, false);
    }
  }
}

//___FONCTION___
// ajouter les images du nouveau produit
function addPicture($file, $newProductId, $main){
  global $pdo;
  $erreur="";

  if($file != ''){
    if($main == true){
      $queryInsertPics = "INSERT INTO hetic21_photos_produit (url_image, id_produit, principal)
      VALUES (:urlimage, :id, 1)";
    } else{
      $queryInsertPics = "INSERT INTO hetic21_photos_produit (url_image, id_produit)
      VALUES (:urlimage, :id)";
    }
  
    $req = $pdo->prepare($queryInsertPics);
    $req->execute(
      [
        'urlimage' => $file,
        'id' => $newProductId
      ]
    );
  }  
  return $erreur;
}

//___FONCTION___
// supprimer un produit
function deleteProduct($id){
  global $pdo;
  $queryDelete = "DELETE FROM hetic21_photos_produit WHERE id_produit = :id";
  $req = $pdo->prepare($queryDelete);
  $req->execute(
    [
      'id' => $id
    ]
  );
  $queryDeleteProduit = "DELETE FROM hetic21_produit WHERE id_produit = :id";
  $req2 = $pdo->prepare($queryDeleteProduit);
  $req2->execute(
    [
      'id' => $id
    ]
  );


  // Supprimer toutes les images
  $dir = 'asset/img/produits/produit' . $id;
  $openDir = opendir($dir);
  $files = readdir($openDir);

  while ($files = readdir($openDir)){
    if($files != '.' && $files != '..'){
      chmod($dir . '/' . $files, 0777);
      unlink("$dir/$files");  
    }
  }
  closedir($openDir);
  
  // Supprimer le dossier
  rmdir($dir);
}

//___FONCTION___
// Traitement si il ya des erreurs dans le renseignement des champs
function erreurProduit($nom, $desc, $prix, $stock){
  $content="";
  if (1 !== preg_match('~^[a-zA-ZÀ-ÿ0-9- _-]{2,50}$~', $nom)){
    $content .= 'Le nom du produit n\'est pas valide. Il ne doit comprendre que des lettres, chiffres, espace, trait d\'union ou underscore.</br>';
  }
  if (1 !== preg_match('~^[a-zA-ZÀ-ÿ0-9- .,()%-]{0,5000}$~', $desc)){
    $content .= 'La description du produit ne peut comprendre que les caractères spéciaux suivants : . , (  ) % -.</br>';
  }
  if (1 !== preg_match('~^[0-9,.]{1,11}$~', $prix)) {
    $content .= 'Le prix doit être un nombre.</br>';
  }
  if (1 !== preg_match('~^[0-9]{1,10}$~', $stock)) {
    $content .= 'Le stock doit être un nombre entier.</br>';
  }
  return $content;
}

///////////////////////////______________________________________PAGE MOT DE PASSE

//___FONCTION___
//Fonction qui permet de changer son mot de passe actuel
function changemdp($mdp_ancien, $mdp_nouveau, $id){
  global $pdo;
  $erreur="";

  //on fait une requête qui va selectionner le mot de passe (hash) de l'utilisateur actuel enregistre dans notre bas de donnée
  $querySelect = "SELECT mdp FROM hetic21_user WHERE id_user = :id";
  $reqancien = $pdo->prepare($querySelect);
  $reqancien->execute(
    [
      'id' => $id
    ]
  );
  //On enregistre ce mot de passe dans une variable
  $mdpCrypt= $reqancien->fetchAll(PDO::FETCH_ASSOC);
  foreach ($mdpCrypt AS $ligne){
    $mdpCryptBbd = $ligne['mdp'];
  }


  //On regarde si le mot de passe hash de la base de donnée et le mot de passe inscrit dans le formulaire par l'utilisateur ne correspondent pas
  if(!password_verify($mdp_ancien, $mdpCryptBbd)){
    //Si ils ne correspondent pas on enregitre une erreur et on ne va pas plus loin
    $erreur="Mot de passe actuel incorrect";
  }
  //Sinon cela veut dire que le mot de passe actuel saisie est corecte
  else{
    // on vérifie alors que le nouveua mot de passe enregistré est bien compris entre 8 et 25 caractères
    if (1 !== preg_match('~^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[$%?!]).{10,20}$~', $mdp_nouveau)) {
      $erreur .= 'Votre mot de passe doit contenir au minimum une majuscule, une minuscule, un chiffre et un carctère spéciale ($%?!).</br>';
      $erreur .= 'Votre mot de passe doit être compris entre 10 et 20 caractères.</br>';
    }
    //Sinon cela veut dire que la saisie du nouveau mot de passe est correcte
    else{
      //On hash le mot de passe
      $mdpCrypt_nouveau = password_hash($mdp_nouveau, PASSWORD_DEFAULT);
      //On met à jour la tabe de notre base de donnée de l'utilisateur actuellement connecté avec son nouveau mot de passe
      $queryUpdate = "UPDATE hetic21_user SET mdp = :mdp WHERE id_user = :id";
      $reqnouveau = $pdo->prepare($queryUpdate);
      $reqnouveau->execute(
        [
          'mdp' => $mdpCrypt_nouveau,
          'id' => $id
        ]
      );
    }
  }
  return $erreur;
}



///////////////////////////______________________________________PAGE PAYEMENT
//___FONCTION___
function payement($idcommande){
  global $pdo;
  //Si l'utilisateur n'est pas connecté on ne va pas plus loin
  if(isset($_SESSION["user"]['id'])){
    //On récupère tous ce dont on a besoin pour informer le recap de la commande passée
    $querySelect = "SELECT LCD.id_produit,PD.nom_produit, PD.description_produit, PD.prix, PD.stock, LCD.quantite
      FROM hetic21_ligne_commande AS LCD 
          INNER JOIN hetic21_produit AS PD ON(LCD.id_produit=PD.id_produit)
          INNER JOIN hetic21_commande AS C ON(C.id_commande=LCD.id_commande)
      WHERE C.id_commande = :idcommande AND C.id_user = :iduser";
    $req = $pdo->prepare($querySelect);
    $req->execute(
      [
        'idcommande'=> $idcommande,
        'iduser' => $_SESSION["user"]['id']
      ]
    );
    $commaneeffectueee= $req->fetchAll(PDO::FETCH_ASSOC);

    //On mets à jour le type dans la table commande pour informer que cettte commande à été réglée
    $queryUpdate = "UPDATE hetic21_commande SET type = :typecommande WHERE id_commande = :idcommande AND id_user = :iduser";
    $reqPrep = $pdo->prepare($queryUpdate);
    $reqPrep->execute(
      [
        'typecommande' => 1,
        'idcommande' => $idcommande,
        'iduser' => $_SESSION["user"]['id']
      ]
    );

    //Update des stock de chaque produit à diminuer
    foreach($commaneeffectueee as $produit){
      //On mets à jour les stocks dans la table produit
      $queryUpdate = "UPDATE hetic21_produit SET stock = :stock WHERE id_produit = :idproduit";
      $reqPrep = $pdo->prepare($queryUpdate);
      $reqPrep->execute(
        [
          'stock' =>  $produit['stock'] - $produit['quantite'],
          'idproduit' => $produit['id_produit']
        ]
      );
    }

    unset($_SESSION['panier']);
    return $commaneeffectueee;
  }
  else{
    header("location:login.php");
    die();
  }
}
//___FONCTION___
//pour que l'utilisateur n'est acces que a ses commandes si il traffic le code
function verifidcommande($idcommandepost){
  global $pdo;
  $querySelect = "SELECT id_user FROM hetic21_commande WHERE id_user = :iduser AND id_commande = :idcommande";
  $req = $pdo->prepare($querySelect);
  $req->execute(
    [
      'iduser' => $_SESSION["user"]['id'],
      'idcommande'=> $idcommandepost 
    ]
  );
  $idcommandeok= $req->fetch(PDO::FETCH_ASSOC);
  return $idcommandeok;
}


//___FONCTION___
function montantfinal($idcommande){
  global $pdo;
  if(isset($_SESSION["user"]['id'])){
    //On mets récupère le montnant total enregistré dans la bdd
    $querySelect = "SELECT total_commande FROM hetic21_commande WHERE id_commande = :idcommande AND id_user = :iduser";
    $reqSec = $pdo->prepare($querySelect);
    $reqSec->execute(
      [
        'idcommande' => $idcommande,
        'iduser' => $_SESSION["user"]['id']
      ]
    );
    $totalfinal = $reqSec->fetch(PDO::FETCH_ASSOC);
    return $totalfinal;
  }
}

//___FONCTION___
function personnecommande($idcommande){
  global $pdo;
  if(isset($_SESSION["user"]['id'])){
    //On mets récupère le montnant total enregistré dans la bdd
    $querySelect = "SELECT pseudo, numero_rue, nom_rue, cp, ville, email, tel FROM hetic21_user WHERE id_user = :iduser";
    $reqSec = $pdo->prepare($querySelect);
    $reqSec->execute(
      [
        'iduser' => $_SESSION["user"]['id']
      ]
    );
    $usercommande = $reqSec->fetchAll(PDO::FETCH_ASSOC);

    //Update les infos du client
    foreach($usercommande as $userinfos){
      $queryUpdate = "UPDATE hetic21_commande SET nom = :pseudo, adresse = :adresse WHERE id_commande = :idcommande AND id_user = :iduser"  ;
      $reqPrep = $pdo->prepare($queryUpdate);
      $reqPrep->execute(
        [
          'pseudo' =>  $userinfos['pseudo'],
          'adresse' => $userinfos['numero_rue'].' '.$userinfos['nom_rue'].' '.$userinfos['cp'].' '.$userinfos['ville'],
          'idcommande' => $idcommande,
          'iduser' => $_SESSION["user"]['id']
        ]
      );
    }
    return $usercommande;
  }
}

?>