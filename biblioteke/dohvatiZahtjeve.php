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
    $zahtjevid=filter_input(INPUT_POST, 'id');
     $upit2 = "SELECT *, to_char(datum_kreiranja_zahtjeva, 'YYYY-MM-DD HH24:MI:SS') as datum_kreiranja_zahtjeva
            FROM KorisniciGrupeZahtjevi
            WHERE zahtjev_id='" . $zahtjevid . "'";
    $rezultat2 = $veza->selectDB($upit2);
    $red = pg_fetch_array($rezultat2);
    $rezultat3[] = array(
        "ime" => $red['ime'],
        "prezime" => $red['prezime'],
        "email_adresa" => $red['email_adresa'],
        "broj_mobitela" => $red['broj_mobitela'],
        "naziv_grupe" => $red['naziv_grupe'],
        "opis_grupe" => $red['opis_grupe'],
        "datum_kreiranja_zahtjeva" => $red['datum_kreiranja_zahtjeva'],
        "aktivan" => $red['aktivan']

    );
   echo json_encode($rezultat3);
    
}

if (filter_input(INPUT_POST, 'akcija') === "odbij") {
    $zahtjevid=filter_input(INPUT_POST, 'id');
    $upit4 = "UPDATE ZahtjevzaGrupu SET aktivan='ne' WHERE zahtjev_id='" . $zahtjevid . "'";
     $rezultat4 = $veza->updateDB($upit4);
     echo 'Odbili ste zahtjev za ulazak u grupu.';
     $upitKZ = "SELECT * FROM KorisniciGrupeZahtjevi WHERE zahtjev_id='" . $zahtjevid . "'";
     $rezultatKZ = $veza->selectDB($upitKZ);
     if ($redKZ = pg_fetch_assoc($rezultatKZ)) {
     $Ime=$redKZ['ime'];
     $Prezime=$redKZ['prezime'];
        $upitAktivnost = "INSERT INTO Aktivnosti(naziv,opis,korisnik_id) VALUES ($1, $2, $3)";
        $values = array('Odbijanje/prihvačanje zahtjeva','Odbijeni zahtjev  od ' . $Ime . ' ' . $Prezime .' za ulazak u grupu',$korisnikId);
        $rezultat = @pg_query_params($veza->spojiDB(), $upitAktivnost, $values);
     }
}

if (filter_input(INPUT_POST, 'akcija') === "prihvati") {
    $zahtjevid=filter_input(INPUT_POST, 'id');
    $datum = date('Y-m-d H:i:s');
    $upit = "SELECT * FROM ZahtjevzaGrupu WHERE zahtjev_id='" . $zahtjevid . "'";
    $rezultat = $veza->selectDB($upit);
    if ($red = pg_fetch_assoc($rezultat)) {
        $korisnik_id=$red['korisnik_id'];
        $grupa_id=$red['grupa_id'];
        $upit2 = "UPDATE ZahtjevzaGrupu SET aktivan='ne' WHERE zahtjev_id='" . $zahtjevid . "'";
        $rezultat2 = $veza->updateDB($upit2);
        $upit3 = "INSERT INTO članovi_Grupa ( grupa_id, korisnik_id,član_od) VALUES ($1, $2,$3)";
        $values = array($grupa_id,$korisnik_id,$datum);
        $rezultat3 = @pg_query_params($veza->spojiDB(), $upit3, $values);
        echo 'Prihvatili ste zahtjev za ulazak u grupu.';
        $upitKZ = "SELECT * FROM KorisniciGrupeZahtjevi WHERE zahtjev_id='" . $zahtjevid . "'";
     $rezultatKZ = $veza->selectDB($upitKZ);
     if ($redKZ = pg_fetch_assoc($rezultatKZ)) {
     $Ime=$redKZ['ime'];
     $Prezime=$redKZ['prezime'];
        $upitAktivnost = "INSERT INTO Aktivnosti(naziv,opis,korisnik_id) VALUES ($1, $2, $3)";
        $values = array('Odbijanje/prihvačanje zahtjeva','Prihvačeni zahtjev  od ' . $Ime . ' ' . $Prezime .' za ulazak u grupu',$korisnikId);
        $rezultat = @pg_query_params($veza->spojiDB(), $upitAktivnost, $values);
     }
    }
}


if (filter_input(INPUT_POST, 'akcija') === "sortiraj") {

  $nazivGrupe = filter_input(INPUT_POST, 'nazivGrupe');
  $upitNazivGrupe = "";
  if (!empty($nazivGrupe) && $nazivGrupe != -1) {
    $upitNazivGrupe = " WHERE naziv_grupe = (SELECT naziv_grupe FROM grupa WHERE grupa_id = " . $nazivGrupe . ")";
  }
      $vrijednost = filter_input(INPUT_POST, 'vrijednost');
      ?>
      <table id="tablicaPodaci" class="table table-bordered table-striped">
          <thead>
              <tr>
                  <th>Ime</th>
                  <th>Prezime</th>
                  <th>E-mail adresa</th>  
                  <th>Naziv grupe</th> 
                  <th>Datum kreiranja zahtjeva</th> 
                  <th>Akcija</th>
              </tr>
          </thead>
          <tbody>
          <?php
          $upitDohvatiZahtjeve = "SELECT * FROM KorisniciGrupeZahtjevi" . $upitNazivGrupe;
          if ($vrijednost === '0') {
              $upitDohvatiZahtjeve .= " ORDER BY datum_kreiranja_zahtjeva ASC";
          } elseif ($vrijednost === '1') {
              $upitDohvatiZahtjeve .= " ORDER BY datum_kreiranja_zahtjeva DESC";
          }
          $rezultatDohvatiZahtjeve = $veza->selectDB($upitDohvatiZahtjeve);
          while ($red = pg_fetch_array($rezultatDohvatiZahtjeve)){
              ?>
              <tr>
                  <td><?=$red['ime']?></td>
                  <td><?=$red['prezime']?></td>
                  <td><?=$red['email_adresa']?></td>
                  <td><?=$red['naziv_grupe']?></td>
                  <td><?=$red['datum_kreiranja_zahtjeva']?></td>
                  <td>
                      <button type="button" value="<?=$red['zahtjev_id']?>" class="btn btn-success" id="buttonPregledaj">Pregledaj</button>
                      <?php if($red['aktivan']==='da'){ ?>
                          <button type="button" value="<?=$red['zahtjev_id']?>" class="btn btn-warning" id="buttonPrihvati">Prihvati</button>
                          <button type="button" value="<?=$red['zahtjev_id']?>" class="btn btn-danger" id="buttonOdbij">Odbij</button>
                      <?php } ?>
                  </td>
              </tr>
              <?php
          }
          ?>
          </tbody>
      </table>
      <?php
  }

