<?php 

session_start();

require('../bdd/connexion.php');

$idnewss = $_GET['idnews'];

if (isset($_POST['ajout-commentaire'])){
    $valid = true;
    $textecom  = $_POST['textecom'];
    
    if(!isset($textecom)){
        $valid = false;
        $mess_commentaire = "Il faut mettre un commentaire";
    }
    
    if($valid == true){
        $ajoutcom = $objPdo->prepare("INSERT INTO commentaires(idnews, idredacteur, datecom, textecom) VALUES(?, ?, CURRENT_TIMESTAMP(), ?)");

        $ajoutcom->bindValue(1, $idnewss, PDO::PARAM_INT);
        $ajoutcom->bindValue(2, $_SESSION['idredacteur'], PDO::PARAM_INT);
        $ajoutcom->bindValue(3, $textecom, PDO::PARAM_STR);

        $ajoutcom->execute();
    }
}

$affcom = $objPdo->prepare('
  SELECT *
  FROM commentaires 
  INNER JOIN redacteur ON commentaires.idredacteur = redacteur.idredacteur
  WHERE idnews = ?
  ORDER BY datecom DESC
  ');

$affcom->bindValue(1, $idnewss, PDO::PARAM_INT);
$affcom->execute();

$reqt = $objPdo->prepare('
  SELECT *
  FROM news
  INNER JOIN redacteur ON news.idredacteur = redacteur.idredacteur
  WHERE idnews = ?
  ');

$reqt->bindValue(1, $idnewss, PDO::PARAM_INT);
$reqt->execute();



?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../style.css">
<title>News</title>
</head>

<body>

  <nav>
        <ul>
            <li><a href="../index.php">
                    Accueil
                </a></li>
            <?php
            if (empty($_SESSION['idredacteur'])) {
            ?>
                <li><a href="../user/authentification.php">
                        Connexion
                    </a></li>
                <li><a href="../user/inscription.php">
                        Inscription
                    </a></li>
            <?php } else { ?>
                <li><a href="../news/newsUtilisateur.php"> 
                        Mes news
                    </a></li>
                <li><a href="../news/ajout_article.php">
                        Nouveau sujet
                    </a></li>
                <li><a href="../user/deconnexion.php" style="margin-left:270px" onclick="return confirm('Etes vous sûr de vouloir vous déconnecter ?');">
                        Déconnexion
                    </a></li>
            <?php } ?>
        </ul>
    </nav>

    <?php 
      $donnees = $reqt->fetch(); 
    ?>

      <div class="newsd">     
              <h2>
                  <?php echo $donnees['titrenews']; ?>
              </h2>
            <blockquote>
                <p>
                  <?php echo ($donnees['textenews']) ?>
                </p>
            </blockquote>
            <footer>
                <?php echo $donnees['pseudo'] . ", le " . $donnees['datenews']; ?>
            </footer>

      </div>

        <?php
        if(!empty($_SESSION['idredacteur'])){
        ?>

      <div style="background: white; box-shadow: 0 5px 15px rgba(0, 0, 0, .15); padding: 5px 10px; border-radius: 10px; margin-top: 20px">
          <h3>Commenter</h3>
            <form method="post">
              <div class="form-group">
                <textarea class="form-control" id="textecom" name="textecom" rows="5" cols="70" placeholder="Écrivez-votre commentaire ...">
                </textarea>
              </div>
            <div>
          <button type="submit" id ="ajout-commentaire" name="ajout-commentaire">Ajouter commentaire</button>
      </div>
    </form>
    </div>

    <?php 
      }
    ?>

      <div>
        <h3>Commentaires</h3>

        <table>

          <?php 
          foreach ($affcom as $donnees) {
          ?>

          <tr>
            <td> <?php echo $donnees['pseudo']; ?> </td>
            <td> <?php echo $donnees['textecom']; ?> </td>
            <td> <?php echo $donnees['datecom']; ?> </td>
          </tr>

          <?php
           }
          ?>
        </table>

      </div>

   </body>
  </html>