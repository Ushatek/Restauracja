<?php
require_once '../../functions/functions.php';

if(!isset($_SESSION['uid'])){
    header("Location: ".".."."/login.php");
}

//jeśli w przesłanych z formularza danych jest wybrana opcja rezerwacji i wpisana data
if($_POST
    && isset($_POST['reservation']) && is_array($_POST['reservation']) && !empty($_POST['reservation'])
    && isset($_POST['date']) && is_array($_POST['date']) && !empty($_POST['date']))
{
	$straznik = 0;//zmienna pomocnicza dla sprawdzenia wypełnienia dat
	foreach ($_POST['reservation'] as $key => $value) //sprawdzamy czy wszystkie zaznaczone daty zostaly wypelnione a daty nie są z przeszłości
	{
		if((strlen($_POST['date'][$key]) == 0) || (($_POST['date'][$key]) < date("Y-m-d\TH:i")))
		{
			$straznik = 1;
		}
    }
	if($straznik == 0)//jeśli wszystkie zaznaczone rezerwacje miały wypełnioną datę z przyszłości to wywołujemy funkcję dodająca rezerwacje do bd, jeśli nie to nie dodajemy żadnej
	{
		makeReservation($_POST['reservation'], $_POST['date'], "../..");//dodawanie do bd rezerwacji
	}
	else
	{
		//komunikat o nie podaniu dat dla zaznaczonych rezerwacji
		getDBQueryErrorJS("Błąd w wyborze dat rezerwacji! Podaj poprawne daty.", "../../");
	}
}
else
{
	header("Location: "."../.."."/");
}
?>