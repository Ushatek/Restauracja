<?php
require_once '../../functions/functions.php';

if(!isset($_SESSION['uid']))
{
    header("Location: ".".."."/login.php");
}
//sprawdzamy, czy użytkownik zaznaczył pole wyboru, wybrał rezerwacje oraz wpisał ilość potraw a także, czy ilość zaznaczonych zamówień jest równa ilości wybranych rezerwacji
if($_POST
    && isset($_POST['order']) && is_array($_POST['order']) && !empty($_POST['order'])
    && isset($_POST['reservations']) && is_array($_POST['reservations']) && !empty($_POST['reservations'])
    && isset($_POST['qty']) && is_array($_POST['qty']) && !empty($_POST['qty'])
    && (int) count($_POST['order']) === (int) count($_POST['reservations']))
{
	orderMeal($_POST['order'], $_POST['reservations'], $_POST['qty'], "..");//wywołujemy funkcję dodającą zamówienie do bd
}
else
{
	//informacja o błędzie zkładania zamówienia
	getDBQueryErrorJS("Błąd w wyborze pozycji zamówienia! Spróbuj ponownie.", "../menu.php");
}
?>
