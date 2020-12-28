<?php

require('bdd/connexion.php');

session_start();

$news_bdd = $objPdo->prepare('
    SELECT *
    FROM news
    INNER JOIN redacteur ON news.idredacteur = redacteur.idredacteur
    ORDER BY datenews DESC
    ');

$news_bdd->execute();

function afficheDonnees($requete){
    while ($donnees = $requete->fetch()) {?>
    <div class="divnews">

        <div class="newstitle">

            <h2><?php echo $donnees['titrenews']; ?></h2>

        </div>

        <div class="txtnews">
            <blockquote>
                <p> <?php echo substr($donnees['textenews'], 0, 250) . '[...]' ?> <a href="news/voir_article.php?idnews=<?php echo $donnees['idnews'] ?>">Lire la suite </a></p>
            </blockquote>
            <footer style="margin-bottom: 5px;">
                <?php echo "Par " . $donnees['pseudo'] . ", le " . date_format(date_create($donnees['datenews']), 'D d M Y'); ?>
            </footer>
        </div>
    </div><?php
    }
}?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>
        Bienvenue sur le blog
    </title>

</head>

<body>
    <nav>
        <ul>
            <li><a href="index.php">
                    Accueil
                </a></li>
            <?php
            if (empty($_SESSION['idredacteur'])) {
            ?>
                <li><a href="./user/authentification.php">
                        Connexion
                    </a></li>
                <li><a href="./user/inscription.php">
                        Inscription
                    </a></li>
            <?php } else { ?>
                <li><a href="./news/newsUtilisateur.php"> 
                        Mes news
                    </a></li>
                <li><a href="./news/ajout_article.php">
                        Nouveau sujet
                    </a></li>
                <li><a href="./user/redacteur.php">
                    Informations personnelles
                </a></li>
                <li><a href="./user/deconnexion.php" style="margin-left:50px" onclick="return confirm('Etes vous sûr de vouloir vous déconnecter ?');">
                        Déconnexion
                    </a></li>
            <?php } ?>
        </ul>
    </nav>

    <form  method="POST" action="index.php">
        <select style="width:500px; height:40px;" name="theme">

                <option value="0"> Toutes les news </option>
           
            <?php $reponse = $objPdo->query('SELECT * FROM theme ');

            while ($donnees = $reponse->fetch()) {
                echo  '<option value=' . $donnees['idtheme'] . '>' . $donnees['description'] . '</option>';
            }
            ?>
            <br />
        </select>
        <input type="date" name="date"></input>
        <input type="submit" value="Afficher les news par theme et/ou par date" name="Afficher"></input>
    </form>


    <?php  

    
    
    if(isset($_POST['Afficher'])){
        if (($_POST['theme']!=0) && ($_POST['date']!='')) {
           
            $theme=$_POST['theme']; 
            $date=$_POST['date'];

            $news_bddThemeDate = $objPdo->prepare('SELECT *
                                                FROM news
                                                INNER JOIN redacteur ON news.idredacteur = redacteur.idredacteur
                                                WHERE idtheme=:id AND DATE(datenews)=:date  
                                                ORDER BY datenews DESC
                                                ');

            $news_bddThemeDate->bindValue(':id', $theme, PDO::PARAM_INT);
            $news_bddThemeDate->bindValue(':date', $date, PDO::PARAM_STR);
            $news_bddThemeDate->execute();
            afficheDonnees($news_bddThemeDate);

            $existe = $news_bddThemeDate->rowCount();
            if($existe==0) echo "Aucune news appartenant a ce thème n'a été publiée à cette date"; 
        }
           
        else if (($_POST['date']=='') && ($_POST['theme']!=0)){
            $theme=$_POST['theme'];  

            $news_bddSelect = $objPdo->prepare('SELECT *
                                                FROM news
                                                INNER JOIN redacteur ON news.idredacteur = redacteur.idredacteur
                                                WHERE idtheme=:id 
                                                ORDER BY datenews DESC
                                                ');

            $news_bddSelect->bindValue(':id', $theme, PDO::PARAM_INT);
            $news_bddSelect->execute();
            afficheDonnees($news_bddSelect);

            $existe = $news_bddSelect->rowCount();
            if($existe==0) echo "Aucune news appartient à ce thème"; 
        }
        
        else if (($_POST['theme']==0) && ($_POST['date']!='')){ 
            $date=$_POST['date']; 
            $news_bddSelectDate = $objPdo->prepare('SELECT *
                                                FROM news
                                                INNER JOIN redacteur ON news.idredacteur = redacteur.idredacteur
                                                WHERE DATE(datenews)=:date 
                                                ORDER BY datenews DESC');
        
            $news_bddSelectDate->bindValue(':date', $date, PDO::PARAM_STR);
            $news_bddSelectDate->execute();

            afficheDonnees($news_bddSelectDate);

            $existe = $news_bddSelectDate->rowCount();
            if($existe==0) echo "Aucune news n'a été publiée à cette date"; 
        }

        else {
            afficheDonnees($news_bdd); 
        }
    }

    else afficheDonnees($news_bdd);
    ?>
    
    <footer class="nom">
    <p>Créer par Boudou Zachary et THIL Claire.</p>
    </footer>
</body>

</html>