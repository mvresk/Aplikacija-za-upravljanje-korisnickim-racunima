<?php
include 'baza.php';
include 'sesija.php';
Sesija::kreirajSesiju();
$br = 0;
$veza = new Baza();
$veza->spojiDB();


if (filter_input(INPUT_POST, 'akcija') === "pregledaj") {
  $korisnikid = filter_input(INPUT_POST, 'id');
  $upit2 = "SELECT *
            FROM PogledPregledKorisnika
            WHERE korisnik_id='" . $korisnikid . "'";
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
    "broj_grupa" => $red['broj_grupa']
  );
  echo json_encode($rezultat3);

}

if (filter_input(INPUT_POST, 'akcija') === "spremi") {
  $korisnikid = filter_input(INPUT_POST, 'id');
  $uloga_id = filter_input(INPUT_POST, 'vrsta');
  $nazivUloge = filter_input(INPUT_POST, 'nazivUloge');

  $upitPrijavljeniKorisnik = "SELECT * FROM korisnici WHERE korisnicko_ime='{$_SESSION["korisnik"]}'";
  $rezultatPrijavljeniKorisnik = $veza->selectDB($upitPrijavljeniKorisnik);
  $red = pg_fetch_assoc($rezultatPrijavljeniKorisnik);
  $administratorId = $red['korisnik_id'];

  $upitImePrezime = "SELECT ime, prezime FROM korisnici WHERE korisnik_id='" . $korisnikid . "'";
  $rezultatImePrezime = $veza->selectDB($upitImePrezime);
  $red = pg_fetch_assoc($rezultatImePrezime);
  $Ime = $red['ime'];
  $Prezime = $red['prezime'];

  if ($uloga_id != -1) {
    $upit = "UPDATE korisnici SET 
   uloga_id = '" . $uloga_id . "'
   WHERE korisnik_id='" . $korisnikid . "'";
    $rezultat = $veza->updateDB($upit);
  }
  if ($rezultat) {

    $upit = "INSERT INTO Aktivnosti(naziv,opis,korisnik_id) VALUES ($1, $2, $3)";
    $values = array('Promjena uloge', 'Promjena uloge korisniku ' . $Ime . ' ' . $Prezime . ' u ' . $nazivUloge, $administratorId);
    $rezultat = @pg_query_params($veza->spojiDB(), $upit, $values);
    echo 'Uspješno ste promjenili ulogu';
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
        <th>Uloga</th>
        <th>Akcija</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php

        $upitDohvatiKorisnike = "SELECT * FROM KorisnikUloga WHERE naziv_uloge LIKE '{$filtriraj}%'";
        $rezultatDohvatiKorisnike = $veza->selectDB($upitDohvatiKorisnike);
        while ($red = pg_fetch_array($rezultatDohvatiKorisnike)) {
          ?>
        <tr>
          <td><?= $red['ime'] ?></td>
          <td><?= $red['prezime'] ?></td>
          <td><?= $red['korisnicko_ime'] ?></td>
          <td><?= $red['email_adresa'] ?></td>
          <td><?= $red['datum_rodenja'] ?></td>
          <td><?= $red['broj_mobitela'] ?></td>
          <td><?= $red['naziv_uloge'] ?></td>
          <td>
            <button type="button" value="<?= $red['korisnik_id'] ?>" class="btn btn-success"
              id="buttonPregledaj">Pregledaj</button>
            <button type="button" value="<?= $red['korisnik_id'] ?>" class="btn btn-warning" id="buttonPromjeniUlogu">Promjeni
              ulogu</button>
          </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
  </table>
<?php }
?>