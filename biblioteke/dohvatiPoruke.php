<?php
include 'baza.php';
include 'sesija.php';
$veza = new Baza();
$veza->spojiDB();
Sesija::kreirajSesiju();
if (filter_input(INPUT_POST, 'akcija') === "dodajPoruku") {
    $grupaId = filter_input(INPUT_POST, 'grupaId');
    $naslovPoruke = filter_input(INPUT_POST, 'naslovPoruke');
    $sadržajPoruke = filter_input(INPUT_POST, 'sadrzajPoruke');
    $datum = date('Y-m-d H:i:s');
    $upitDohvatiKorisnikID = "SELECT korisnik_id FROM korisnici WHERE korisnicko_ime = '{$_SESSION["korisnik"]}'";
    $result = $veza->selectDB($upitDohvatiKorisnikID);

    if ($row = pg_fetch_assoc($result)) {
        $korisnik_id = $row['korisnik_id'];
        $upit = "INSERT INTO poruke (naslov, sadrzaj, vrijeme, posiljatelj, grupa_id)  VALUES ($1, $2, $3, $4, $5)";
        $values = array($naslovPoruke, $sadržajPoruke, $datum, $korisnik_id, $grupaId);
        $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);

        if ($rezultat) {
            echo 'Poslali ste novu poruku.';
        } else {
            $error_message = pg_last_error($veza->spojiDB());
            $parsed_error_message = substr($error_message, strpos($error_message, "ERROR:") + strlen("ERROR:"), strpos($error_message, "CONTEXT:") - strlen($error_message));
            echo $parsed_error_message;
        }
    }

}
if (filter_input(INPUT_POST, 'akcija') === "obrišiPoruku") {
    $porukeId = filter_input(INPUT_POST, 'porukeId');
    $upit = "DELETE FROM poruke WHERE poruka_id = '" . $porukeId . "'";
    $rezultat = $veza->selectDB($upit);
    if ($rezultat) {
        echo "Uspješno ste obrisali grupu.";
    }

}

?>