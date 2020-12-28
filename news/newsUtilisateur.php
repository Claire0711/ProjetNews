<?php

require('../bdd/connexion.php');

session_start();

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../style.css">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>
        Mes News
    </title>


</head>

<body>

    <nav>
        <ul>
            <li><a href="../index.php">
                    Accueil
            </a></li>
            <li><a href="./ajout_article.php"> 
                    Nouveau sujet
                </a></li>
                <li><a href="../user/redacteur.php"> 
                    Informations personnelles
            </a></li>
            <li><a href="../user/deconnexion.php" style="margin-left:270px" onclick="return confirm('Etes vous sûr de vouloir vous déconnecter ?');">
                    Déconnexion
                </a></li>
        </ul>
    </nav>

   <h1 style="text-align:center; margin-right:90px; font-size:25px;"> Mes news </h1> 

    <?php
    $newsUtilisateur = $objPdo->prepare('SELECT *
                                        FROM news
                                        INNER JOIN redacteur ON news.idredacteur = redacteur.idredacteur
                                        WHERE news.idredacteur=:id
                                        ORDER BY datenews DESC
                                        ');

    $newsUtilisateur->bindValue(':id', $_SESSION['idredacteur'], PDO::PARAM_INT);
    $newsUtilisateur->execute(); 

    while ($donnees = $newsUtilisateur->fetch()) {?>
         
        <div class="divnews">
                
            <div class="newstitle">

                <h2><?php echo $donnees['titrenews']; ?></h2>

            </div>

            <div class="txtnews">
                <blockquote>
                    <p> <?php echo $donnees['textenews'] ?></p>
                </blockquote>
                <?php $id=$donnees['idnews']; ?>
                <a style="float:right;" onclick="return confirm('Etes vous sûr de vouloir supprimer cette news ?')" href="./supprNews.php?id=<?php echo htmlspecialchars($id)?>"> Supprimer la news </a>  <a style="float:right; margin-right:30px;" href="./editNews.php?id=<?php echo htmlspecialchars($id)?>"> Modifier la news </a>
        </form>
            </div>
        </div><?php
    }
    ?>
    
</body>
</html>

