<?php
include 'sesija.php';
include 'baza.php';
$veza = new Baza();
$veza->spojiDB();
Sesija::kreirajSesiju();
session_start();
$upitDohvatiKorisnikID = "SELECT korisnik_id FROM korisnici WHERE korisnicko_ime = '{$_SESSION["korisnik"]}'";
$rezultat = $veza->selectDB($upitDohvatiKorisnikID);
$red = pg_fetch_assoc($rezultat);
$korisnikId = $red['korisnik_id'];
unset($_SESSION["korisnik"]);
unset($_SESSION["uloga"]);
session_unset();
session_destroy();
$upit = "INSERT INTO Aktivnosti(naziv,opis,korisnik_id) VALUES ($1, $2, $3)";
$values = array('Odjava iz sustava', 'Uspješna odjava', $korisnikId);
$rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);
header('Location: ../index.php');
exit();
?>