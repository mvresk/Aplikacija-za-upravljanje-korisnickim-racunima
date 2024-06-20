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
  header("Refresh:0");
}
?>
<html lang="hr">

<head>
  <title>Moji zahtjevi</title>
  <meta charset="utf-8">
  <meta name="author" content="Marko Vresk">
  <meta name="keywords" content="Moji zahtjevie">
  <meta name="description" content="25.05.2024.">
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

  <div class="modal" tabindex="-1" id="obrišiZahtjev">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Obriši zahtjev</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Sigurni ste da želite obrisati zahtjev?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="obrisi">Obriši</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
        </div>
      </div>
    </div>
  </div>

  <div id="sadrzaj">
    <div class="container" style="margin-top: 100px;">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4>Moji zahtjevi</h4>
            </div>
            <div class="card-body">
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
                  $upitDohvatiZahtjeve = "SELECT *, to_char(datum_kreiranja_zahtjeva, 'YYYY-MM-DD HH24:MI:SS') as datum_kreiranja_zahtjeva FROM KorisniciGrupeZahtjevi WHERE korisnicko_ime= '{$_SESSION["korisnik"]}'";
                  $rezultatDohvatiZahtjeve = $veza->selectDB($upitDohvatiZahtjeve);
                  while ($red = pg_fetch_array($rezultatDohvatiZahtjeve)) {
                    ?>
                    <tr>
                      <td><?= $red['ime'] ?></td>
                      <td><?= $red['prezime'] ?></td>
                      <td><?= $red['email_adresa'] ?></td>
                      <td><?= $red['naziv_grupe'] ?></td>
                      <td><?= $red['datum_kreiranja_zahtjeva'] ?></td>

                      <td>
                        <?php
                        if ($red['aktivan'] === 'da') {
                          ?>
                          <button type="button" value="<?= $red['zahtjev_id'] ?>" class="btn btn-danger"
                            id="buttonObriši">Obriši</button>
                        <?php
                        }
                        ?>
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
    $(document).on('click', '#buttonObriši', function () {
      var zahtjevId = $(this).val();
      $('#obrišiZahtjev').modal('show');
      $('#obrišiZahtjev').data('zahtjevId', zahtjevId);
    });

    $(document).on('click', '#obrisi', function () {
      var akcija = "obriši";
      var zahtjevId = $('#obrišiZahtjev').data('zahtjevId');
      $.ajax({
        url: "biblioteke/pocetnaPodaci.php",
        method: "POST",
        data: { akcija: akcija, zahtjevId: zahtjevId },
        success: function (data) {
          alert(data);
          $('#obrišiZahtjev').modal('hide');
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }
      });

    });

  });
</script>