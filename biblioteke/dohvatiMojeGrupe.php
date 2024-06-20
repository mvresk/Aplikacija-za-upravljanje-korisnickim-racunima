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

if (filter_input(INPUT_POST, 'akcija') === "pregledaj") {
    $grupaid = filter_input(INPUT_POST, 'id');
    $upit2 = "SELECT naziv_grupe, opis_grupe, to_char(datum_kreiranja, 'YYYY-MM-DD HH24:MI:SS') as datum_kreiranja, broj_članova, ime, prezime, email_adresa
          FROM GrupaVlasnik
          WHERE grupa_id='" . $grupaid . "'";
    $rezultat2 = $veza->selectDB($upit2);
    $red = pg_fetch_array($rezultat2);
    $rezultat3[] = array(
        "naziv_grupe" => $red['naziv_grupe'],
        "opis_grupe" => $red['opis_grupe'],
        "datum_kreiranja" => $red['datum_kreiranja'],
        "broj_članova" => $red['broj_članova'],
        "ime" => $red['ime'],
        "prezime" => $red['prezime'],
        "email_adresa" => $red['email_adresa']

    );
    echo json_encode($rezultat3);

}

if (filter_input(INPUT_POST, 'akcija') === "spremi") {

    $grupaid = filter_input(INPUT_POST, 'id');
    $nazivGrupe = filter_input(INPUT_POST, 'nazivGrupe');
    $opisGrupe = filter_input(INPUT_POST, 'opisGrupe');
    $upitDohvatiGrupe = "SELECT * FROM grupa WHERE naziv_grupe = '$nazivGrupe'";
    $rezultatDohvatiGrupe = $veza->selectDB($upitDohvatiGrupe);

    if ($rezultatDohvatiGrupe !== false) {
        $num_rows = pg_num_rows($rezultatDohvatiGrupe);
        if ($num_rows == 0 && strlen($nazivGrupe) >= 5) {
            $upit = "UPDATE grupa SET naziv_grupe = '$nazivGrupe', opis_grupe = '$opisGrupe' WHERE grupa_id = '$grupaid'";
            $rezultat = $veza->updateDB($upit);
            echo 'Uspješno ste promjenili naziv grupe.';
        }
        if (strlen($nazivGrupe) < 5) {
            echo 'Naziv grupe mora imati barem 5 znakova.';
        }
        if ($num_rows != 0) {
            echo 'Već postoji grupa s tim nazivom.';
        }
    }

}

if (filter_input(INPUT_POST, 'akcija') === "dodaj") {
    $nazivGrupe = filter_input(INPUT_POST, 'nazivGrupe');
    $opisGrupe = filter_input(INPUT_POST, 'opisGrupe');
    $datum = date('Y-m-d H:i:s');
    $upitDohvatiKorisnikID = "SELECT * FROM korisnici WHERE korisnicko_ime = '{$_SESSION["korisnik"]}'";
    $result = $veza->selectDB($upitDohvatiKorisnikID);

    if ($row = pg_fetch_assoc($result)) {
        $korisnik_id = $row['korisnik_id'];
        $upit = "INSERT INTO grupa (naziv_grupe,opis_grupe,datum_kreiranja,status,kreirao)
         VALUES ($1, $2, $3, $4, $5)";
        $values = array($nazivGrupe, $opisGrupe, $datum, 'da', $korisnik_id);
        $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);

        if ($rezultat) {
            
            $Ime=$row['ime'];
            $Prezime=$row['prezime'];
            $upit = "INSERT INTO Aktivnosti(naziv,opis,korisnik_id) VALUES ($1, $2, $3)";
            $values = array('Kreirana nova grupa', 'Korisnik ' . $Ime . ' ' . $Prezime . ' je kreirao grupu ' . $nazivGrupe, $korisnikId);
            $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);
            echo 'Uspješno ste dodali novu grupu.';
        } else {
            $error_message = pg_last_error($veza->spojiDB());
            $parsed_error_message = substr($error_message, strpos($error_message, "ERROR:") + strlen("ERROR:"), strpos($error_message, "CONTEXT:") - strlen($error_message));
            echo $parsed_error_message;
        }
    }

}



if (filter_input(INPUT_POST, 'akcija') === "izbriši") {
    $id = filter_input(INPUT_POST, 'id');
    $upitDohvatiKorisnikID = "SELECT * FROM korisnici WHERE korisnicko_ime = '{$_SESSION["korisnik"]}'";
    $result = $veza->selectDB($upitDohvatiKorisnikID);
    if ($row = pg_fetch_assoc($result)) {
        $Ime=$row['ime'];
        $Prezime=$row['prezime'];
        $upitDohvatiNazivGrupe = "SELECT * FROM grupa WHERE grupa_id=$id";
        $rezultatDohvatiNazivGrupe= $veza->selectDB($upitDohvatiNazivGrupe);
        if($red= pg_fetch_assoc($rezultatDohvatiNazivGrupe)){
        $nazivGrupe=$red['naziv_grupe'];
        $upit = "INSERT INTO Aktivnosti(naziv,opis,korisnik_id) VALUES ($1, $2, $3)";
        $values = array('Obrisana grupa', 'Korisnik ' . $Ime . ' ' . $Prezime . ' je obrisao grupu ' . $nazivGrupe, $korisnikId);
        $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);
    }
    }

    $upit = "DELETE FROM ZahtjevzaGrupu WHERE grupa_id = '" . $id . "'";
    $upit2 = "DELETE FROM GrupaKategorija WHERE grupa_id = '" . $id . "'";
    $upit3 = "DELETE FROM članovi_grupa WHERE grupa_id = '" . $id . "'";
    $upit4 = "DELETE FROM grupa WHERE grupa_id = '" . $id . "'";
    $rezultat = $veza->selectDB($upit);
    $rezultat2 = $veza->selectDB($upit2);
    $rezultat3 = $veza->selectDB($upit3);
    $rezultat4 = $veza->selectDB($upit4);

    
    echo "Uspješno ste obrisali grupu.";
}



if (filter_input(INPUT_POST, 'akcija') === "dodajKategoriju") {
    $nazivKategorije = filter_input(INPUT_POST, 'nazivKategorije');
    $opisKategorije = filter_input(INPUT_POST, 'opisKategorije');
    $upit = "INSERT INTO kategorija (naziv_kategorije,opis_kategorije)
         VALUES ($1, $2)";
    $values = array($nazivKategorije, $opisKategorije);
    $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);

    if ($rezultat) {
        echo 'Uspješno ste dodali novu kategoriju.';
    } else {
        $error_message = pg_last_error($veza->spojiDB());
        $parsed_error_message = substr($error_message, strpos($error_message, "ERROR:") + strlen("ERROR:"), strpos($error_message, "CONTEXT:") - strlen($error_message));
        echo $parsed_error_message;
    }

}

if (filter_input(INPUT_POST, 'akcija') === "dohvatiKategorije") {
    $id = filter_input(INPUT_POST, 'grupaId');
    $upitDohvatiKategorije = "SELECT naziv_kategorije, kategorija_id
    FROM kategorija
    WHERE kategorija_id NOT IN (
        SELECT kategorija_id
        FROM GrupaKategorija
        WHERE grupa_id = $id
    )";
    $kategorije = $veza->selectDB($upitDohvatiKategorije);

    $options = '';
    if ($kategorije) {
        while ($red = pg_fetch_array($kategorije)) {
            $options .= '<option value="' . htmlspecialchars($red['kategorija_id']) . '">' . htmlspecialchars($red['naziv_kategorije']) . '</option>';
        }
    }
    echo $options;
}


if (filter_input(INPUT_POST, 'akcija') === "spremiDKG") {
    $grupaId = filter_input(INPUT_POST, 'grupaId');
    $kategorijaId = filter_input(INPUT_POST, 'kategorijaId');
    $datum = date('Y-m-d H:i:s');
    $upit = "INSERT INTO GrupaKategorija ( grupa_id, kategorija_id,datum_pridruživanja) VALUES ($1, $2,$3)";
    $values = array($grupaId, $kategorijaId, $datum);
    $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);
    if ($rezultat) {
        echo 'Dodjelili ste novu kategoriju ovoj grupi.';
    }
}

if (filter_input(INPUT_POST, 'akcija') === "pretrazi") {
    $filtriraj = filter_input(INPUT_POST, 'filtriraj');
    ?>
    <table id="tablicaPodaci" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Naziv grupe</th>
                <th>Opis grupe</th>
                <th>Datum kreiranja</th>
                <th>Akcija</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $upitDohvatiKorisnikID = "SELECT korisnik_id FROM korisnici WHERE korisnicko_ime = '{$_SESSION["korisnik"]}'";
            $result = $veza->selectDB($upitDohvatiKorisnikID);

            if ($row = pg_fetch_assoc($result)) {
                $korisnik_id = $row['korisnik_id'];

                $upitDohvatiGrupe = "SELECT DISTINCT ON (g.grupa_id) g.grupa_id, g.naziv_grupe, g.opis_grupe, to_char(g.datum_kreiranja, 'YYYY-MM-DD HH24:MI:SS') as datum_kreiranja , g.status, k.korisnicko_ime
                     FROM PogledGrupeKategorije g
                     LEFT JOIN članovi_grupa cg ON g.grupa_id = cg.grupa_id
                     LEFT JOIN korisnici k ON g.kreirao = k.korisnik_id
                     WHERE cg.korisnik_id = '{$korisnik_id}'
                     AND (g.naziv_grupe LIKE '{$filtriraj}%' OR g.naziv_kategorije LIKE '{$filtriraj}%')";

        
                $rezultatDohvatiGrupe = $veza->selectDB($upitDohvatiGrupe);
                while ($red = pg_fetch_array($rezultatDohvatiGrupe)) {

                    ?>
                    <tr>
                        <td><?= $red['naziv_grupe'] ?></td>
                        <td><?= $red['opis_grupe'] ?></td>
                        <td><?= $red['datum_kreiranja'] ?></td>

                        <td>
                            <button type="button" value="<?= $red['grupa_id'] ?>" class="btn btn-success"
                                id="buttonPregledaj">Pregledaj</button>
                            <button type="button" value="<?= $red['grupa_id'] ?>" class="btn btn-warning"
                                id="buttonAžuriraj">Ažuriraj</button>
                            <button type="button" value="<?= $red['grupa_id'] ?>" class="btn btn-danger"
                                id="buttonIzbriši">Izbriši</button>
                            <button type="button" value="<?= $red['grupa_id'] ?>" class="btn btn-info"
                                id="buttonDodjeliKategoriju">Dodjeli kategoriju</button>
                            <button type="button" value="<?= $red['grupa_id'] ?>" class="btn btn-light"
                                id="buttonPregledPoruka">Pregledaj poruke</button>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
<?php }

?>