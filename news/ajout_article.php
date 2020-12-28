<?php

include '../bdd/connexion.php';

session_start();

$erreur = array();
$erreurTheme = array();

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../style.css">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title> Ajouter un article </title>
    
    <script language="javascript" type="text/javascript">
        
        function Verification() {
            
			var titre = document.forms['formulaire'].titre;
			var article = document.forms['formulaire'].article;
			var formValid = document.forms['formulaire'].$formValid;
			var erreur = [];

			if (!titre.value.replace(/\s+/, '').length) {
				erreur.push("Merci de saisir un titre à votre article");
				formValid = false;
			} 

			if (!article.value.replace(/\s+/, '').length) {
				erreur.push("Vous ne pouvez pas publier un article vide");
				formValid = false;
			}

			if (erreur.length>0) alert("Vous ne pouvez pas publier un article sans titre et/ou sans contenu.")
			return formValid;
		}

        function verifTheme() {
            
			var titre = document.forms['formTheme'].newTheme;
            var regex = /^[a-z]+[\-']?[[a-z]+[\-']?]*[a-z]+$/i;

            if(!(regex.test(titre.value))){
                alert("Le nom de thème que vous souhaitez ajouter n'est pas valide"); 
                return false; 
            }
            else return true; 

			if (!titre.value.replace(/\s+/, '').length) {
				alert("Merci de saisir un titre à ce nouveau theme");
				return false; 
			} 
            else return true; 

			
        }

        function Annuler() {
        if (confirm("Souhaitez-vous vous vraiment revenir à l'accueil ?"))
            window.location.href = "../index.php"; 
        }

	</script>

    <?php

    if (isset($_POST["ajoutTheme"])) {

        $newTheme = $_POST['newTheme'];

        $themedouble = $objPdo->prepare("SELECT COUNT(*) FROM theme WHERE description= ?");
        $themedouble->bindValue(1, $newTheme, PDO::PARAM_STR);
        $themedouble->execute();

        if (empty($newTheme))
            $erreurTheme[] = "Veuillez renseigner un titre pour le nouveau thème";

        else if ($themedouble->fetchColumn() > 0)
            $erreur[] = "Ce thème existe déjà";

        else {
            $insert_theme = $objPdo->prepare("INSERT INTO theme (description) VALUES(?)");
            $insert_theme->bindValue(1, $newTheme, PDO::PARAM_STR);
            $insert_theme->execute();
        }
    }

    if (isset($_POST['Envoyer'])) {
        if (!empty($_POST['titre']) && !empty($_POST['article'])) {

            $titre = htmlentities($_POST['titre']);
            $article = htmlentities($_POST['article']);

            $insert_stmt = $objPdo->prepare("INSERT INTO `news`(`idtheme`, `titrenews`, `textenews`, `idredacteur`) VALUES (:theme, :titre, :article, :redacteur);");
            $insert_stmt->bindvalue(':theme', $_POST['theme'], PDO::PARAM_INT);
            $insert_stmt->bindvalue(':titre', $titre, PDO::PARAM_STR);
            $insert_stmt->bindvalue(':article', $article, PDO::PARAM_STR);
            $insert_stmt->bindvalue(':redacteur', $_SESSION['idredacteur'], PDO::PARAM_INT);
            $insert_stmt->execute(); 
            header('Location:./newsUtilisateur.php'); 
        }
        else $erreur[]=('Vous ne pouvez pas publier un article sans titre ou sans contenu'); 
    }

    ?>
</head>

<body>

<nav>
    <ul>
        <li><a href="../index.php">
                Accueil
        </a></li>
        <li><a href="./newsUtilisateur.php"> 
                Mes news
            </a></li>
        <li><a href="../user/redacteur.php">
                    Informations personnelles
                </a></li>
        <li><a href="../user/deconnexion.php" style="margin-left:270px" onclick="return confirm('Etes vous sûr de vouloir vous déconnecter ?');">
                Déconnexion
            </a></li>
    </ul>
</nav>

<h1 style="text-align:center; font-size:25px;  margin-right:90px"> Ajouter un article </h1>

    <form style="width:860px;" method="POST" action="ajout_article.php" name="formTheme" onsubmit="return verifTheme();">
    <input type="text" name="newTheme" maxlength='50'></input><input type="submit" value="Ajouter un thème" name="ajoutTheme"><br /></input><br />
        <?php foreach ($erreurTheme as $value)
            echo $value ?>
    </form>
    <form style="width:860px;" method="POST" action="ajout_article.php" name="formulaire" onsubmit="return Verification()">
        Thème : <select style="width:92.5%; height:40px;" name="theme">
            <?php $reponse = $objPdo->query('SELECT * FROM theme ');

            while ($donnees = $reponse->fetch()) {
                echo  '<option value=' . $donnees['idtheme'] . '>' . $donnees['description'] . '</option>';
            }
            ?>
            <br /></select> <br /><br />
        Titre : <input type="text" maxlength='100' size="100" name="titre"><br /></input>
        Article: <br /> <textarea rows='25' cols='116' name="article"></textarea><br />
        <?php foreach ($erreur as $value)
            echo $value ?><br />
        <input type='submit' value='Envoyer' name='Envoyer' style='width:49%; margin-right:1% '></input><input type="button" value="Annuler" onclick='Annuler()' style='width:49%; margin-left:1% '></input>
    </form>


</body>

</html>