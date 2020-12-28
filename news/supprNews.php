<html>

<head>
    <meta charset="UTF-8">
    <title>Supprimer</title>
</head>

<body>
    <?php
    include '../bdd/connexion.php';

    $delete_stmt = $objPdo->prepare("DELETE FROM news WHERE idnews = :idnews");
    $delete_stmt->bindvalue(':idnews', $_GET['id'], PDO::PARAM_INT);
    $delete_stmt->execute(); 
    header('Location:./newsUtilisateur.php');
    ?>
</body>

</html>