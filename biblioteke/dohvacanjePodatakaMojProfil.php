<?php
include 'biblioteke/baza.php';
include 'biblioteke/sesija.php';
Sesija::kreirajSesiju();

if (isset($_SESSION["uloga"])) {
    $poruke = "";
    $br = 0;

    $veza = new Baza();
    $veza->spojiDB();
    $uloga = $_SESSION["uloga"];


    $upitPrijavljeniKorisnik = "SELECT * FROM prijavljeniKorisnik WHERE korisnicko_ime='{$_SESSION["korisnik"]}'";
    $rezultatPrijavljeniKorisnik = $veza->selectDB($upitPrijavljeniKorisnik);
    if ($rezultatPrijavljeniKorisnik !== false) {
        $row = pg_fetch_assoc($rezultatPrijavljeniKorisnik);
    } else {
        $poruke = "Greška pri dohvatu podataka o prijavljenom korisniku.";
    }

    if (filter_input(INPUT_POST, 'azurirajMojProfilButton')) {
        $ime = filter_input(INPUT_POST, 'imeR');
        $prezime = filter_input(INPUT_POST, 'prezimeR');
        $korimeR = filter_input(INPUT_POST, 'korImeR');
        $emailR = filter_input(INPUT_POST, 'emailR');
        $datum_rođenja = filter_input(INPUT_POST, 'datumRođenja');
        $broj_mobitela = filter_input(INPUT_POST, 'brojMobitela');



        $reIme = "/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]+$/";
        if (!preg_match($reIme, $ime)) {
            $poruke .= "Ime nije ispravno! </br>";
            $br++;
        }

        if (!preg_match($reIme, $prezime)) {
            $poruke .= "Prezime nije ispravno! </br>";
            $br++;
        }

        $reKorIme = '/^(?=.*[a-zčćđšž])(?=.*[A-ZČĆĐŠŽ])(?=.*\d)[a-zA-ZčćđšžČĆĐŠŽ\d]{7,20}$/';
        if (!preg_match($reKorIme, $korimeR)) {
            $poruke .= "Korisničko ime ne zadovoljava sve specificirane uvjete(barem 1 malo, veliko slovo i broj,najmanje 7 ,a najviše 20 znakova.Zabranjeno je korištenje specijalnih znakova )</br>";
            $br++;
        }

        $reEmail = '/[a-zA-Z0-9-ŠšĐđČčĆćŽž]+@[a-zA-Z0-9-ŠšĐđČčĆćŽž]+\.(com$|info$|hr$)/';
        if (!preg_match($reEmail, $emailR)) {
            $poruke .= "Email ne zadovoljava sve potrebne kriterije!</br>";
            $br++;
        }


        $godina = explode('-', $datum_rođenja);
        if (filter_input(INPUT_POST, 'datumRođenja') && strlen($godina[0]) === 4) {
            $sadasnjiDatum = date('Y-m-d');
            $rodenje = DateTime::createFromFormat('Y-m-d', $datum_rođenja);
            $danas = DateTime::createFromFormat('Y-m-d', $sadasnjiDatum);
            if (($danas->diff($rodenje)->format("%Y") < 10) || ($danas->diff($rodenje)->format("%Y") > 130)) {
                $poruke .= "Morate imati najmanje deset godina i ne više od 130!</br>";
                $br++;
            }
        }


        $rePhoneNumber = '/^\d{3}-\d{3}-\d{4}$|^\d{3}-\d{3}-\d{3}$/';
        if (!preg_match($rePhoneNumber, $broj_mobitela)) {
            $poruke .= "Broj mobitela ne zadovoljava sve potrebne kriterije!</br>";
            $br++;
        }
        if ($br === 0) {
            $upit = "UPDATE korisnici SET 
                    ime = '{$ime}',
                    prezime = '{$prezime}',
                    korisnicko_ime = '{$korimeR}',
                    email_adresa = '{$emailR}',
                    datum_rodenja = '{$datum_rođenja}',
                    broj_mobitela = '{$broj_mobitela}'
                 WHERE korisnicko_ime='{$_SESSION["korisnik"]}'";

            $rezultat = @$veza->updateDB($upit);

            if ($rezultat) {

                Sesija::kreirajKorisnika($korimeR, $uloga);
                echo '<script>alert("Uspješno ste ažurirali podatke.");';
                echo 'window.location.href = "pocetna.php";</script>';

            } else {
                $error_message = pg_last_error($veza->spojiDB());
                $parsed_error_message = substr($error_message, strpos($error_message, "ERROR:") + strlen("ERROR:"), strpos($error_message, "CONTEXT:") - strlen($error_message));
                $poruke = $parsed_error_message;
            }
        }

    }
}
?>