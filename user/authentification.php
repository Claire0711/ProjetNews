<!DOCTYPE HTML>
<?php

include("../bdd/connexion.php");

session_start();

$identifiant = $motdepasse = "";
$erreur = array();
?>

<html>

<head>
	<link rel="stylesheet" href="../style.css">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Authentification</title>

	<script language="javascript" type="text/javascript">
		function Verification() {
			var identifiant = document.forms['formulaire'].identifiant;
			var mdp = document.forms['formulaire'].motdepasse;
			var formValid = document.forms['formulaire'].$formValid;
			var erreur = [];
			var regex = /^\w+[\+\.\w-]*@([\w-]+\.)*\w+[\w-]*\.([a-z]{2,4}|\d+)$/i;

			if (!identifiant.value.replace(/\s+/, '').length) {
				erreur.push("Merci de saisir votre adresse mail ou votre pseudo");
				formValid = false;
			} 

			if (!mdp.value.replace(/\s+/, '').length) {
				erreur.push("Merci de saisir votre mot de passe");
				formValid = false;
			}

			if (erreur.length>0) alert("Vous devez saisir votre adresse mail ou pseudo, ET votre mot de passe")
			return formValid;
		}
	</script>

<?php
	if(isset($_POST['connexion'])){
		if (isset($_POST['identifiant']) and isset($_POST['motdepasse'])) {
			$formValid = true;

			$identifiant = $_POST['identifiant'];
			$motdepasse = $_POST['motdepasse'];

			if (empty($identifiant) && empty($motdepasse)) {
				$formValid = false;
				$erreur[] = "Vous devez saisir une adresse email ou votre pseudo et votre mot de passe.";
			} else if (empty($identifiant)){
				$formValid = false;
			$erreur[] = "Vous devez saisir une adresse email ou votre pseudo.";
			} else if (empty($motdepasse)) {
			$formValid = false;
			$erreur[] = "Vous devez saisir un mot de passe.";
			}
		}

		if ($formValid) {
			$requser = $objPdo->prepare("SELECT idredacteur, adressemail, motdepasse, pseudo FROM redacteur WHERE adressemail = ? OR pseudo=?");
			$requser->bindValue(1, $identifiant, PDO::PARAM_STR);
			$requser->bindValue(2, $identifiant, PDO::PARAM_STR);
			$requser->execute();
			$userexist = $requser->rowCount();
			$userinfo = $requser->fetch();
		}


		if ($userexist>0) {
			if (password_verify($motdepasse, $userinfo['motdepasse'])) {
				session_start();
				$_SESSION['idredacteur'] = $userinfo['idredacteur'];
				$_SESSION['adressemail'] = $userinfo['adressemail'];
				$_SESSION['pseudo'] = $userinfo['pseudo'];

				header("location:../index.php?");
			} else {
				$erreur[] = "Email ou Mot de passe incorrect.";
			}
		}
	}
?>


</head>

<nav>
	<ul>
		<li><a href="../index.php">
				Retour à l'accueil
			</a>
		<li>
	</ul>
</nav>

<h1 style="text-align:center; margin-right:90px;">Authentification</h1>

<body>
	<form method="POST" action="authentification.php" onsubmit="return Verification()" name="formulaire">
		Adresse mail ou pseudo : <input type='text' name='identifiant'><br /></input>
		Mot de passe : <input type='password' name='motdepasse'><br /></input>
		<?php foreach ($erreur as $value)
			echo $value ?>
		<input type='submit' value='Connexion' name="connexion"></input>
		<a href=" ./inscription.php"> Pas encore membre ? Inscrivez vous ! </a> 
	</form> 
</body>
</html>
