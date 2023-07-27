<?php

session_start();

//Połącznie z bazą danych
function connectToDatabase()
{
    $serverName = "localhost";
    $username = "root";
    $password = "";
	//łączymy z bazą danych restauracja
    $connection = new mysqli($serverName, $username, $password, "restauracja");
    $connection->set_charset('utf8');
    if($connection->connect_error)
	{
        die("Problem z połączeniem z bazą danych");
    }
    return $connection;
}

//wiadomość przy błędach
function getDBQueryError()
{
    return 'Błąd w zapytaniu do bazy danych';
}

//wyskakująca wiadomość w oknie przy błędach (javascript)
function getDBQueryErrorJS($message, $path)
{
    echo "<script type='text/javascript'>alert('$message'); window.location = '$path';</script>";
}

//funkcja tworząca górne menu
function getMainMenu($path)
{
	//tworzymy menu w liście
    $menu = '<ul>';
    $menu .= '<li><a href="' .$path . '">Zarezewuj stolik</a></li>';//
    $menu .= '<li><a href="'.$path.'/functions/autor.php">Autor</a></li>';//
    $menu .= '<li><a href="'.$path.'/functions/menu.php">Menu</a></li>';
	//zależnie od tego, czy użytkownik jest zalogowany dodajemy odpowiednie elementy listy
    $menu .= isset($_SESSION['uid']) ? '<li><a href="'.$path.'/functions/myReservations.php">Moje rezerwacje</a></li>
		<li><a href="'.$path.'/functions/myOrders.php">Moje zamówienia</a></li>
		<li><a href="'.$path.'/index.php?action=logout">Wyloguj się</a></li>' : '<li><a href="'.$path.'/functions/login.php">Zaloguj się</a></li>
		<li><a href="'.$path.'/functions/register.php">Utwórz konto</a></li>';
		
    $menu .= '</ul>';
    return $menu;
}

//funkcja wywoływana przez widok login.php, zawiera wygląd formularza logowania i jego działanie
function getLoginForm($path)
{
   return '<h1>Zaloguj się</h1><form method="post" action="'.$path.'/post/signIn.php">'.//wciśnięcie zaloguj spowoduje przesłanie danych do post/signIn.php
   '<label>Podaj adres e-mail</label><br/>
   <input type="email" placeholder="Podaj e-mail" name="username" required /><br/> './/typ email waliduje formę wpisanego e-maila
   '<label>Podaj hasło</label>
   <br/><input type="password" placeholder="Podaj hasło" name="password" required /><br><br>
   <input type="submit" value="Zaloguj" /><br/><br>
   <label>Nie masz konta?</label><br>
   <a href="'.$path.'/register.php">Załóż konto</a></form>';
}

//funkcja wywoływana przez widok register.php (działanie podobne do getLoginForm), zawiera wygląd formularza rejestracji i jego działanie
function getRegisterForm($path)
{
    return '<h1>Utwórz konto</h1>
	<form method="post" action="'.$path.'/post/register.php">
	<label>Podaj adres e-mail</label><br/>
	<input type="email" placeholder="Podaj e-mail" name="username" required /><br/>
	<label>Podaj hasło</label><br/>
	<input type="password" placeholder="Podaj hasło" name="password" required /><br>
	<br><input type="submit" value="Stwórz konto" /><br/><br>
	<label>Masz już konto?</label><br>
	<a href="'.$path.'/login.php">Zaloguj się</a></form>';
}

//funkcja tworząca konta użytkowników
function createAccount($username, $password, $path)
{
    $connection = connectToDatabase();//połączenie z bd
	//sprawdzamy, czy w bazie jest dany użytkownik pobierając z zapytania bd użytkowników o podanym loginie
    $query = mysqli_query($connection, 'SELECT * FROM `users` WHERE `username` = "'.$username.'" LIMIT 1');
	//jeśli jest użytkownik o danym loginie
    if($query->num_rows > 0)
	{
		//przy błędzie wywołuje się mini skrypt javascript, który wowołuje okno z informacją i przekierowuje po doczytanie ponownie do strony logowanie
		getDBQueryErrorJS("Podany użytkownik już istnieje! Zaloguj się.", "../login.php");	
    }
	
	$zaszyfrowane_haslo = password_hash($password, PASSWORD_DEFAULT);//szyfrujemy haslo
	//dodajemy użytkownika do bazy
	$query = mysqli_query($connection, 'INSERT INTO users (username, password) VALUES ("'.$username.'", "'.$zaszyfrowane_haslo.'")');
    if($query)//jeśli dodaliśmy bez błędu to wyświetlamy informację użytkownikowi
	{
		header('Location: '.$path.'/register.php?result=success');       
    }
    else
	{
        $message = "Błąd bazy danych.";//treść komunikatu przy błędzie dodawania użytkownika
		//przy błędzie wywołuje się mini skrypt javascript, który wowołuje okno z informacją i przekierowuje po doczytanie ponownie do strony logowanie
		echo "<script type='text/javascript'>alert('$message'); window.location = '../login.php';</script>";	
    }
		
	die();
}

//funkcja logująca użytkownika po podaniu przez niego nazwy użtykownika i hasła 
function signIn($username, $password, $path)
{
    $connection = connectToDatabase();//łaczenie z bazą danych
	
	//zapytanie, które pobiera z bd wiersz, który odpowiada przesłanej nazwie użytkownika
    $query = mysqli_query($connection, 'SELECT * FROM `users` WHERE `username` = "'.$username.'" LIMIT 1');

    if($query->num_rows == 1)//funkcja wywołuje się, gdy znaleziono użytkownika o wprowadzonej nazwie
	{
        while($obj = $query->fetch_object())//bo zmiennej obj pobieramy wiersz z bd zawierający informacje o użytkowniku
		{
			
            //if(crypt($password, $obj->password) == $obj->password)//hashujemy (szyfrujemy) wpisane przez użytkownika 
            if(password_verify($password, $obj->password))//sprawdzamy, czy hasła się zgadzają, potrzebujemy password verify, bo hasło jest zaszyfrowane 
			{
                $_SESSION['uid'] = $obj->id;//przypisujemy id zalogowanego użytkownika w tablicy sesji (potrzebne to innych funkcji)
                header('Location: '.$path.'/signIn.php?result=success'); //wyświetlamy stronę z potwierdzeniem zalogowania 
            }
			else
			{
				//przy błędzie wywołuje się mini skrypt javascript, który wowołuje okno z informacją i przekierowuje po doczytanie ponownie do strony logowanie
				getDBQueryErrorJS("Błędne hasło! Spróbuj ponownie.", "../login.php");
			}
        }
    }
	else//gdy nie znaleziono
	{
		//przy błędzie wywołuje się mini skrypt javascript, który wowołuje okno z informacją i przekierowuje po doczytanie ponownie do strony logowanie
		getDBQueryErrorJS("Brak podanego użytkownika! Spróbuj ponownie.", "../login.php");
	}
}

//funkcja, do wylogowania użytkownika
function getLogout($path)
{
	//czyścimy sesje i wyświetlamy stronę głóną
    session_unset();
    session_destroy();
    header('Location: '.$path.'/index.php');
}

//funckja wyświetlająca formularz z listą stolików i możliwością ich rezerwacji na podaną datę
function getTablesList($path)
{
    $connection = connectToDatabase();//łączymy się z bd
    $query = mysqli_query($connection, "SELECT * FROM `tables`");//lista stolików
    if(!$query)
	{
        echo getDBQueryError();
        die();
    }
    else
	{
        $reservations = [];//tablica do przechowywania zarezerwowanych id stolików
        $date = date('Y-m-d H:i:s');
        $rQuery = mysqli_query($connection, 'SELECT table_id FROM `table_reservation` WHERE `reservation_date` > "'.$date.'"');//zapytanie sprawdzające czy na dany stolik począwszy od teraźniejszej daty jest rezerwacja
        if($rQuery && $rQuery->num_rows > 0)//jeśli rezerwacja na jakiś stolik jest
		{
            while($obj = $rQuery->fetch_object())
			{
                $reservations[] = $obj->table_id;//do tablicy dodajemy wszystkie id stolików zarezerwowanych
            }
        }
		$result = '<h1>Lista stolików</h1>';
        $result .= '<form method="post" action="'.$path.'/functions/post/bookTable.php"><table>';//tworzymy formularz stolików z i rezerwacji
        $result .= '<thead><tr><th>Nr. stolika</th><th>Liczba os.</th><th>Zarezerwuj</th></tr></thead><tbody>';

        while($obj = $query->fetch_object())//dla każdego stolika z bd
		{
			//jeśli w tablicy zarezerwowanych stolików jest na konkretny rezerwacja to nie dodajemy opcji rezerwacji, dla wolnych taka opcja się pokazuje
            $input = in_array($obj->id, $reservations) ? "Rezerwacja" : '<input type="checkbox" value="'.$obj->id.'" name="reservation[]" /><input type="datetime-local" name="date[]" id="booking" />';
            $result .= '<tr>';
            $result .= '<td>'.$obj->id.'</td>';
            $result .= '<td>'.$obj->number_of_people.'</td>';
            $result .= '<td>'.$input.'</td>';
            $result .= '</tr>';
        }
        $result .= '</tbody></table><input type="submit" value="Zarezerwuj zaznaczone" /></form>';
        return $result;
    }
}

//funckja dodająca rezerwacje do bd
function makeReservation($arrayOfTables, $arrayOfDates, $path)
{
    if(!isset($_SESSION['uid']))
	{
        header('Location: '.$path.'/functions/login.php');
        die();
    }

    $connection = connectToDatabase();//łączenie z bd
    $insertParts = [];//tablica zawierające w każdej komórce wartości jednej rezerwacji
    for ($i = 0; $i <= count($arrayOfTables)-1; $i++)//dla każdej z dodawanych rezerwacji
	{
        $insertParts[] = '("'.$_SESSION['uid'].'","'.$arrayOfTables[$i].'","'.date('Y-m-d H:i:s', strtotime($arrayOfDates[$i])).'")';
    }
    $insertString = implode(',', $insertParts);//łączenie przecinkami wielu wartości by dodać wszystkie zaznaczone przez użytkownika jednocześnie
    $query = mysqli_query($connection, 'INSERT INTO table_reservation (user_id, table_id, reservation_date) VALUES '.$insertString);//dodanie wszystkich rezerwacji do bd
    if(!$query)
	{
        echo getDBQueryError();
        die();
    }
    else
	{
		//komunikat o pomyślnym dodaniu rezerwacji
		getDBQueryErrorJS("Pomyślnie zarezerwowano!", "../..");
    }
    die();
}

//funkcja wyświetlająca rezerwacje uzytkownika wywoływana z widoku myReservations.php
function getMyReservations()
{
    $connection = connectToDatabase();//łączenie z bd
	//zapytanie łączące tabele rezerwacji ze stolikami dla zalogowanego id użytkownika
    $query = mysqli_query($connection, 'SELECT table_reservation.*, tables.id AS tableID FROM `table_reservation` LEFT JOIN `tables` ON table_reservation.table_id = tables.id WHERE `user_id` = '.$_SESSION['uid']);
    if(!$query)
	{
        echo getDBQueryError();
        die();
    }

    if($query->num_rows > 0)//jeśli jakieś rezerwacje są to tworzymy tabelę, w której w kolejnych wierszach je wyświetlamy
	{
        $result = '<h1>Moje rezerwacje</h1><table><thead><tr><th>ID</th><th>Nr stolika</th><th>Data rezerwacji</th><th>Data utwórzenia</th></tr></thead><tbody>';

        while($obj = $query->fetch_object())//dla kojelnych rezerwacji nowy wiersz
		{
            $result .= '<tr>';
            $result .= '<td>'.$obj->id.'</td>';
            $result .= '<td>'.$obj->tableID.'</td>';
            $result .= '<td>'.$obj->reservation_date.'</td>';
            $result .= '<td>'.$obj->cdate.'</td>';
            $result .= '</tr>';
        }

        $result .= '</tbody></table>';
        echo $result;
    }
	else//jeśli nie ma to wypisujemy taką informację
	{
		$result = '<h1>Brak rezerwacji</h1>';
		echo $result;
	}
}

//funkcja wczytująca listę posiłków z pliku i dodające je bo bazy jeśli ich nie ma, lub aktualizująca cene, jeśli dany posiłek już występuje
function loadMealsFromFile()
{
    $filePath = __DIR__.'/../menu/karta_dan.txt';//ścieżka do listy dań (dir zwraca aktualną ścieżkę pliku)
    $file = fopen($filePath, 'r');//otwieramy plik w trybie odczytu
    $arrayOfElements = [];//tablica kolejnych linii z pliku zawierająca dania
    while(!feof($file))//dopóki plik się nie skończy
	{
        $arrayOfElements[] = fgets($file);//kolejno dodajemy do tablicy dania z cenami
    }

    if(!empty($arrayOfElements))//jeśli plik nie jest pusty to łączymy się z bd
	{
        $connection = connectToDatabase();//łączenie z bd
        for($i = 0; $i <= count($arrayOfElements)-1; $i++)//dla każdego elemenu tablicy
		{
            $meal = explode(',', $arrayOfElements[$i]);//rodzielamy nazwę od ceny
			if(isset($meal[0]) && $meal[0] != "" && isset($meal[1]) && $meal[1] != "")//sprawdzamy, czy użytkownik zastosował odpowiedni szablon nazwa,cena
			{
				$checkQuery = mysqli_query($connection, 'SELECT id FROM `meals` WHERE `name` = "'.$meal[0].'"');//sprawdzamy, czy pod nazwą dania już jest takie
				if(!$checkQuery)
				{
					echo mysqli_error($connection);
					die();
				}

				if($checkQuery->num_rows == 0)//jeśli nie ma dania o takiej nazwie to dodajemy do bd
				{
					$query = mysqli_query($connection, 'INSERT INTO `meals` (`name`, `price`) VALUES ("'.$meal[0].'", "'.$meal[1].'")');
					if(!$query)
					{
						echo getDBQueryError();
						die();
					}
				}
				else//jeśli jest danie o takiej nazwie to aktualizujemy informacje o cenie
				{
					while($obj = $checkQuery->fetch_object())
					{
						$query = mysqli_query($connection, 'UPDATE meals SET `price` = "'.$meal[1].'" WHERE id = '.$obj->id);
						if(!$query)
						{
							echo getDBQueryError();
							die();
						}
					}
				}
			}
        }
    }
    return;
}

//funkcja znajdująca rezerwacje użytkownika począwszy od teraźniejszej daty, funkcja potrzebna na rzecz zamawiania potraw do danej rezerwacji
function selectMyFutureReservations()
{
    if(!isset($_SESSION['uid']))
	{
        return null;
    }

    $connection = connectToDatabase();
	//zapytanie łączace tabele rezerwacji stolików ze stolikami dla zalogowanego użytkownika. Wyświetla informacje o rezerwacji w liście ich wyboru dla zamówienia
    $query = mysqli_query($connection, 'SELECT table_reservation.*, tables.number_of_people AS tableNumbersOfPeople FROM `table_reservation` LEFT JOIN `tables` ON table_reservation.table_id = tables.id WHERE `user_id` = '.$_SESSION['uid'].' AND `reservation_date` > "'.date('Y-m-d H:i:s').'"');
    if(!$query)
	{
        echo getDBQueryError();
        die();
    }

    if($query->num_rows > 0)//jeśli użytkownik ma rezerwacje (nie w przeszłości)
	{
		//tworzymy listę wyboru rezerwacji
        $select = '<select name="reservations[]">';
        $select .= '<option value="" disabled selected>Wybierz rezerwacje</option>';

        while($obj = $query->fetch_object())//dodajemy do listy każdą znalezioną rezerwacje użytkownka
		{
            $select .= '<option value="'.$obj->id.'">Stolik '.$obj->tableNumbersOfPeople.' os. ('.$obj->reservation_date.')</option>';
        }

        $select .= '</select>';
        return $select;
    }
    return null;
}

//funkcja wyświetlająca menu potraw wywoływana z widoku menu.php
function getMealList($path)
{
    loadMealsFromFile();//wywołanie funkcji pobierającej i aktualizującej bd z menu z pliku txt

    $connection = connectToDatabase();
    $query = mysqli_query($connection, "SELECT * FROM `meals`");
    if(!$query)
	{
        echo getDBQueryError();
        die();
    }
    else
	{
		//tworzymy formularz z potrawami, będzie on wyświetlał tylko menu dla niezalgowanych i opcje zamówień dla zalogowanych
		$result = '<h1>Menu</h1>';
        $result .= '<form method="post" action="'.$path.'/functions/post/orderMeal.php"><table>';

        $myReservations = selectMyFutureReservations();//przypisujemy listę rezerwacji
        $result .= '<thead><tr><th>Potrawa</th><th>Cena</th>';
        if($myReservations != null)//elementy nagłówka tabeli tylko jak mamy rezerwacje
		{
            $result .= '<th>Zamów</th><th>Rezerwacja</th><th>Sztuk</th></tr>';
        }
        $result .= '</thead><tbody>';

        while($obj = $query->fetch_object())
		{
            $checkbox = null;
            if($myReservations != null)//jeśli mamy rezerwacje to dodajemy komórki z polem wyboru, listą rezerwacji i ilością potrawy do zamówienia
			{
                $checkbox = '<td><input type="checkbox" name="order[]" value="' . $obj->id . '"></td><td>'.$myReservations.'</td><td><input type="number" min="1" step="1" name="qty[]" value="1"  /> </td>';

            }

            $result .= '<tr>';
            $result .= '<td>'.$obj->name.'</td>';
            $result .= '<td>'.number_format($obj->price, 2).' PLN</td>';
            if(isset($_SESSION['uid']))
			{
                $result .= $checkbox;//dodajemy do formularza elementy dodatkowe z powyższego checkboxa jeśli użytkownik jest zalogowany
            }
            $result .= '</tr>';
        }
        $result .= '</tbody></table>';

        if(isset($_SESSION['uid']) && $myReservations != null)
		{
            $result .= '<input type="submit" value="Złóż zamówienie" />';//dodajemy przycisk do wysłania rezerweacji
        }
        $result .= '</form>';

        return $result;
    }
}

//funckja dodająca do bd zamówienia na potrawy i ich pozycje
function orderMeal($orderArray, $reservationsArray, $qtyArray, $path)
{
    if(!isset($_SESSION['uid']))
	{
        header('Location: '.$path.'/login.php');
        die();
    }
	
	$connection = connectToDatabase();//łączenie z bd
	//dla każdej przesłanej zaznaczonej rezerwacji
	for ($i = 0; $i <= count($reservationsArray)-1; $i++)
	{
        $id = 0;//zmienna dzięki której albo stworzymy nowe zamówienie, albo dodamy do istniejącego nowe pozycje
		//zapytanie do bd o zamówienia już istniejące i nieopłacone dla danego id użytkownika na konkretną rezerwację
        $selectQuery = mysqli_query($connection, 'SELECT id FROM `orders` WHERE `user_id` = '.$_SESSION['uid'].' AND `reservation_id` = '.$reservationsArray[$i].' AND `status_id` = 1 AND `is_deleted` = 0');
        if($selectQuery->num_rows > 0)//jeśli jest zamówienie nieopłacone na daną rezerwacje istnieje, to przypiszemy id istniejącej 
		{
            while($obj = $selectQuery->fetch_object())
			{
                $id = $obj->id;
            }
        }
        else//jeśli nie ma zamówienia na daną rezerwacje lub jest opłacone to tworzymy nowe
		{
            $insertString = '('.$_SESSION['uid'].', '.(int) $reservationsArray[$i].', 1)';
            $query = mysqli_query($connection, 'INSERT INTO orders (user_id, reservation_id, status_id) VALUES '.$insertString);
            if($query)
			{
                $id = mysqli_insert_id($connection);//przypisujemy pod id numer id zamówienia by dodać do tabeli pozycji zamówień
            }
        }
        if($id > 0 && isset($orderArray[$i]) && isset($qtyArray[$i]))//dopisanie pozycji zamówień do właśnie stworzonego zamówienia lub już istniejącego wcześniej
		{
            mysqli_query($connection, 'INSERT INTO order_meals (order_id, meal_id, qty) VALUES ('.$id.', '.$orderArray[$i].', '.$qtyArray[$i].')');
        }
    }
    header('Location: '.$path.'/myOrders.php');
    die();
}

//funkcja wyświetlająca zamówienia wywoływana z widoku myOrders.php
function getMyOrders($path)
{
    $connection = connectToDatabase();//łączenie z bd
	//zapytanie, które łączy tabele bd zamówień, ztatusów zamówień (potrzebna nazwa statusu), rezerwacji oraz stolików (informacja o ilości osób przy stoliku)
    $query = mysqli_query($connection, 'SELECT orders.*, order_status.name AS orderStatus, table_reservation.id AS reservationID, table_reservation.reservation_date AS reservationDate, tables.number_of_people AS numberOfPeople FROM `orders`
            LEFT JOIN `order_status` ON orders.status_id = order_status.id
            LEFT JOIN `table_reservation` ON orders.reservation_id = table_reservation.id
            LEFT JOIN `tables` ON table_reservation.table_id = tables.id
            WHERE orders.user_id = '.$_SESSION['uid'].' AND is_deleted = 0');
    if(!$query)
	{
        echo getDBQueryError();
        die();
    }

    if($query->num_rows > 0)//jeśli zamówienia dla danego użytkownika istnieją to tworzymy formularz
	{
        $result = '<form method="POST" action="'.$path.'/post/manageOrders.php"><table><tr><th></th><th>Numer zamówienia</th><th>Stolik</th><th>Data rezerwacji</th><th>Status</th> <th> </th> </tr>';
        while($obj = $query->fetch_object())//dla każdego zamówienia dodajemy wiersz z danymi
		{
            $payDate = $obj->status_id == 2 ? '<small><b>('.$obj->pay_date.')</b></small>' : null;//jeśli zamówienie opłacone to dopiszemy informacje o dacie płatności
            $disabled = $obj->status_id == 1 ? '' : 'disabled';//pole do zaznaczania pozycji które będziemy edytować, jeśli opłacone to niekatywne
            $result .= '<tr>';
            $result .= '<td style="width: 20px"><input type="checkbox" name="order[]" value="'.$obj->id.'" '.$disabled.'/></td>';
            $result .= '<td>'.$obj->id.'</td>';
            $result .= '<td>'.$obj->numberOfPeople.' os</td>';
            $result .= '<td>'.$obj->reservationDate.'</td>';
            $result .= '<td>'.$obj->orderStatus.' '.$payDate.'</td>';
            $result .= '<td><a href="'.$path.'/orderDetails.php?id='.$obj->id.'">Szczegóły</a></td>';//przejście do widoku orderDetails.php z pozycjami zamówienia

            $result .= '</tr>';
        }
        $result .= '</table>';
        $result .= '<ul><li><input type="submit" name="pay" value="Opłać zaznaczone"/></li><li><input type="submit" name="delete" value="Usuń zaznaczone"/></li><li><input type="submit" name="download" value="Pobierz zaznaczone"/></li></ul></form>';
        echo $result;
    }
	else
	{
		$result = '<h1>Brak zamówień</h1>';
		echo $result;
	}
}

//funkcja zarządzająca płatnościami, usuwaniem oraz pobieraniem pliku z zamówieniami
function manageOrders($post, $path)
{
    if(!isset($_SESSION['uid']))
	{
        header('Location: '.$path.'/login.php');
        die();
    }
	
	//tablica pomocnicza zawierająca id
    $orderArray = !empty($post['order']) ? $post['order'] : [];
    $connection = connectToDatabase();
	//obsługa płatności
    if(!empty($orderArray) && isset($post['pay']))
	{
        for ($i = 0; $i <= count($orderArray)-1; $i++)
		{
            $query = mysqli_query($connection, 'UPDATE orders SET status_id = 2, pay_date = "'.date('Y-m-d H:i:s').'" WHERE id = '.$orderArray[$i].' AND user_id = '.$_SESSION['uid']);
        }
    }
	
	//usuwanie - realizujemy to poprzez ustawienie flagi w bd na usunięty, nie niszczymy danych
    elseif (!empty($orderArray) && isset($post['delete']))
	{
        for ($i = 0; $i <= count($orderArray)-1; $i++)
		{
            $query = mysqli_query($connection, 'UPDATE orders SET is_deleted = 1 WHERE id = '.$orderArray[$i].' AND user_id = '.$_SESSION['uid']);
        }
    }
	//pobieranie zamówień do pliku
    elseif (!empty($orderArray) && isset($post['download']))
	{
        $whereInStr = '('.implode(',', $orderArray).')';//do warunku where in (wiele wartości)
        $query = mysqli_query($connection, 'SELECT * FROM orders
                    WHERE id IN '.$whereInStr.' AND user_id = '.$_SESSION['uid']);
        if(!$query)
		{
            echo getDBQueryError();
            die();
        }
        $fileName = 'zamowienie-'.uniqid().'.txt';//nadajemy nazwę dla pliku, uniqid generuje unikalną nazwę
        $directoryPath = __DIR__.'/../pliki_zamowien/';//ścieżka do zapisu
        $file = fopen($directoryPath.$fileName, "w") or die('Brak możliwości utworzenia pliku');//otwiera plik do zapisu danych
        if($file)
		{
            while($obj = $query->fetch_object())
			{
				
				//fwrite($file, var_dump($obj));
                fwrite($file, 'Zamówienie nr: '.$obj->id.'');
                fwrite($file, PHP_EOL);
				fwrite($file, 'Produky:');
				fwrite($file, PHP_EOL);
				//wypisujemy produkty i ich ceny
				$queryPoz = mysqli_query($connection, 'SELECT * , meals.name AS mealName, meals.price AS mealPrice FROM `order_meals` LEFT JOIN meals ON order_meals.meal_id = meals.id WHERE `order_id` = '.$obj->id);
				while($objPoz = $queryPoz->fetch_object())
				{
					fwrite($file, $objPoz-> mealName);
					fwrite($file, ', cena: '.$objPoz-> mealPrice);
					fwrite($file, PHP_EOL);
				}
                fwrite($file, 'Kod weryfikacji: '.uniqid());//generujemy unikalny kod jako symulacja kodu weryfikacji zamówienia
                fwrite($file, PHP_EOL);
                fwrite($file, '----------------------------------------------');
                fwrite($file, PHP_EOL);
            }
            fclose($file);
			
			//pobieramy plik na dysk
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename='.basename($fileName));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($directoryPath.$fileName));
            header("Content-Type: text/plain");
            readfile($directoryPath.$fileName);
        }
        die();
    }
    header('Location: '.$path.'/myOrders.php');
    die();
}

//funkcja wywoływana przez orderDetails.php, która zawiera pozycje zamówienia, które użytkownik może modyfikować
function getOrderDetails($id)
{
    $connection = connectToDatabase();//połączenie z bd
	//zapytanie ,które znajduje zamówienie po przekazanym id
    $query = mysqli_query($connection, 'SELECT * FROM `orders` WHERE `user_id` = '.$_SESSION['uid'].' AND `id` = '.$id);
    if(!$query)
	{
        echo getDBQueryError();
        die();
    }

    if($query->num_rows > 0)//jeśli zamówienie o podanym id istnieje
	{
		//wybieramy złączenie tabel pozycji zamówienia i posiłków, gdzie id zamówienia jest równy podanemu
        $orderItemsSelect = mysqli_query($connection, 'SELECT *, order_meals.id AS idPozycjiZamowienia, meals.name AS mealName, meals.price AS mealPrice FROM `order_meals` LEFT JOIN meals ON order_meals.meal_id = meals.id WHERE `order_id` = '.$id);
        if($orderItemsSelect->num_rows > 0)//tworzenie forzmularza z potrawami, cenami, ilością i sumą wartości pozycji dla danego zamówienia, użytkownik może edytować ilości
		{
			$objD = $query->fetch_object();//do sprawdzania czy zamówienie opłacone
            $result = '<form method="POST" action="./post/manageOrdersDetails.php"><table><tr><th></th><th>Potrawa</th><th>Cena jednostkowa</th><th>Ilość</th><th>Suma</th> <th>Edytuj</th></tr>';
            while($obj = $orderItemsSelect->fetch_object())
			{
                $price = $obj->qty*$obj->mealPrice;//obliczanie wartości pozycji
                $result .= '<tr>';
				$result .= '<td style="width: 20px"><input type="checkbox" name="order[]" value="'.$obj->idPozycjiZamowienia.'"/></td>';
                $result .= '<td>'.$obj->mealName.'</td>';
                $result .= '<td>'.$obj->mealPrice.' zł</td>';
                $result .= '<td> <input type="number" min="0" step="1" name="ilosci[]" id='. $obj->idPozycjiZamowienia .'  disabled value='. $obj->qty .'></td>';//id pola takie jak unikalne id pozycji zamówienia
                $result .= '<td>'.number_format($price, 2).' PLN</td>';
				$disabled = $objD->status_id == 1 ? '' : 'disabled';//edycja nieaktywna jeśli zamówienie jest opłacone
                $result .= '<td><button type="button" '.$disabled.' onclick="getElementById('. $obj->idPozycjiZamowienia.').removeAttribute(\'disabled\')">Edytuj</button></td>';//w polui o konkretnym id odblokowuujemy edycje ilości używając js
                $result .= '</tr>';
            }
            $result .= '</table>';
			$result .= '<ul><li><input type="submit" name="edit" value="Edytuj zaznaczone"/></li></form>';
        
            echo $result;
        }
    }
}

//funkcja do edycji przez użytkownika zamówień nieopłaconych
function manageOrdersDetails($post, $path)
{
	if(!isset($_SESSION['uid']))
	{
        header('Location: '.$path.'/login.php');
        die();
    }
	
	//tablice do których przypisujemy idpozycjizamowienia oraz ilosci zamówionych potraw
    $orderArray = !empty($post['order']) ? $post['order'] : [];
    $qtyArray = !empty($post['ilosci']) ? $post['ilosci'] : [];
    $connection = connectToDatabase();
    if(!empty($orderArray) && isset($post['ilosci']))
	{
        //zmiana ilosci
        for ($i = 0; $i <= count($orderArray)-1; $i++)
		{
            $query = mysqli_query($connection, 'UPDATE order_meals SET qty = "'. $qtyArray[$i] .'" WHERE id = '.$orderArray[$i]);
        }
    }
    header('Location: '.$path.'/myOrders.php');
    die();
}
