<?php
include 'biblioteke/baza.php';
include 'biblioteke/sesija.php';
$veza = new Baza();
$veza->spojiDB();
Sesija::kreirajSesiju();
$grupaId = filter_input(INPUT_GET, 'id');
if (!isset($_SESSION["uloga"])) {
  echo '<script>
        window.location.href="index.php";
        </script>';
}
?>
<html lang="hr">

<head>
  <title>Poruke</title>
  <meta charset="utf-8">
  <meta name="author" content="Marko Vresk">
  <meta name="keywords" content="Poruke">
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

  <div id="sadrzaj">
    <div class="modal fade" id="dodajNovuPoruku" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Dodaj novu kategoriju</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="spremiKorisnika">

            <div class="modal-body">
              <div class="mb-3">
                <label for="">Nalov poruke</label>
                <input type="text" name="naslovPoruke" id="naslovPoruke" class="form-control" />
              </div>
              <div class="mb-3">
                <label for="">Sadržaj poruke</label>
                <textarea name="sadržajPoruke" id="sadržajPoruke" class="form-control"
                  style=" height: 120px;"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="spremi">Spremi</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id='odustani'>Odustani</button>
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
              <h4>Poruke
                <button type="button" class="btn btn-primary float end" data-bs-toggle="modal"
                  data-bs-target="#dodajNovuPoruku">Dodaj novu poruku</button>
              </h4>
            </div>
            <div class="input-group mb-3"></div>
            <div class="card-body">
              <table id="tablicaPodaci" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Pošiljatelj</th>
                    <th>Naslov poruke</th>
                    <th>Sadržaj poruke</th>
                    <th>Datum kreiranja</th>
                    <th>Akcija</th>
                  </tr>
                </thead>
                <tbody>
                  <?php

                  $upitDohvatiKorisnikID = "SELECT * FROM PregledPoruka WHERE grupa_id=$grupaId";
                  $result = $veza->selectDB($upitDohvatiKorisnikID);
                  while ($red = pg_fetch_array($result)) {
                    ?>
                    <tr>
                      <td><?= $red['korisnicko_ime'] ?></td>
                      <td><?= $red['naslov'] ?></td>
                      <td><?= $red['sadrzaj'] ?></td>
                      <td><?= $red['vrijeme'] ?></td>

                      <td>
                        <?php
                        if ($red['korisnicko_ime'] === $_SESSION["korisnik"]) {
                          ?>
                          <button type="button" value="<?= $red['poruka_id'] ?>" class="btn btn-danger"
                            id="buttonObriši">Obriši poruku</button>
                        <?php } ?>
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
    $(document).on('click', '#dodajNovuPoruku', function () {
      $('#dodajNovuPoruku').modal('show');
    });

    $(document).on('click', '#spremi', function () {
      var akcija = "dodajPoruku";
      var naslovPoruke = $('#naslovPoruke').val();
      var sadrzajPoruke = $('#sadržajPoruke').val();
      var grupaId = '<?php echo $grupaId; ?>';
      $.ajax({
        url: "biblioteke/dohvatiPoruke.php",
        method: "POST",
        data: { akcija: akcija, naslovPoruke: naslovPoruke, sadrzajPoruke: sadrzajPoruke, grupaId: grupaId },
        success: function (data) {
          alert(data);
          $('#dodajNovuPoruku').modal('hide');
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }
      });
    });

    $(document).on('click', '#buttonObriši', function () {
      var akcija = "obrišiPoruku";
      var porukeId = $(this).val();
      var grupaId = '<?php echo $grupaId; ?>';
      $.ajax({
        url: "biblioteke/dohvatiPoruke.php",
        method: "POST",
        data: { akcija: akcija, porukeId: porukeId, grupaId: grupaId },
        success: function (data) {
          alert(data);
          $('#dodajNovuPoruku').modal('hide');
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }
      });
    });
  });
</script>