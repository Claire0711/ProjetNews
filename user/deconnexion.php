<?php

session_start();

session_destroy();

$_SESSION['idredacteur'] = "";

?>

<!doctype html>
<html>

<head>
	<link rel="stylesheet" href="../style.css">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>Deconnexion</title>
</head>

<body>
<div class="deco">
    <p>Vous avez été déconnecté </p>
    <a href="../index.php">Retour à l'accueil</a>
</div>
</body>

</html>