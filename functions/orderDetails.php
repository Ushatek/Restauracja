<html>
<head>
<meta charset="utf-8">
<title>Szczegóły zamówienia</title>
<link rel="stylesheet" href='../css/style.css' type="text/css">
</head>
<!--Widok z pozycjami zamówienia-->
<body id="body">
	<div id="naglowek_strony">
        <div id="logo_strony">
			<a href=".."></a>
		</div>
		<div id="menu_gorne">
            <?php
				require_once 'functions.php';
				echo getMainMenu("..");
			?>
		</div>
    </div>
	</div>

	<div id="rezerwacje_main">
	<hr>
		<?php
			if(isset($_SESSION['uid']) && isset($_GET['id']))
			{
				getOrderDetails($_GET['id']);
			}
		?>

	</div>
	<br>

	<div id="stopka">
		Programowanie w PHP - Projekt - Autorzy: Kamil Ślusar, Michał Wójcik</a>
	</div>
</body>
</html>
