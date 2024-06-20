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
  <title>Promjeni ulogu</title>
  <meta charset="utf-8">
  <meta name="author" content="Marko Vresk">
  <meta name="keywords" content="Promjeni ulogu">
  <meta name="description" content="19.05.2024.">
  <link href="css/mvresk.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
            <label for="">Broj grupa u kojima je korisnik</label>
            <input type="text" name="brojGrupaP" id="brojGrupaP" class="form-control" disabled />
          </div>

        </div>
        <!-- </form> -->
      </div>
    </div>
  </div>


  <div class="modal fade" id="promjeniUlogu" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Promjeni ulogu</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="spremiKorisnika">
          <div class="modal-body">
            <div class="mb-3">
              <label for="">Ime</label>
              <input type="text" name="ime" id="imeA" class="form-control" disabled />
            </div>
            <div class="mb-3">
              <label for="">Prezime</label>
              <input type="text" name="prezime" id="prezimeA" class="form-control" disabled />
            </div>
            <div class="mb-3">
              <select class="form-control" name="vrstaUloge" id="vrstaUloge">
                <option value="-1" selected="selected">---Odaberi ulogu---</option>
                <?php
                $upit = "SELECT * FROM Uloga";
                $rezultat = $veza->selectDB($upit);
                while ($red = pg_fetch_array($rezultat)) {
                  ?>
                  <option value="<?php echo $red['uloga_id']; ?>"><?php echo $red['naziv_uloge']; ?></option>
                <?php }
                ?>
              </select>
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
            <h4>Pregled korisnika i njihovih uloga</h4>
          </div>
          <form id="prijava" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="Naziv uloge" id="pretražiUloge">
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
                  <th>Uloga</th>
                  <th>Akcija</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $upitDohvatiKorisnike = "SELECT * FROM KorisnikUloga";
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
                      <button type="button" value="<?= $red['korisnik_id'] ?>" class="btn btn-warning"
                        id="buttonPromjeniUlogu">Promjeni ulogu</button>
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
    $(document).on('click', '#buttonPregledaj', function () {
      var id = $(this).val();
      var akcija = "pregledaj";
      $.ajax({
        url: "biblioteke/dohvatiPodatkePromjeniUlogu.php",
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
          $('#brojGrupaP').val(data[0].broj_grupa);
          $('#pregledKorisnika').modal('show');

        }
      });

    });

    $("#pretražiUloge").on('keyup click', function () {
      var filtriraj = $("#pretražiUloge").val();
      var akcija = "pretrazi";
      $.ajax({
        url: "biblioteke/dohvatiPodatkePromjeniUlogu.php",
        method: "POST",
        data: { filtriraj: filtriraj, akcija: akcija },
        success: function (data) {
          $('.card-body').html(data);
        }
      });
    });

    $(document).on('click', '#buttonPromjeniUlogu', function () {
      var id = $(this).val();
      var akcija = "pregledaj";
      $('#promjeniUlogu').data('id', id);
      $.ajax({
        url: "biblioteke/dohvatiPodatkePromjeniUlogu.php",
        method: "POST",
        data: { id: id, akcija: akcija },
        dataType: "json",
        success: function (data) {
          $('#imeA').val(data[0].ime);
          $('#prezimeA').val(data[0].prezime);
          $('#promjeniUlogu').modal('show');

        }

      });

    });

    $(document).on('click', '#spremi', function () {
      var akcija = "spremi";
      var vrsta = $('#vrstaUloge').val();
      var id = $('#promjeniUlogu').data('id');
      var nazivUloge = $('#vrstaUloge option:selected').text();
      $.ajax({
        url: "biblioteke/dohvatiPodatkePromjeniUlogu.php",
        method: "POST",
        data: { id: id, akcija: akcija, vrsta: vrsta, nazivUloge: nazivUloge },
        success: function (data) {
          alert(data);
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }

      });
    });

  });
</script>