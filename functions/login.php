<html>
<head>
<meta charset="utf-8">
<title>Logowanie</title>
<link rel="stylesheet" href='../css/style.css' type="text/css">
</head>
<!--Widok, który wyświetla formularz logowania za pomocą funkcji getLoginForm-->
<body id="body">
	<div id="naglowek_strony">
        <div id="logo_strony">
			<a href=".."></a>
		</div>
		<div id="menu_gorne">
            <?php
				require_once 'functions.php';
				echo getMainMenu("..", ".");
			?>
		</div> 
    </div> 
	</div>
	
	<div id="rejestracja_main">
	<hr>
		<div id="formularz_main">

		<?php
			require_once './functions.php';
			echo getLoginForm(".");//wywołanie funkcji z formularzem logowania
		?>
		</div>
	</div> 

	<br>
	<div id="stopka">
		Programowanie w PHP - Projekt - Autorzy: Kamil Ślusar, Michał Wójcik</a>
	</div>

</body>
</html>