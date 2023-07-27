<html>
<head>
<meta charset="utf-8">
<title>Logowanie</title>
<link rel="stylesheet" href='../css/style.css' type="text/css">
</head>
<!--Widok, w którym witamy użytkownika po pomyslnym zalogowaniu -->
<body id="body">
	<div id="naglowek_strony">
        <div id="logo_strony">
			<a href=".."></a>
		</div>
		<div id="menu_gorne">
            <?php
				require_once 'functions.php';
				echo getMainMenu("..",".");
			?>
		</div> 
    </div> 
	</div>
	
	<div id="rejestracja_main">
	<hr>
		<div id="formularz_main">
		<?php
			require_once './functions.php';

			if(isset($_GET['result']) && $_GET['result'] == 'success')
			{
				echo '<p>Pomyślnie zalogowano!</p><a href="..">Strona główna.</a>';
			}
			else
			{
				echo getRegisterForm(".");
			}
		?>
		
		</div>
	</div> 
	<br>
	<div id="stopka">
		Programowanie w PHP - Projekt - Autorzy: Kamil Ślusar, Michał Wójcik</a>
	</div>
</body>
</html>