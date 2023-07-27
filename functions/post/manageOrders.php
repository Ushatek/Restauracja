<?php
require_once '../../functions/functions.php';

if(!isset($_SESSION['uid']))
{
    header("Location: ".".."."/login.php");
}
//sprawdzamy czy przesłane w post wartości nie są puste
if($_POST
    && isset($_POST['order']) && is_array($_POST['order']) && !empty($_POST['order'])
    && (isset($_POST['pay']) || isset($_POST['delete']) || isset($_POST['download']))

)
{
    manageOrders($_POST, "..");//wywołujemy funkcje
}
else
{
    header('Location: '."..".'/myOrders.php');
    die();
}
?>