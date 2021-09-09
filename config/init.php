<?php
session_start();
global $pdo;

global $ModeTrace;

$ModeTrace=1;
require 'function.php';


/*_____ Données de connexion à la base de donnée_____*/
//en local
// define('HOSTNAME', 'localhost');
// define('USERNAME', 'root');
// define('PASSWORD', 'root');
// define('DATABASE', 'maillem599');

//en ligne
define('HOSTNAME', 'maillem599.mysql.db');
define('USERNAME', 'maillem599');
define('PASSWORD', 'VPJDk2JBvE6t');
define('DATABASE', 'maillem599');

/*_____Connexion PDO à la base de donnée_____*/
$dsn = "mysql:host=" . HOSTNAME . ';dbname=' . DATABASE;
try{
  $pdo = new PDO($dsn, USERNAME, PASSWORD);
} catch(PDOException $e){
  die('<ul><li>Erreur sur le fichier : ' . 
  $e -> getfile() . '</li><li>Erreur à la ligne : ' .
  $e -> getLine() . 'Message d\'erreur : ' .
  $e -> getMessage() . '</li></ul>');
}


///______________________________________DECONNEXION
//Pour déconnecter l'utilisateur
if(isset($_GET['session']) && $_GET['session'] == 'destroy'){
  session_destroy();
  header('location:index.php');
  exit();
}

?>