<?php

include '../bdd/connexion.php';

session_start();

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../style.css">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title> Modifier un article </title>

    <script>
         function Annuler() {
        if (confirm("Souhaitez-vous vous vraiment revenir à l'accueil ?"))
            window.location.href = "../index.php"
        }
    </script>
    <?php

    $erreur = array();

    if (isset($_POST['Envoyer'])) {

        if (!empty($_POST['titre']) && !empty($_POST['article'])) {
            $titre = htmlentities($_POST['titre']);
            $article = htmlentities($_POST['article']);
            $idnews =  $_GET['id'];

            $update_stmt = $objPdo->prepare("UPDATE news SET titrenews=:titre, textenews=:article WHERE idnews= $idnews; ");
            $update_stmt->bindValue('titre', $titre, PDO::PARAM_STR);
            $update_stmt->bindValue('article', $article, PDO::PARAM_STR);
            $update_stmt->execute(); 
            header('Location:./newsUtilisateur.php'); 
        }
        else $erreur[]="titre ou article vide"; 
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
            <li><a href="./ajout_article.php"> 
                    Nouveau sujet
            </a></li>
            <li><a href="../user/redacteur.php"> 
                    Informations personnelles
            </a></li>
            <li><a href="../user/deconnexion.php" style="margin-left:100px" onclick="return confirm('Etes vous sûr de vouloir vous déconnecter ?');">
                    Déconnexion
                </a></li>
        </ul>
    </nav>

<h1 style="text-align:center; margin-right:90px; font-size:25px"> Modifier un article </h1>

<?php
    $selectSTMT = $objPdo->prepare("SELECT * FROM news WHERE idnews=:id LIMIT 1");
    $selectSTMT->bindValue('id', htmlspecialchars($_GET['id']), PDO::PARAM_INT);
    $selectSTMT->execute(); 
    
    foreach ($selectSTMT as $row) {
        $titre = htmlspecialchars($row['titrenews']);
        $article = htmlspecialchars($row['textenews']);
    }?>

    <form style="width:860px;" method="POST">
        Titre : <input type="text" maxlength='100' size="100" name="titre" value=<?php echo '"' . html_entity_decode($titre). '"' ?>><br /></input>
        Article: <textarea rows='25' cols='116' name="article"><?php echo trim(html_entity_decode($article))?></textarea><br />
        <?php foreach ($erreur as $value)
            echo $value ?><br />
        <input type='submit' value='Envoyer' name='Envoyer' style='width:49%; margin-right:1% '></input><input type="button" value="Annuler" onclick='Annuler()' style='width:49%; margin-left:1% '></input>
    </form>


</body>

</html>