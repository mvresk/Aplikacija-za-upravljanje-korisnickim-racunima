<?php
include 'biblioteke/baza.php';
include 'biblioteke/sesija.php';

$autentificiran = false;
$poruke = "";

$veza = new Baza();
$veza->spojiDB();

if (filter_input(INPUT_POST, 'prijavaButton')) {
    $kor_ime = filter_input(INPUT_POST, 'emailP');
    $lozinka = filter_input(INPUT_POST, 'lozinkaP');

    $uloga = 4; 

    $upitPostojiKorIme = "SELECT * FROM korisnici WHERE korisnicko_ime='" . pg_escape_string($kor_ime) . "'";
    $rezultatPostojiKorIme = $veza->selectDB($upitPostojiKorIme);

    if ($rezultatPostojiKorIme !== false) {

        $num_rows = pg_num_rows($rezultatPostojiKorIme);
        if ($num_rows == 0) {
            $poruke = "Korisnik s unesenim korisničkim imenom ne postoji!";
        } else {
            $korisnik = pg_fetch_assoc($rezultatPostojiKorIme);
            $uloga = $korisnik["uloga_id"];
            if ($korisnik['blokiran'] == 't') {
                $poruke = "Blokiran korisnički račun!";
            } else {
                $upitTocnaLozinkaiKorIme = "SELECT * FROM verify($1,$2)";
                $rezTocnaLozinkaiKorIme = pg_query_params($veza->spojiDB(), $upitTocnaLozinkaiKorIme, array($kor_ime, $lozinka));

                if ($rezTocnaLozinkaiKorIme) {
                    $rezultatTocnaLozinkaiKorIme = pg_fetch_object($rezTocnaLozinkaiKorIme);
                    if ($rezultatTocnaLozinkaiKorIme) {
                        $autentificiran = $rezultatTocnaLozinkaiKorIme->verify == 1;
                    }
                }

                if (!$autentificiran) {
                    $poruke = "Unjeli ste pogrešnu lozinku za navedeno korisničko ime!";
                }
            }
        }
    }

    if ($autentificiran && $korisnik->blokiran != 'true') {
        Sesija::kreirajKorisnika($kor_ime, $uloga);
        $upitDohvatiKorisnikID = "SELECT korisnik_id FROM korisnici WHERE korisnicko_ime = '{$_SESSION["korisnik"]}'";
        $rezultat = $veza->selectDB($upitDohvatiKorisnikID);
        if ($red = pg_fetch_assoc($rezultat)) {
            $korisnikId = $red['korisnik_id'];
            $upit = "INSERT INTO Aktivnosti(naziv,opis,korisnik_id) VALUES ($1, $2, $3)";
            $values = array('Prijava u sustav', 'Uspješna prijava', $korisnikId);
            $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);
        }
        header('location:pocetna.php');
    }

}
$veza->zatvoriDB();
?>
