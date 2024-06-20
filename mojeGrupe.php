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
  <title>Moje grupe</title>
  <meta charset="utf-8">
  <meta name="author" content="Marko Vresk">
  <meta name="keywords" content="Moje grupe">
  <meta name="description" content="23.05.2024.">
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
    <div class="modal fade" id="pregledajGrupu" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Pregled grupe</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="spremiKorisnika">
            <div class="modal-body">
              <div class="mb-3">
                <label for="">Naziv grupe</label>
                <input type="text" name="nazivGrupe" id="nazivGrupe" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Opis grupe</label>
                <textarea name="opisGrupe" id="opisGrupe" class="form-control" style=" height: 120px;"
                  disabled></textarea>
              </div>
              <div class="mb-3">
                <label for="">Datum kreiranja</label>
                <input type="text" name="datumKreiranja" id="datumKreiranja" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Broj članova</label>
                <input type="text" name="brojČlanova" id="brojČlanova" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Ime vlasnika</label>
                <input type="text" name="imeVlasnika" id="imeVlasnika" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Prezime vlasnika</label>
                <input type="text" name="prezimeVlasnika" id="prezimeVlasnika" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">E-mail adresa vlasnika</label>
                <input type="text" name="emailAdresa" id="emailAdresa" class="form-control" disabled />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="spremi" disabled style="display: none;">Spremi</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id='odustani' disabled
                style="display: none;">Odustani</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="dodajGrupu" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Dodaj novu grupu</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="spremiKorisnika">
            <div class="modal-body">
              <div class="mb-3">
                <label for="">Naziv grupe</label>
                <input type="text" name="nazivGrupeD" id="nazivGrupeD" class="form-control" />
              </div>
              <div class="mb-3">
                <label for="">Opis grupe</label>
                <textarea name="opisGrupeD" id="opisGrupeD" class="form-control" style=" height: 120px;"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="spremiD">Spremi</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id='odustaniD'>Odustani</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="dodajKategoriju" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Dodaj novu kategoriju</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="spremiKorisnika">
            <div class="modal-body">
              <div class="mb-3">
                <label for="">Naziv kategorije</label>
                <input type="text" name="nazivKategorije" id="nazivKategorije" class="form-control" />
              </div>
              <div class="mb-3">
                <label for="">Opis kategorije</label>
                <textarea name="opisKategorije" id="opisKategorije" class="form-control"
                  style=" height: 120px;"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="spremiK">Spremi</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id='odustaniK'>Odustani</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="dodijeliKategorijuGrupi" tabindex="-1" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Dodijeli kategoriju grupi</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="spremiKorisnika">
            <div class="modal-body">
              <div class="mb-3">
                <select name="nazivKategorijeD" id="nazivKategorijeD" class="form-control">
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="spremiDKG">Spremi</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id='odustaniDKG'>Odustani</button>
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
              <h4>Informacije o grupama
                <button type="button" class="btn btn-primary float end" data-bs-toggle="modal"
                  data-bs-target="#dodajGrupu">Dodaj novu grupu</button>
                <button type="button" class="btn btn-primary float end" data-bs-toggle="modal"
                  data-bs-target="#dodajKategoriju">Dodaj novu kategoriju</button>
              </h4>
            </div>
            <form id="prijava" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
              <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Naziv kategorije ili grupe"
                  id="pretražiKategoriju">
              </div>
            </form>
            <div class="card-body">
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

                    $upitDohvatiGrupe = "SELECT g.*, to_char(g.datum_kreiranja, 'YYYY-MM-DD HH24:MI:SS') as datum_kreiranja, k.korisnicko_ime
                                    FROM grupa g
                                    LEFT JOIN članovi_grupa cg ON g.grupa_id = cg.grupa_id
                                    LEFT JOIN korisnici k ON g.kreirao = k.korisnik_id
                                    WHERE cg.korisnik_id = '{$korisnik_id}'";
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
      $('#pregledajGrupu .modal-title').text('Pregled grupe');
      document.getElementById('nazivGrupe').disabled = true;
      document.getElementById('opisGrupe').disabled = true;
      document.getElementById('spremi').disabled = true;
      document.getElementById('spremi').style.display = 'none';
      document.getElementById('odustani').disabled = true;
      document.getElementById('odustani').style.display = 'none';
      $.ajax({
        url: "biblioteke/dohvatiMojeGrupe.php",
        method: "POST",
        data: { id: id, akcija: akcija },
        dataType: "json",
        success: function (data) {
          $('#nazivGrupe').val(data[0].naziv_grupe);
          $('#opisGrupe').val(data[0].opis_grupe);
          $('#datumKreiranja').val(data[0].datum_kreiranja);
          $('#brojČlanova').val(data[0].broj_članova);
          $('#imeVlasnika').val(data[0].ime);
          $('#prezimeVlasnika').val(data[0].prezime);
          $('#emailAdresa').val(data[0].email_adresa);
          $('#pregledajGrupu').modal('show');

        }
      });

    });

    $("#pretražiKategoriju").on('keyup click', function () {
      var filtriraj = $("#pretražiKategoriju").val();
      var akcija = "pretrazi";
      $.ajax({
        url: "biblioteke/dohvatiMojeGrupe.php",
        method: "POST",
        data: { filtriraj: filtriraj, akcija: akcija },
        success: function (data) {
          $('.card-body').html(data);
        }
      });
    });

    $(document).on('click', '#buttonAžuriraj', function () {
      var id = $(this).val();
      var akcija = "pregledaj";
      document.getElementById('nazivGrupe').disabled = false;
      document.getElementById('opisGrupe').disabled = false;
      var spremiButton = document.getElementById('spremi');
      spremiButton.disabled = false;
      spremiButton.style.display = 'inline-block';

      var spremiButton = document.getElementById('odustani');
      spremiButton.disabled = false;
      spremiButton.style.display = 'inline-block';
      $('#pregledajGrupu ').data('id', id);
      $('#pregledajGrupu .modal-title').text('Ažuriraj podatke o grupi');
      $.ajax({
        url: "biblioteke/dohvatiMojeGrupe.php",
        method: "POST",
        data: { id: id, akcija: akcija },
        dataType: "json",
        success: function (data) {
          $('#nazivGrupe').val(data[0].naziv_grupe);
          $('#opisGrupe').val(data[0].opis_grupe);
          $('#datumKreiranja').val(data[0].datum_kreiranja);
          $('#brojČlanova').val(data[0].broj_članova);
          $('#imeVlasnika').val(data[0].ime);
          $('#prezimeVlasnika').val(data[0].prezime);
          $('#emailAdresa').val(data[0].email_adresa);
          $('#pregledajGrupu').modal('show');

        }
      });

    });

    $(document).on('click', '#spremi', function () {
      var akcija = "spremi";
      var nazivGrupe = $('#nazivGrupe').val();
      var opisGrupe = $('#opisGrupe').val();
      var id = $('#pregledajGrupu').data('id');
      $.ajax({
        url: "biblioteke/dohvatiMojeGrupe.php",
        method: "POST",
        data: { akcija: akcija, id: id, nazivGrupe: nazivGrupe, opisGrupe: opisGrupe },
        success: function (data) {
          alert(data);
          $('#pregledajGrupu').modal('hide');
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }

      });
    });


    $(document).off('click', '#dodajGrupu').on('click', '#dodajGrupu', function () {
      $('#dodajGrupu').modal('show');
    });

    $(document).off('click', '#spremiD').on('click', '#spremiD', function () {
      var akcija = "dodaj";
      var nazivGrupe = $('#nazivGrupeD').val();
      var opisGrupe = $('#opisGrupeD').val();
      $.ajax({
        url: "biblioteke/dohvatiMojeGrupe.php",
        method: "POST",
        data: { akcija: akcija, nazivGrupe: nazivGrupe, opisGrupe: opisGrupe },
        success: function (data) {
          alert(data);
          $('#dodajGrupu').modal('hide');
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }

      });
    });

    $(document).on('click', '#buttonIzbriši', function () {
      var akcija = "izbriši";
      var id = $(this).val();
      $.ajax({
        url: "biblioteke/dohvatiMojeGrupe.php",
        method: "POST",
        data: { akcija: akcija, id: id },
        success: function (data) {
          alert(data);
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }

      });
    });

    $(document).on('click', '#dodajKategoriju', function () {
      $('#dodajKategoriju').modal('show');
    });

    $(document).on('click', '#spremiK', function () {
      var akcija = "dodajKategoriju";
      var nazivKategorije = $('#nazivKategorije').val();
      var opisKategorije = $('#opisKategorije').val();
      $.ajax({
        url: "biblioteke/dohvatiMojeGrupe.php",
        method: "POST",
        data: { akcija: akcija, nazivKategorije: nazivKategorije, opisKategorije: opisKategorije },
        success: function (data) {
          alert(data);
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
          $('#dodajKategoriju').modal('hide');
        }

      });
    });

    $(document).on('click', '#buttonDodjeliKategoriju', function () {
      var grupaId = $(this).val();
      $('#dodijeliKategorijuGrupi').data('grupaId', grupaId);
      var akcija = "dohvatiKategorije"
      $.ajax({
        url: 'biblioteke/dohvatiMojeGrupe.php',
        type: 'POST',
        data: { grupaId: grupaId, akcija: akcija },
        success: function (data) {
          $('#nazivKategorijeD').html(data);
          $('#dodijeliKategorijuGrupi').modal('show');
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");

        }
      });
    });

    $(document).on('click', '#spremiDKG', function () {
      var akcija = "spremiDKG";
      var kategorijaId = $('#nazivKategorijeD').val();
      var grupaId = $('#dodijeliKategorijuGrupi').data('grupaId');
      $.ajax({
        url: "biblioteke/dohvatiMojeGrupe.php",
        method: "POST",
        data: { akcija: akcija, kategorijaId: kategorijaId, grupaId: grupaId },
        success: function (data) {
          alert(data);
          $('#dodijeliKategorijuGrupi').modal('hide');
          $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
        }

      });
    });

    $(document).on('click', '#buttonPregledPoruka', function () {
      var grupaId = $(this).val();
      window.open('poruke.php?id=' + grupaId, '_blank');
    });


  });
</script>