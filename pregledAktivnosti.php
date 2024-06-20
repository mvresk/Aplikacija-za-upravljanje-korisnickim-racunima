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
  <title>Pregled aktivnosti</title>
  <meta charset="utf-8">
  <meta name="author" content="Marko Vresk">
  <meta name="keywords" content="Pregled aktivnosti">
  <meta name="description" content="20.05.2024.">
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

  <div id="sadrzaj">
    <div class="modal fade" id="pregledajKorisnika" tabindex="-1" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Aktivnost</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="spremiKorisnika">
            <div class="modal-body">
              <div class="mb-3">
                <label for="">Ime</label>
                <input type="text" name="ime" id="ime" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Prezime</label>
                <input type="text" name="prezime" id="prezime" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Korisnicko ime</label>
                <input type="text" name="kor_ime" id="kor_ime" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Naziv aktivnosti</label>
                <input type="text" name="nazivAktivnosti" id="nazivAktivnosti" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Opis aktivnosti</label>
                <input type="text" name="opisAktivnosti" id="opisAktivnosti" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Datum aktivnosti</label>
                <input type="text" name="datumAktivnosti" id="datumAktivnosti" class="form-control" disabled />
              </div>
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
              <h4>Pregled aktivnosti</h4>
            </div>

            <form id="prijava" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
              <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Naziv aktivnosti" id="pretražiAktivnost">
              </div>
              <form>
                <select class="form-select mt-3" name="sortiranjeDatum" id="sortiranjeDatum">
                  <option value="-1" selected="selected">--- Odaberi način sortiranja ---</option>
                  <option value="0">Starije aktivnosti - Novije aktivnosti</option>
                  <option value="1">Novije aktivnosti - Starije aktivnosti</option>
                </select>
              </form>
              <div class="card-body">
                <table id="tablicaPodaci" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Ime</th>
                      <th>Prezime</th>
                      <th>Korisničko ime</th>
                      <th>Naziv aktivnosti</th>
                      <th>Opis aktivnosti</th>
                      <th>Datum aktivnosti</th>
                      <th>Akcija</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $upitDohvatiKorisnike = "SELECT *, to_char(datum, 'YYYY-MM-DD HH24:MI:SS') as datum FROM KorisniciAktivnostiView";
                    $rezultatDohvatiKorisnike = $veza->selectDB($upitDohvatiKorisnike);
                    while ($red = pg_fetch_array($rezultatDohvatiKorisnike)) {
                      ?>
                      <tr>
                        <td><?= $red['ime'] ?></td>
                        <td><?= $red['prezime'] ?></td>
                        <td><?= $red['korisnicko_ime'] ?></td>
                        <td><?= $red['naziv_aktivnosti'] ?></td>
                        <td><?= $red['opis_aktivnosti'] ?></td>
                        <td><?= $red['datum'] ?></td>
                        <td>
                          <button type="button" value="<?= $red['aktivnost_id'] ?>" class="btn btn-success"
                            id="buttonPregledaj">Pregledaj</button>
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
  </div>
</body>


</html>
<script type="text/javascript">
  $(document).ready(function () {
    $(document).on('click', '#buttonPregledaj', function () {
      var id = $(this).val();
      var akcija = "pregledaj";
      $.ajax({
        url: "biblioteke/dohvatiPodatkePregledAktivnosti.php",
        method: "POST",
        data: { id: id, akcija: akcija },
        dataType: "json",
        success: function (data) {
          $('#ime').val(data[0].ime);
          $('#prezime').val(data[0].prezime);
          $('#kor_ime').val(data[0].korisnicko_ime);
          $('#nazivAktivnosti').val(data[0].naziv_aktivnosti);
          $('#opisAktivnosti').val(data[0].opis_aktivnosti);
          $('#datumAktivnosti').val(data[0].datum);
          $('#pregledajKorisnika').modal('show');

        }
      });

    });


    $("#pretražiAktivnost, #sortiranjeDatum").on('keyup click change', function () {
      var filtriraj = $("#pretražiAktivnost").val();
      var vrijednost = $("#sortiranjeDatum").val();
      var akcija = "pretraziISortiraj";
      $.ajax({
        url: "biblioteke/dohvatiPodatkePregledAktivnosti.php",
        method: "POST",
        data: { filtriraj: filtriraj, vrijednost: vrijednost, akcija: akcija },
        success: function (data) {
          $('.card-body').html(data);
        }
      });
    });


  });
</script>