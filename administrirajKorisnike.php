<?php
include 'biblioteke/baza.php';
include 'biblioteke/sesija.php';
$veza = new Baza();
$veza->spojiDB();
Sesija::kreirajSesiju();
if (!isset($_SESSION["uloga"])) {
  echo '<script>
        window.location.href="index.php";
        </script>';
}
?>
<html lang="hr">

<head>
  <title>Administriraj korisnike</title>
  <meta charset="utf-8">
  <meta name="author" content="Marko Vresk">
  <meta name="keywords" content="Administriraj Korisnike">
  <meta name="description" content="11.05.2024.">
  <link href="css/mvresk.css" rel="stylesheet" type="text/css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</head>

</head>

<body>

  <header id="zaglavlje">
    <nav class="navigacija">
      <a class="veza" href="mojeGrupe.php">Moje grupe</a>
      <a class="veza" href="mojProfil.php">Moj profil</a>
      <a class="veza" href="pocetna.php">Moji zahtjevi</a>
      <a class="veza" href="pregledGrupa.php">Pregled grupa</a>
      <?php
      if ($_SESSION["uloga"] == 1 || $_SESSION["uloga"] == 3) {
        ?>
        <a class="veza" href="pregledAktivnosti.php">Pregledaj aktivnosti</a>
        <a class="veza" href="zahtjevi.php">Zahtjevi</a>
      <?php }
      if ($_SESSION["uloga"] == 3) {
        ?>
        <a class="veza" href="administrirajKorisnike.php">Administriraj korisnike</a>
        <a class="veza" href="promjeniUlogu.php">Promjeni ulogu</a>
        <?php

      }
      ?>
    </nav>
    <button type="button" class="btn btn-primary" onclick="location.href='biblioteke/odjava.php'">Odjava</button>
  </header>

  <div class="modal fade" id="dodajKorisnika" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Dodaj novog korisnika</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="spremiKorisnika">
          <div class="modal-body">
            <div class="mb-3">
              <label for="">Ime</label>
              <input type="text" name="ime" id="ime" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">Prezime</label>
              <input type="text" name="prezime" id="prezime" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">Korisnicko ime</label>
              <input type="text" name="kor_ime" id="kor_ime" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">E-mail adresa</label>
              <input type="text" name="emailadresa" id="emailAdresa" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">Datum rođenja</label>
              <input type="date" name="datumRođenja" id="datumRođenja" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">Broj mobitela</label>
              <input type="text" name="brojMobitela" id="brojMobitela" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">Lozinka</label>
              <input type="password" name="lozinka" id="lozinka" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">Ponovljena lozinka</label>
              <input type="password" name="Ponovljenalozinka" id="Ponovljenalozinka" class="form-control" />
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zatvori</button>
            <button type="submit" class="btn btn-primary">Spremi promjene</button>
          </div>
        </form>
      </div>
    </div>
  </div>





  <div class="modal fade" id="pregledKorisnika" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Informacije o korisniku</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <!-- <form id="spremiKorisnika"> -->
        <div class="modal-body">
          <div class="mb-3">
            <label for="">Ime</label>
            <input type="text" name="ime" id="imeP" class="form-control" disabled />
          </div>
          <div class="mb-3">
            <label for="">Prezime</label>
            <input type="text" name="prezime" id="prezimeP" class="form-control" disabled />
          </div>
          <div class="mb-3">
            <label for="">Korisničko ime</label>
            <input type="text" name="kor_ime" id="korImeP" class="form-control" disabled />
          </div>
          <div class="mb-3">
            <label for="">Email adresa</label>
            <input type="text" name="emailP" id="emailP" class="form-control" disabled />
          </div>
          <div class="mb-3">
            <label for="">Datum rođenja</label>
            <input type="text" name="datumRođenjaP" id="datumRođenjaP" class="form-control" disabled />
          </div>
          <div class="mb-3">
            <label for="">Broj mobitela</label>
            <input type="text" name="brojMobitelaP" id="brojMobitelaP" class="form-control" disabled />
          </div>
          <div class="mb-3">
            <label for="">Blokiran</label>
            <input type="text" name="blokiranP" id="blokiranP" class="form-control" disabled />
          </div>
          <div class="mb-3">
            <label for="">Uloga</label>
            <input type="text" name="ulogaP" id="ulogaP" class="form-control" disabled />
          </div>
          <div class="mb-3">
            <label for="">Zadnja aktivnost</label>
            <input type="text" name="zadnjaAktivnostP" id="zadnjaAktivnostP" class="form-control" disabled />
          </div>
          <div class="mb-3">
            <label for="">Datum zadnje aktivnosti</label>
            <input type="text" name="datumZadnjeAktivnostiP" id="datumZadnjeAktivnostiP" class="form-control"
              disabled />
          </div>
          <div class="mb-3">
            <label for="">Broj grupa u kojima je korisnik</label>
            <input type="text" name="brojGrupaP" id="brojGrupaP" class="form-control" disabled />
          </div>

        </div>
        <!-- </form> -->
      </div>
    </div>
  </div>


  <div class="modal fade" id="ažuriranjeKorisnika" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Dodaj novog korisnika</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="spremiKorisnika">
          <div class="modal-body">
            <div class="mb-3">
              <label for="">Ime</label>
              <input type="text" name="ime" id="imeA" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">Prezime</label>
              <input type="text" name="prezime" id="prezimeA" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">Korisnicko ime</label>
              <input type="text" name="kor_ime" id="kor_imeA" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">Email adresa</label>
              <input type="text" name="email" id="emailA" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">Datum rođenja</label>
              <input type="date" name="datumRođenja" id="datumRođenjaA" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="">Broj mobitela</label>
              <input type="text" name="brojMobitela" id="brojMobitelaA" class="form-control" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zatvori</button>
            <button type="submit" class="btn btn-primary" id="spremi">Spremi promjene</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="container" style="margin-top: 100px;">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>Administriraj korisnike
              <button type="button" class="btn btn-primary float end" data-bs-toggle="modal"
                data-bs-target="#dodajKorisnika">Dodaj novog korisnika</button>
            </h4>
          </div>
          <form id="prijava" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="Ime i prezime korisnika" id="pretraži">
            </div>
          </form>
          <div class="card-body">
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
                <?php
                $upitDohvatiKorisnike = "SELECT * FROM prijavljeniKorisnik";
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
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
<script type="text/javascript">
  $(document).ready(function () {
    $('#spremiKorisnika').submit(function (event) {

      event.preventDefault();
      var forma = new FormData(this);
      forma.append("spremi", true);
      $.ajax({
        url: "biblioteke/administrirajKorisnika.php",
        method: "POST",
        data: forma,
        processData: false,
        contentType: false,
        success: function (data) {
          alert(data);
          $('#dodajKorisnika').modal('hide');
          $('#spremiKorisnika')[0].reset();
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }
      });

    });
    $(document).on('click', '#buttonPregledaj', function () {
      var id = $(this).val();
      var akcija = "pregledaj";
      $.ajax({
        url: "biblioteke/administrirajKorisnika.php",
        method: "POST",
        data: { id: id, akcija: akcija },
        dataType: "json",
        success: function (data) {
          $('#imeP').val(data[0].ime);
          $('#prezimeP').val(data[0].prezime);
          $('#korImeP').val(data[0].korisnicko_ime);
          $('#emailP').val(data[0].email_adresa);
          $('#datumRođenjaP').val(data[0].datum_rodenja);
          $('#brojMobitelaP').val(data[0].broj_mobitela);
          $('#ulogaP').val(data[0].naziv_uloge);
          $('#blokiranP').val(data[0].blokiran === "f" ? "ne" : "da");
          $('#zadnjaAktivnostP').val(data[0].naziv_aktivnosti);
          $('#datumZadnjeAktivnostiP').val(data[0].datum_zadnjeaktivnosti);
          $('#brojGrupaP').val(data[0].broj_grupa);
          $('#pregledKorisnika').modal('show');

        }
      });

    });
    $(document).on('click', '#buttonIzbriši', function () {
      var id = $(this).val();
      var akcija = "izbriši";
      $.ajax({
        url: "biblioteke/administrirajKorisnika.php",
        method: "POST",
        data: { id: id, akcija: akcija },
        success: function (data) {
          alert(data);
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");

        }
      });

    });

    $(document).on('click', '#buttonAžuriraj', function () {
      var id = $(this).val();
      var akcija = "ažuriraj";
      $('#ažuriranjeKorisnika').data('id', id);
      $.ajax({
        url: "biblioteke/administrirajKorisnika.php",
        method: "POST",
        data: { id: id, akcija: akcija },
        dataType: "json",
        success: function (data) {
          $('#imeA').val(data[0].ime);
          $('#prezimeA').val(data[0].prezime);
          $('#kor_imeA').val(data[0].korisnicko_ime);
          $('#emailA').val(data[0].email_adresa);
          $('#datumRođenjaA').val(data[0].datum_rodenja);
          $('#brojMobitelaA').val(data[0].broj_mobitela);
          $('#ažuriranjeKorisnika').modal('show');

        }

      });

    });
    $(document).on('click', '#spremi', function () {
      var akcija2 = "spremi";
      var imeA = $('#imeA').val();
      var prezimeA = $('#prezimeA').val();
      var kor_imeA = $('#kor_imeA').val();
      var emailA = $('#emailA').val();
      var datumRođenjaA = $('#datumRođenjaA').val();
      var brojMobitelaA = $('#brojMobitelaA').val();
      var id = $('#ažuriranjeKorisnika').data('id');
      $.ajax({
        url: "biblioteke/administrirajKorisnika.php",
        method: "POST",
        data: { id: id, akcija2: akcija2, imeA: imeA, kor_imeA: kor_imeA, prezimeA: prezimeA, emailA: emailA, datumRođenjaA: datumRođenjaA, brojMobitelaA: brojMobitelaA },
        success: function (data) {
          alert(data);
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }

      });
    });
    $(document).on('click', '#buttonBlokiraj', function () {
      var akcija = "blokiraj";
      var id = $(this).val();
      $.ajax({
        url: "biblioteke/administrirajKorisnika.php",
        method: "POST",
        data: { id: id, akcija: akcija },
        success: function (data) {
          alert(data);
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }

      });
    });
    $(document).on('click', '#buttonOdblokiraj', function () {
      var akcija = "odblokiraj";
      var id = $(this).val();
      $.ajax({
        url: "biblioteke/administrirajKorisnika.php",
        method: "POST",
        data: { id: id, akcija: akcija },
        success: function (data) {
          alert(data);
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }

      });
    });
    $("#pretraži").on('keyup click', function () {
      var filtriraj = $("#pretraži").val();
      var akcija = "pretrazi";
      $.ajax({
        url: "biblioteke/administrirajKorisnika.php",
        method: "POST",
        data: { filtriraj: filtriraj, akcija: akcija },
        success: function (data) {
          $('.card-body').html(data);
        }
      });
    });
    $(document).on('change', '#sortiranjeDatum', function () {
      var vrijednost = $('#sortiranjeDatum').val();
      var akcija = 'sortiraj';
      $.ajax({
        url: "biblioteke/administrirajKorisnika.php",
        method: "POST",
        data: { vrijednost: vrijednost, akcija: akcija },
        success: function (data) {
          $('.card-body').html(data);
        }
      });
    });
  });
</script>