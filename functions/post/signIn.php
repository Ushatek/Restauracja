<?php
require_once '../../functions/functions.php';
//funkcja cześciowo sprawdzająca poprawność przesłanych w formularzu (czy zostały przesłane dane)
//oraz poprawiająca bezpieczeństwo eleminując potencjalnie niebezpiecznie wprowadzone danych przez użytkownika (np. linki)
if($_POST)
{
	//sprawdzamy, czy wpisano nazwę użytkownika i hasło, jeśli tak to zamieniamy zawartość funkcją htmlspecialchars
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : null;//htmlspecialchars zamienia znaki specjalne (np. ",>) na bezpieczne odpowiedniki w html, 
    $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : null;//tak by wpisany tekst został stringiem a nie np. kodem html	
	
    if(mb_strlen($username) && mb_strlen($password))//sprawdzamy czy długość nawzy i hasła jest większa niż 0 
	{
        signIn($username, $password, "..");//jeśli tak, to przekazujemy wprowadzone dane do funkcji signIn
    }
}
?>