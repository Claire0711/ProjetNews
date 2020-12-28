<?php

include '../bdd/connexion.php';

session_start();

$ancienmdp=$motdepasse=$confirmdp=""; 
$correctmdp = true;
$formValid = true; 
$erreur=array(); 

if (!empty($_SESSION['idredacteur'])) {
	$idredacteur = $_SESSION['idredacteur'];

	$affred = $objPdo->prepare('
  	SELECT *
  	FROM redacteur 
  	WHERE idredacteur = ?
  	');

	$affred->bindValue(1, $idredacteur, PDO::PARAM_INT);
	$affred->execute();
} else {
	echo "Erreur connexion";
}

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../style.css">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>
        Informations personnelles
	</title>
	

	<?php
    if (isset($_POST['valider'])) {
        if (!empty($_POST['ancienmdp'] && !empty($_POST['motdepasse']) && !empty($_POST['confirmdp']))){

            $motdepasse = crypt(htmlentities($_POST['motdepasse']), '$2a$07$usesomesillystringforsalt');
			$ancienmdp=$_POST['ancienmdp']; 

            if ($_POST['motdepasse'] != $_POST['confirmdp']) {
                $correctmdp = false;
                $erreur[] = "Les deux mots de passes ne correspondent pas.";
            }

            if ($formValid and $correctmdp) {

				$mdpexist = $objPdo->prepare("SELECT * FROM redacteur WHERE idredacteur =?");
				$mdpexist->bindValue(1, $_SESSION['idredacteur'], PDO::PARAM_STR);
				$mdpexist->execute();
				$exist = $mdpexist->fetch(); 

				if (password_verify($ancienmdp, $exist['motdepasse'])){

                $update_red = $objPdo->prepare('UPDATE redacteur SET motdepasse =:motdepasse WHERE idredacteur=:id');
                $update_red->bindValue(':motdepasse', $motdepasse, PDO::PARAM_STR);
				$update_red->bindValue(':id', $_SESSION['idredacteur'], PDO::PARAM_STR);
				$update_red->execute();
				}
				else $erreur[]=("Mot de passe incorrect"); 
            }
        }
        else $erreur[] = ("Merci de remplir tous les champs"); 
    }

?>

</head>

<body>

	<nav>
        <ul>
            <li><a href="../index.php">
                    Accueil
                </a></li>
            <li><a href="../news/newsUtilisateur.php"> 
                    Mes news
                    </a></li>
            <li><a href="../news/ajout_article.php">
                    Nouveau sujet
                    </a></li>
            <li><a href="../user/deconnexion.php" style="margin-left:470px" onclick="return confirm('Etes vous sûr de vouloir vous déconnecter ?');">
                    Déconnexion
                    </a></li>
        </ul>
    </nav>

    <div class="info">
    	<h2>Informations personnelles</h2>

    	<table class="infor">

		<?php 
      	$donnees = $affred->fetch(); 
    	?>

    	<tr>
    		<td>Pseudo</td>
    		<td> <?php echo $donnees['pseudo']; ?> </td>
    	</tr>

    	<tr>
    		<td>Nom</td>
    		<td> <?php echo $donnees['nom']; ?> </td>
    	</tr>

    	<tr>
			<td>Prénom</td>
			<td> <?php echo $donnees['prenom']; ?> </td>
    	</tr>
    		
    	<tr>
			<td>Adresse e-mail</td> 
			<td> <?php echo $donnees['adressemail']; ?> </td>
    	</tr>

    	</table>
	</div>
	
	<form name="formulaire" method="post" action="redacteur.php" onsubmit="return Verification()">
		<label>Ancien mot de passe</label></br>
        <input type="password" value="" name="ancienmdp" id="ancienmdp"> </br>
		<label>Nouveau mot de passe</label></br>
        <input type="password" value="" name="motdepasse" id="motdepasse"> </br>
        <label>Confirmer le nouveau mot de passe</label> </br>
		<input type="password" value="" name="confirmdp" id="confirmdp"> </br> </br>
		<?php foreach ($erreur as $value)
			echo $value ?>
        <input type="submit" value="Validez" name="valider">
	</form>

</body>
</html>