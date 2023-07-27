<html>
<head>
    <meta charset="utf-8">
    <title>Restauracja "U PHPa"</title>
    <link rel="stylesheet" href='../css/style.css' type="text/css">
</head>
<!--Widok menu posiłków i ich cen-->
<body id="body">
<div id="naglowek_strony">
    <div id="logo_strony">
        <a href=".."></a>
    </div>
    <div id="menu_gorne">
        <?php
        require_once 'functions.php';
        echo getMainMenu("..", "..");
        ?>
    </div>
</div>

<div id="menu_glowne">
    <hr><br>
    <?php
    require_once 'functions.php';
    echo getMealList("..");//pobieramy listę potraw

    ?>
</div>
<div id="stopka">
    Programowanie w PHP - Projekt - Autorzy: Kamil Ślusar, Michał Wójcik</a>
</div>
</body>
</html>
