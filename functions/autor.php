<html>
<head>
<meta charset="utf-8">
<title>Restauracja "U PHPa"</title>
<link rel="stylesheet" href='../css/style.css' type="text/css">
</head>
<!--Widok strony głównej, zawieramy w nim widok dostępnych stolików jak i możliwości ich rezerwacji-->
<body id="body">
	<div id="naglowek_strony">
        <div id="logo_strony">
			<a href=".."></a>
		</div>
        <div id="menu_gorne">
            <?php
				require_once './functions.php';
				echo getMainMenu("..");
			?>
		</div>
    </div>

    <div id="menu_glowne">
	<hr><br>
		<p style="text-align: center; font-size:30px; color:white; "> <b>Michał Wójcik</b> </p>
		<p style="text-align: center; font-size:20px; color:white"> moja ocena projektu: 25 </p>
		<p style="text-align: center; font-size:20px; color:white"> mój stopień uczestnictwa w projekcie: 30% </p>
    </div>
	<div id="stopka">
		Programowanie w PHP - Projekt - Autorzy: Kamil Ślusar, Michał Wójcik</a>
	</div>
</body>
</html>
