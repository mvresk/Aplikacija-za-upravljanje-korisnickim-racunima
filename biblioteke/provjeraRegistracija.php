<?php
$poruke = "";
$br = 0;
include 'biblioteke/baza.php';

if (filter_input(INPUT_POST, 'registracijaButton')) {
    $ime = filter_input(INPUT_POST, 'imeR');
    $prezime = filter_input(INPUT_POST, 'prezimeR');
    $korimeR = filter_input(INPUT_POST, 'korImeR');
    $emailR = filter_input(INPUT_POST, 'emailR');
    $datum_rođenja = filter_input(INPUT_POST, 'datumRođenja');
    $lozinkaR = filter_input(INPUT_POST, 'lozinkaR');
    $potvrda_Lozinke = filter_input(INPUT_POST, 'potvrdaLozinkeR');

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
    } else {
        $poruke .= "Morate odabrati datum rođenja!</br>";
        $br++;
    }

    $reLozinka = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{7,20}$/';
    if (!preg_match($reLozinka, $lozinkaR)) {
        $poruke .= "Lozinka ne zadovoljava sve specificirane uvjete(barem 1 malo i veliko slovo,1 broj ,najmanje 7 ,a najviše 20 znakova.Zabranjeno je korištenje specijalnih znakova)!</br>";
        $br++;
    }

    if ($lozinkaR !== $potvrda_Lozinke) {
        $poruke .= "Lozinke nisu identične!</br>";
        $br++;
    }


    if ($br === 0) {
        $veza = new Baza();
        $veza->spojiDB();


        pg_query_params($veza->spojiDB(), 'CREATE EXTENSION IF NOT EXISTS pgcrypto', array());
        $upit = "INSERT INTO korisnici (ime, prezime, korisnicko_ime, email_adresa, datum_rodenja,blokiran,lozinka,uloga_id)
         VALUES ($1, $2, $3, $4, $5, $6, crypt($7, gen_salt('md5')), $8)";
        $values = array($ime, $prezime, $korimeR, $emailR, $datum_rođenja, 'false', $lozinkaR, 2);

        $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);

        if ($rezultat) {
            header("Location: index.php");
        } else {
            $error_message = pg_last_error($veza->spojiDB());
            $parsed_error_message = substr($error_message, strpos($error_message, "ERROR:") + strlen("ERROR:"), strpos($error_message, "CONTEXT:") - strlen($error_message));
            $poruke = $parsed_error_message;
        }

        $veza->zatvoriDB();

    }
}
?>

