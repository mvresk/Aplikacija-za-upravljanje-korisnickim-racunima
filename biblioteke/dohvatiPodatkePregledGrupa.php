<?php
include 'baza.php';
include 'sesija.php';
$veza = new Baza();
$veza->spojiDB();
Sesija::kreirajSesiju();
if (filter_input(INPUT_POST, 'akcija') === "pregledaj") {
  $grupaid = filter_input(INPUT_POST, 'id');
  $upit2 = "SELECT *, to_char(datum_kreiranja, 'YYYY-MM-DD HH24:MI:SS') as datum_kreiranja
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

if (filter_input(INPUT_POST, 'akcija') === "pošaljiZahtjev") {
  $grupaid = filter_input(INPUT_POST, 'id');
  $upitNazivGrupe = "SELECT naziv_grupe FROM grupa WHERE grupa_id= $grupaid";
  $rezultatNazivGrupe = $veza->selectDB($upitNazivGrupe);
  if ($redNG = pg_fetch_assoc($rezultatNazivGrupe)) {
    $nazivGrupe = $redNG['naziv_grupe'];
    $upitDohvatiKorisnikID = "SELECT korisnik_id FROM korisnici WHERE korisnicko_ime = '{$_SESSION["korisnik"]}'";
    $result = $veza->selectDB($upitDohvatiKorisnikID);

    if ($row = pg_fetch_assoc($result)) {
      $korisnik_id = $row['korisnik_id'];
      $upit = "INSERT INTO ZahtjevzaGrupu (korisnik_id, grupa_id, aktivan)
         VALUES ($1, $2, $3)";
      $values = array($korisnik_id, $grupaid, 'da');

      $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);

      if ($rezultat) {
        $upitDohvatiKorisnika = "SELECT * FROM korisnici WHERE korisnicko_ime = '{$_SESSION["korisnik"]}'";
        $rezultatDohvatiKorisnika = $veza->selectDB($upitDohvatiKorisnika);
        if ($red = pg_fetch_assoc($rezultatDohvatiKorisnika)) {
          $Ime = $red['ime'];
          $Prezime = $red['prezime'];
          $upitAktivnost = "INSERT INTO Aktivnosti(naziv,opis,korisnik_id) VALUES ($1, $2, $3)";
          $values = array('Slanje zahtjeva za primanje u grupu', 'Poslan zahtjev  od ' . $Ime . ' ' . $Prezime . ' za ulazak u grupu ' . $nazivGrupe, $korisnik_id);
          $rezultat = @pg_query_params($veza->spojiDB(), $upitAktivnost, $values);
        }
      }
      echo 'Uspješno ste kreirali zahtjev!';

    } else {
      $error_message = pg_last_error($veza->spojiDB());
      $parsed_error_message = substr($error_message, strpos($error_message, "ERROR:") + strlen("ERROR:"), strpos($error_message, "CONTEXT:") - strlen($error_message));
      echo $parsed_error_message;
    }

    $veza->zatvoriDB();
  }

    
}

if (filter_input(INPUT_POST, 'akcija') === "pretrazi") {
    $filtriraj=filter_input(INPUT_POST, 'filtriraj');
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

                                    $upitDohvatiGrupe = "SELECT DISTINCT ON (g.grupa_id) g.grupa_id, to_char(datum_kreiranja, 'YYYY-MM-DD HH24:MI:SS') as datum_kreiranja, g.naziv_grupe, g.opis_grupe, g.status, k.korisnicko_ime
                                    FROM PogledGrupeKategorije g
                                    LEFT JOIN članovi_grupa cg ON g.grupa_id = cg.grupa_id AND cg.korisnik_id = '{$korisnik_id}'
                                    LEFT JOIN korisnici k ON g.kreirao = k.korisnik_id
                                    WHERE cg.korisnik_id IS NULL
                                    AND (g.naziv_grupe LIKE '{$filtriraj}%' OR g.naziv_kategorije LIKE '{$filtriraj}%')
                                ";
                                
                                $rezultatDohvatiGrupe = $veza->selectDB($upitDohvatiGrupe);
                                  while ($red = pg_fetch_array($rezultatDohvatiGrupe)){
                        
                                ?>
                                <tr>
                                  <td><?=$red['naziv_grupe']?></td>
                                  <td><?=$red['opis_grupe']?></td>
                                  <td><?=$red['datum_kreiranja']?></td>

                                  <td>
                                  <button type="button" value="<?=$red['grupa_id']?>" class="btn btn-success" id="buttonPregledaj">Pregledaj</button>
                                  <button type="button" value="<?=$red['grupa_id']?>" class="btn btn-warning" id="buttonPošaljiZahtjev">Pošalji zahtjev</button>
                                </td>
                                </tr>
                                <?php
                                }
                            }
                                ?>
   </tbody>
 </table>
<?php    }
?>

