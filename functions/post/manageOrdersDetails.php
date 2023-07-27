<?php
require_once '../../functions/functions.php';

if(!isset($_SESSION['uid']))
{
    header("Location: ".".."."/login.php");
}
//sprawdzamy czy w tablicy post zostal zaznaczony checkbox, wpisana ilosc i czy te ilosci sie zgadzają 
if($_POST
    && isset($_POST['order']) && is_array($_POST['order']) && !empty($_POST['order'])
    && isset($_POST['ilosci']) && is_array($_POST['ilosci']) && !empty($_POST['ilosci'])
	&& (int) count($_POST['order']) === (int) count($_POST['ilosci']))
{
    manageOrdersDetails($_POST, "..");
}
else
{
	//informacja o błędzie edycji zamówienia
	getDBQueryErrorJS("Błąd w edycji zamówienia! Spróbuj ponownie.", "../myOrders.php");
}
?>