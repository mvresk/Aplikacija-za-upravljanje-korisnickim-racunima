<?php
include 'baza.php';
include 'sesija.php';
$veza = new Baza();
$veza->spojiDB();
Sesija::kreirajSesiju();
$upitDohvatiKorisnikID = "SELECT korisnik_id FROM korisnici WHERE korisnicko_ime = '{$_SESSION["korisnik"]}'";
$rezultat = $veza->selectDB($upitDohvatiKorisnikID);
$red = pg_fetch_assoc($rezultat);
$korisnikId = $red['korisnik_id'];
$br = 0;

if (filter_input(INPUT_POST, 'spremi')) {
    $ime = filter_input(INPUT_POST, 'ime');
    $prezime = filter_input(INPUT_POST, 'prezime');
    $korIme = filter_input(INPUT_POST, 'kor_ime');
    $email = filter_input(INPUT_POST, 'emailadresa');
    $datumRođenja = filter_input(INPUT_POST, 'datumRođenja');
    $brojMobitela = filter_input(INPUT_POST, 'brojMobitela');
    $lozinka = filter_input(INPUT_POST, 'lozinka');
    $potvrda_Lozinke = filter_input(INPUT_POST, 'Ponovljenalozinka');

    $reIme = "/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]+$/";
    if (!preg_match($reIme, $ime)) {
        echo "Ime nije ispravno!\n";
        $br++;
    }

    if (!preg_match($reIme, $prezime)) {
        echo "Prezime nije ispravno!\n";
        $br++;
    }

    $reKorIme = '/^(?=.*[a-zčćđšž])(?=.*[A-ZČĆĐŠŽ])(?=.*\d)[a-zA-ZčćđšžČĆĐŠŽ\d]{7,20}$/';
    if (!preg_match($reKorIme, $korIme)) {
        echo "Korisničko ime ne zadovoljava sve specificirane uvjete (barem 1 malo, veliko slovo i broj, najmanje 7, a najviše 20 znakova). Zabranjeno je korištenje specijalnih znakova\n";
        $br++;
    }

    $reEmail = '/[a-zA-Z0-9-ŠšĐđČčĆćŽž]+@[a-zA-Z0-9-ŠšĐđČčĆćŽž]+\.(com$|info$|hr$)/';
    if (!preg_match($reEmail, $email)) {
        echo "Email ne zadovoljava sve potrebne kriterije!\n";
        $br++;
    }

    if (!empty($datumRođenja)) {
        $godina = DateTime::createFromFormat('Y-m-d', $datumRođenja)->format('Y');
        $sadasnjiDatum = date('Y-m-d');
        $trenutnaGodina = date('Y');
        if (($trenutnaGodina - $godina < 10) || ($trenutnaGodina - $godina > 130)) {
            echo "Morate imati najmanje deset godina i ne više od 130!\n";
            $br++;
        }
    } else {
        echo "Morate odabrati datum rođenja!\n";
        $br++;
    }

    $reLozinka = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{7,20}$/';
    if (!preg_match($reLozinka, $lozinka)) {
        echo "Lozinka ne zadovoljava sve specificirane uvjete (barem 1 malo i veliko slovo, 1 broj, najmanje 7, a najviše 20 znakova). Zabranjeno je korištenje specijalnih znakova!\n";
        $br++;
    }

    if ($lozinka !== $potvrda_Lozinke) {
        echo "Lozinke nisu identične\n";
        $br++;
    }



    if ($br == 0) {
        pg_query_params($veza->spojiDB(), 'CREATE EXTENSION IF NOT EXISTS pgcrypto', array());
        $upit = "INSERT INTO korisnici (ime, prezime, korisnicko_ime, email_adresa, datum_rodenja, broj_mobitela,blokiran,lozinka,uloga_id)
         VALUES ($1, $2, $3, $4, $5, $6, $7,crypt($8, gen_salt('md5')), $9)";
        $values = array($ime, $prezime, $korIme, $email, $datumRođenja, $brojMobitela, 'false', $lozinka, 2);

        $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);

        if ($rezultat) {
            echo "Uspješno ste kreirali novog korisnika";
        } else {
            $error_message = pg_last_error($veza->spojiDB());
            $parsed_error_message = substr($error_message, strpos($error_message, "ERROR:") + strlen("ERROR:"), strpos($error_message, "CONTEXT:") - strlen($error_message));
            echo $parsed_error_message;
        }

        $veza->zatvoriDB();
    }

}


if (filter_input(INPUT_POST, 'akcija') === "pregledaj") {
    $korisnikid = filter_input(INPUT_POST, 'id');
    $upit2 = $upit2 = "SELECT ime, prezime, korisnicko_ime, email, datum_rodenja, broj_mobitela, blokiran, naziv_uloge, naziv_posljednje_aktivnosti, 
    to_char(datum_zadnje_aktivnosti, 'YYYY-MM-DD HH24:MI:SS') as datum_zadnje_aktivnosti, broj_grupa
    FROM PogledPregledKorisnika WHERE korisnik_id='" . $korisnikid . "'";

            
    $rezultat2 = $veza->selectDB($upit2);
    $red = pg_fetch_array($rezultat2);
    $rezultat3[] = array(
        "ime" => $red['ime'],
        "prezime" => $red['prezime'],
        "korisnicko_ime" => $red['korisnicko_ime'],
        "email_adresa" => $red['email'],
        "datum_rodenja" => $red['datum_rodenja'],
        "broj_mobitela" => $red['broj_mobitela'],
        "blokiran" => $red['blokiran'],
        "naziv_uloge" => $red['naziv_uloge'],
        "naziv_aktivnosti" => $red['naziv_posljednje_aktivnosti'],
        "datum_zadnjeaktivnosti" => $red['datum_zadnje_aktivnosti'],
        "broj_grupa" => $red['broj_grupa']


    );
    echo json_encode($rezultat3);

}

if (filter_input(INPUT_POST, 'akcija') === "izbriši") {
    $korisnikid = filter_input(INPUT_POST, 'id');
    $upit2 = [
        "DELETE FROM članovi_grupa WHERE korisnik_id = $korisnikid",
        "DELETE FROM Aktivnosti WHERE korisnik_id = $korisnikid",
        "DELETE FROM ZahtjevzaGrupu WHERE korisnik_id = $korisnikid",
        "DELETE FROM poruke WHERE posiljatelj = $korisnikid",
        "DELETE FROM korisnici WHERE korisnik_id = $korisnikid"
    ];
       
    foreach ($upit2 as $upit) {
        $rezultat2 = $veza->updateDB($upit);
    }
    
    
    echo 'Uspješno ste obrisali zapis';



}
if (filter_input(INPUT_POST, 'akcija') === "ažuriraj") {
    $korisnikid = filter_input(INPUT_POST, 'id');

    $upit2 = "SELECT *
            FROM prijavljeniKorisnik
            WHERE korisnik_id='" . $korisnikid . "'";
    $rezultat2 = $veza->selectDB($upit2);
    $red = pg_fetch_array($rezultat2);
    $rezultat3[] = array(
        "ime" => $red['ime'],
        "prezime" => $red['prezime'],
        "korisnicko_ime" => $red['korisnicko_ime'],
        "email_adresa" => $red['email_adresa'],
        "datum_rodenja" => $red['datum_rodenja'],
        "broj_mobitela" => $red['broj_mobitela']

    );
    echo json_encode($rezultat3);
}
if (filter_input(INPUT_POST, 'akcija2') === "spremi") {
    $br = 0;
    $korisnikid = filter_input(INPUT_POST, 'id');
    $ime = filter_input(INPUT_POST, 'imeA');
    $prezime = filter_input(INPUT_POST, 'prezimeA');
    $korime = filter_input(INPUT_POST, 'kor_imeA');
    $email = filter_input(INPUT_POST, 'emailA');
    $datum_rođenja = filter_input(INPUT_POST, 'datumRođenjaA');
    $broj_mobitela = filter_input(INPUT_POST, 'brojMobitelaA');

    $reIme = "/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]+$/";
    if (!preg_match($reIme, $ime)) {
        echo "Ime nije ispravno!\n ";
        $br++;
    }

    if (!preg_match($reIme, $prezime)) {
        echo "Prezime nije ispravno!\n ";
        $br++;
    }

    $reKorIme = '/^(?=.*[a-zčćđšž])(?=.*[A-ZČĆĐŠŽ])(?=.*\d)[a-zA-ZčćđšžČĆĐŠŽ\d]{7,20}$/';
    if (!preg_match($reKorIme, $korime)) {
        echo "Korisničko ime ne zadovoljava sve specificirane uvjete(barem 1 malo, veliko slovo i broj,najmanje 7 ,a najviše 20 znakova.Zabranjeno je korištenje specijalnih znakova )\n";
        $br++;
    }

    $reEmail = '/[a-zA-Z0-9-ŠšĐđČčĆćŽž]+@[a-zA-Z0-9-ŠšĐđČčĆćŽž]+\.(com$|info$|hr$)/';
    if (!preg_match($reEmail, $email)) {
        echo "Email ne zadovoljava sve potrebne kriterije!\n";
        $br++;
    }


    if ($datum_rođenja) {
        $godina = explode('-', $datum_rođenja);
        if (strlen($godina[0]) === 4) {
            $sadasnjiDatum = date('Y-m-d');
            if ($datum_rođenja > $sadasnjiDatum) {
                echo "Odabrali ste neispravni datum.\n";
                $br++;
            } else {
                $rodenje = DateTime::createFromFormat('Y-m-d', $datum_rođenja);
                $danas = new DateTime($sadasnjiDatum);
                $age = $danas->diff($rodenje)->y;
                if ($age < 10 || $age > 130) {
                    echo "Morate imati najmanje deset godina i ne više od 130!\n";
                    $br++;
                }
            }
        }
    }




    $rePhoneNumber = '/^\d{3}-\d{3}-\d{4}$|^\d{3}-\d{3}-\d{3}$/';
    if (!preg_match($rePhoneNumber, $broj_mobitela)) {
        echo "Broj mobitela ne zadovoljava sve potrebne kriterije!";
        $br++;
    }



    $upit2 = "SELECT * FROM korisnici WHERE korisnicko_ime='" . $korime . "' AND korisnik_id != " . $korisnikid;
    $rezultat2 = $veza->selectDB($upit2);
    $num_rows = pg_num_rows($rezultat2);
    if ($num_rows > 0) {
        echo "Već postoji korisnik s unesenim korisničkim imenom";
        $br++;
    }

    $upit3 = "SELECT * FROM korisnici WHERE email_adresa='" . $email . "' AND korisnik_id != " . $korisnikid;
    $rezultat3 = $veza->selectDB($upit3);
    $num_rows = pg_num_rows($rezultat3);
    if ($num_rows > 0) {
        echo "Već postoji korisnik s unesenom email adresom";
        $br++;
    }


    if ($br === 0) {
        $upit = "UPDATE korisnici SET 
   ime = '" . $ime . "',
   prezime = '" . $prezime . "',
   korisnicko_ime = '" . $korime . "',
   email_adresa = '" . $email . "',
   datum_rodenja = '" . $datum_rođenja . "',
   broj_mobitela = '" . $broj_mobitela . "'
   WHERE korisnik_id='" . $korisnikid . "'";
        $rezultat = $veza->updateDB($upit);
        echo 'Uspješno ste ažurirali podatke';
    }
}

if (filter_input(INPUT_POST, 'akcija') === "blokiraj") {
    $korisnikid = filter_input(INPUT_POST, 'id');
    $upit = "UPDATE korisnici SET 
    blokiran = true
    WHERE korisnik_id='" . $korisnikid . "'";
    $rezultat = $veza->updateDB($upit);
    $upitImePrezime = "SELECT ime, prezime FROM korisnici WHERE korisnik_id='" . $korisnikid . "'";
    $rezultatImePrezime = $veza->selectDB($upitImePrezime);
    $red = pg_fetch_assoc($rezultatImePrezime);
    $Ime = $red['ime'];
    $Prezime = $red['prezime'];
    if ($rezultat) {
        $upit = "INSERT INTO Aktivnosti(naziv,opis,korisnik_id) VALUES ($1, $2, $3)";
        $values = array('Blokiranje korisnika', 'Blokiran korisnik  ' . $Ime . ' ' . $Prezime, $korisnikId);
        $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);
        echo 'Korisnik je blokiran';
    }
}

if (filter_input(INPUT_POST, 'akcija') === "odblokiraj") {
    $korisnikid = filter_input(INPUT_POST, 'id');
    $upit = "UPDATE korisnici SET 
    blokiran = false
    WHERE korisnik_id='" . $korisnikid . "'";
    $rezultat = $veza->updateDB($upit);

    $upitImePrezime = "SELECT ime, prezime FROM korisnici WHERE korisnik_id='" . $korisnikid . "'";
    $rezultatImePrezime = $veza->selectDB($upitImePrezime);
    $red = pg_fetch_assoc($rezultatImePrezime);
    $Ime = $red['ime'];
    $Prezime = $red['prezime'];
    if ($rezultat) {
        $upit = "INSERT INTO Aktivnosti(naziv,opis,korisnik_id) VALUES ($1, $2, $3)";
        $values = array('Odblokiranje korisnika', 'Odblokiran je korisnik  ' . $Ime . ' ' . $Prezime, $korisnikId);
        $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);
        echo 'Korisnik je odblokiran';
    }
}

if (filter_input(INPUT_POST, 'akcija') === "pretrazi") {
    $filtriraj = filter_input(INPUT_POST, 'filtriraj');
    ?>
    <table id="tablicaPodaci" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Ime</th>
                <th>Prezime</th>
                <th>Korisničko ime</th>
                <th>Email adresa</th>
                <th>Datum rođenja</th>
                <th>Broj mobitela</th>
                <th>Akcija</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                $upitDohvatiKorisnike = "SELECT * FROM prijavljeniKorisnik WHERE ime LIKE '{$filtriraj}%' OR prezime LIKE '{$filtriraj}%' OR CONCAT(ime, ' ', prezime) LIKE '{$filtriraj}%'";

                $rezultatDohvatiKorisnike = $veza->selectDB($upitDohvatiKorisnike);
                while ($red = pg_fetch_array($rezultatDohvatiKorisnike)) {
                    ?>

                    <td><?= $red['ime'] ?></td>
                    <td><?= $red['prezime'] ?></td>
                    <td><?= $red['korisnicko_ime'] ?></td>
                    <td><?= $red['email_adresa'] ?></td>
                    <td><?= $red['datum_rodenja'] ?></td>
                    <td><?= $red['broj_mobitela'] ?></td>
                    <td>
                        <button type="button" value="<?= $red['korisnik_id'] ?>" class="btn btn-success"
                            id="buttonPregledaj">Pregledaj</button>
                        <button type="button" value="<?= $red['korisnik_id'] ?>" class=" btn btn-warning"
                            id="buttonAžuriraj">Ažuriraj</button>
                        <button type="button" value="<?= $red['korisnik_id'] ?>" class="btn btn-danger"
                            id="buttonIzbriši">Izbriši</button>
                        <?php
                        if ($red['blokiran'] === 'f') {
                            ?>
                            <button type="button" value="<?= $red['korisnik_id'] ?>" class="btn btn-primary"
                                id="buttonBlokiraj">Blokiraj</button>
                        <?php } else {
                            ?>
                            <button type="button" value="<?= $red['korisnik_id'] ?>" class="btn btn-primary"
                                id="buttonOdblokiraj">Odblokiraj</button>
                            <?php
                        } ?>
                    </td>
                </tr>
                <?php
                }
                ?>
        </tbody>
    </table>
<?php }
?>
