<?php

include ('../bdd/connexion.php');

$nom = $prenom = $adressemail = $motdepasse = $confirmdp = $correctmdp = "";
$formValid = true;
$correctmdp = true;
$erreur = array();

?>

<html>

<head>
    <link rel="stylesheet" href="../style.css">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>Inscription</title>

    <script language="javascript" type="text/javascript">

    function Verification() {
        var nom = document.forms['formulaire'].nom
        var prenom = document.forms['formulaire'].prenom;
        var pseudo = document.forms['formulaire'].pseudo;
        var email = document.forms['formulaire'].adressemail;
        var regexMail = /^\w+[\+\.\w-]*@([\w-]+\.)*\w+[\w-]*\.([a-z]{2,4}|\d+)$/i;
        var mdp = document.forms['formulaire'].motdepasse;
        var confirmdp = document.forms['formulaire'].confirmdp;
        var formValid = document.forms['formulaire'].$formValid;
        var label = document.forms['formulaire'].erreur;
        var erreur = []; 
        //regex pour le nom et prénom
        var regex = /^[a-z]+[\-']?[[a-z]+[\-']?]*[a-z]+$/i;

        if (!nom.value.replace(/\s+/, '').length) {
            erreur.push('Nom vide !'); 
            formValid = false;
            ChangerStyle(nom);
        } else
            Reinitialiser(nom);

        if (!prenom.value.replace(/\s+/, '').length) {
            erreur.push('Prénom vide !');
            formValid = false;
            ChangerStyle(prenom);
        } else
            Reinitialiser(prenom);

        if (!(regex.test(nom.value)) || !(regex.test(prenom.value))) {
            alert('Merci de saisir un nom et un prénom valide');
            formValid = false;
            ChangerStyle(nom);
            ChangerStyle(prenom);
        } else {
            Reinitialiser(nom); 
            Reinitialiser(prenom);
        }

        if (!pseudo.value.replace(/\s+/, '').length) {
            erreur.push('Pseudo vide !');
            formValid = false;
            ChangerStyle(pseudo);
        } else
            Reinitialiser(pseudo);

        if (!email.value.match(regexMail)) {
            alert("Adresse email invalide");
            formValid = false;
            ChangerStyle(email);
        } else
            Reinitialiser(email);
    

        if (!mdp.value.replace(/\s+/, '').length) {
            erreur.push('Mot de passe vide!');
            formValid = false;
            ChangerStyle(mdp);
        }

        if (!confirmdp.value.replace(/\s+/, '').length) {
            erreur.push('Confirmation de mot de passe vide !');
            formValid = false;
            ChangerStyle(confirmdp);
        }

        if (mdp.value != confirmdp.value) {
            alert("Les deux mots de passe sont différents");
            formValid = false;
        } 

        if(erreur.length>0) alert('Merci de remplir les champs en pointillé'); 
 
        return formValid;
    }

    function ChangerStyle(objet) {
        objet.setAttribute('style', 'border-bottom: 2px dotted #FD5936;');
    }

    function Reinitialiser(objet) {
        objet.setAttribute('style', 'border-bottom:1px solid #ddd;;');
    }
</script>

<?php
    if (isset($_POST['valider'])) {
        if (!empty($_POST['nom'] && !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['adressemail']) && !empty($_POST['motdepasse']) && !empty($_POST['confirmdp']))){

            $nom = htmlentities($_POST['nom']);
            $prenom = htmlentities($_POST['prenom']);
            $adressemail = htmlentities($_POST['adressemail']);
            $motdepasse = crypt(htmlentities($_POST['motdepasse']), '$2a$07$usesomesillystringforsalt');
            $pseudo = htmlentities($_POST['pseudo']);

            $adressemaildouble = $objPdo->prepare("SELECT COUNT(*) FROM redacteur WHERE adressemail = ?");
            $adressemaildouble->bindValue(1, $adressemail, PDO::PARAM_STR);
            $adressemaildouble->execute();

            if ($adressemaildouble->fetchColumn() > 0) {
                $formValid = false;
                $erreur[] = "Un utilisateur est déjà inscrit avec cette adresse e-email. Choisissez une autre adresse e-email.";
            }

            $pseudodouble = $objPdo->prepare("SELECT COUNT(*) FROM redacteur WHERE pseudo = ?");
            $pseudodouble->bindValue(1, $pseudo, PDO::PARAM_STR);
            $pseudodouble->execute();

            if ($pseudodouble->fetchColumn() > 0) {
                $formValid = false;
                $erreur[] = "Ce pseudo est déjà pris. Merci d'en choisir un nouveau";
            }


            if ($_POST['motdepasse'] != $_POST['confirmdp']) {
                $correctmdp = false;
                $erreur[] = "Les deux mots de passes ne correspondent pas.";
            }

            if ($formValid and $correctmdp) {

                $insert_red = $objPdo->prepare("INSERT INTO redacteur(nom, prenom, adressemail, motdepasse, pseudo) VALUES(?, ?, ?, ?, ?)");
                $insert_red->bindValue(1, $nom, PDO::PARAM_STR);
                $insert_red->bindValue(2, $prenom, PDO::PARAM_STR);
                $insert_red->bindValue(3, $adressemail, PDO::PARAM_STR);
                $insert_red->bindValue(4, $motdepasse, PDO::PARAM_STR);
                $insert_red->bindValue(5, $pseudo, PDO::PARAM_STR);

                $insert_red->execute();

                session_start();
			    $_SESSION['adressemail'] = $adressemail;
                $_SESSION['pseudo'] = $pseudo;

                //on recupere l'id du redacteur qu'on vient de crée pour le connecter
                $requser = $objPdo->prepare("SELECT * FROM redacteur WHERE pseudo=? LIMIT 1");
                $requser->bindValue(1, $pseudo, PDO::PARAM_STR);
                $requser->execute();
                $userinfo = $requser->fetch();
                $_SESSION['idredacteur'] = $userinfo['idredacteur'];
                header("location:../index.php?");
            }
        }
        else $erreur[] = ("Merci de remplir tous les champs"); 
    }

?>

</head>

    <nav>
		<ul>
			<li><a href="../index.php">
					Retour à l'accueil
				</a></li>
		</ul>
	</nav>

<body>

    <h1 style="text-align:center; margin-right:90px;">Inscription</h1>

    <form method="post" action="inscription.php" onsubmit="return Verification()" name="formulaire">
        <label>Nom</label> </br>
        <input type="text" value="" name="nom" id="nom"></br>
        <label>Prénom</label> </br>
        <input type="text" value="" name="prenom" id="prenom"> </br>
        <label>Pseudo</label> </br>
        <input type="text" value="" name="pseudo" id="pseudo"> </br>
        <label>E-mail</label> </br>
        <input type="text" value="" name="adressemail" id="adressemail"> </br>
        <label>Mot de passe</label></br>
        <input type="password" value="" name="motdepasse" id="motdepasse"> </br>
        <label>Confirmer le mot de passe</label> </br>
        <input type="password" value="" name="confirmdp" id="confirmdp"> </br> </br>
        <?php foreach ($erreur as $value)
			echo $value ?>
        <input type="submit" value="Validez" name="valider">
        <a href=" ./authentification.php"> Déjà membre ? Connectez vous ! </a> 
    </form>
</body>

</html>