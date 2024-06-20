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
  <title>Zahtjevi za ulazak u grupe</title>
  <meta charset="utf-8">
  <meta name="author" content="Marko Vresk">
  <meta name="keywords" content="Zahtjevi za ulazak u grupe">
  <meta name="description" content="22.05.2024.">
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
    <div class="modal fade" id="pregledajZahtjeve" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Pregled zahtjeva za ulazak u grupu</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="spremiKorisnika">
            <div class="modal-body">
              <div class="mb-3">
                <label for="">Ime pošiljatelja zahtjeva</label>
                <input type="text" name="imePošiljateljaZahtjeva" id="imePošiljateljaZahtjeva" class="form-control"
                  disabled />
              </div>
              <div class="mb-3">
                <label for="">Prezime pošiljatelja zahtjeva</label>
                <input type="text" name="prezimePošiljateljaZahtjeva" id="prezimePošiljateljaZahtjeva"
                  class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">E-mail adresa pošiljatelja</label>
                <input type="text" name="emailAdresa" id="emailAdresa" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Broj mobitela pošiljatelja</label>
                <input type="text" name="brojMobitela" id="brojMobitela" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Naziv grupe</label>
                <input type="text" name="nazivGrupe" id="nazivGrupe" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Opis grupe</label>
                <textarea name="opisGrupe" id="opisGrupe" class="form-control" style=" height: 120px;"
                  disabled>></textarea>
              </div>
              <div class="mb-3">
                <label for="">Datum kreiranja zahtjeva</label>
                <input type="text" name="datumKreiranja" id="datumKreiranja" class="form-control" disabled />
              </div>
              <div class="mb-3">
                <label for="">Aktivan</label>
                <input type="text" name="aktivan" id="aktivan" class="form-control" disabled />
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal" tabindex="-1" id="odbijPrihvatiZahtjev">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Odbij zahtjev</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Sigurni ste da želite odbiti zahtjev?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
            <button type="button" class="btn btn-primary" id="odbij">Odbij</button>
          </div>
        </div>
      </div>
    </div>


    <div class="container" style="margin-top: 100px;">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4>Zahtjevi</h4>
            </div>
            <div style="margin-left:20px; margin-right:20px;">
              <h3>Sortiraj po datum kreiranja zahtjeva</h3>
              <form>
                <select class="form-select mt-3" name="sortiranjeDatum" id="sortiranjeDatum">
                  <option value="-1" selected="selected">--- Odaberi način sortiranja ---</option>
                  <option value="0">Stariji zahtjevi - Noviji zahtjevi</option>
                  <option value="1">Noviji zahtjevi - Stariji zahtjevi</option>
                </select>
              </form>
              <div class="mb-3">
                <select class="form-select mt-3" name="sortiranjeNazivGrupe" id="sortiranjeNazivGrupe">
                  <option value="-1" selected="selected">---Odaberi grupu--</option>
                  <?php
                  $upit = "SELECT * FROM grupa";
                  $rezultat = $veza->selectDB($upit);
                  while ($red = pg_fetch_array($rezultat)) {
                    ?>
                    <option value="<?php echo $red['grupa_id']; ?>"><?php echo $red['naziv_grupe']; ?></option>
                  <?php }
                  ?>
                </select>
              </div>
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
                  $upitDohvatiZahtjeve = "SELECT *, to_char(datum_kreiranja_zahtjeva, 'YYYY-MM-DD HH24:MI:SS') as datum_kreiranja_zahtjeva FROM KorisniciGrupeZahtjevi";
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
                        <button type="button" value="<?= $red['zahtjev_id'] ?>" class="btn btn-success"
                          id="buttonPregledaj">Pregledaj</button>
                        <?php
                        if ($red['aktivan'] === 'da') {
                          ?>
                          <button type="button" value="<?= $red['zahtjev_id'] ?>" class="btn btn-warning"
                            id="buttonPrihvati">Prihvati</button>
                          <button type="button" value="<?= $red['zahtjev_id'] ?>" class="btn btn-danger"
                            id="buttonOdbij">Odbij</button>
                        <?php
                        }
                        ?>
                      </td>
                    </tr>
                    <?php
                  }
                  // }
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
<script type="text/javascript">
  $(document).ready(function () {
    $(document).on('click', '#buttonPregledaj', function () {
      var id = $(this).val();
      var akcija = "pregledaj";
      $.ajax({
        url: "biblioteke/dohvatiZahtjeve.php",
        method: "POST",
        data: { id: id, akcija: akcija },
        dataType: "json",
        success: function (data) {
          $('#imePošiljateljaZahtjeva').val(data[0].ime);
          $('#prezimePošiljateljaZahtjeva').val(data[0].prezime);
          $('#emailAdresa').val(data[0].email_adresa);
          $('#brojMobitela').val(data[0].broj_mobitela);
          $('#nazivGrupe').val(data[0].naziv_grupe);
          $('#opisGrupe').val(data[0].opis_grupe);
          $('#datumKreiranja').val(data[0].datum_kreiranja_zahtjeva);
          $('#aktivan').val(data[0].aktivan);
          $('#pregledajZahtjeve').modal('show');

        }
      });

    });

    $(document).on('click', '#buttonOdbij', function () {
      var id = $(this).val();
      $('#odbijPrihvatiZahtjev').data('id', id);
      $('#odbijPrihvatiZahtjev .modal-title').text('Odbij zahtjev');
      $('#odbijPrihvatiZahtjev .modal-body p').text('Sigurni ste da želite odbiti zahtjev?');
      $('#odbijPrihvatiZahtjev .btn-primary')
        .attr('id', 'odbij')
        .text('Odbij');
      $('#odbijPrihvatiZahtjev').modal('show');

      $(document).off('click', '#odbij').on('click', '#odbij', function () {
        var id = $('#odbijPrihvatiZahtjev').data('id');
        var akcija = "odbij";
        $.ajax({
          url: "biblioteke/dohvatiZahtjeve.php",
          method: "POST",
          data: { id: id, akcija: akcija },
          success: function (data) {
            $('#odbijPrihvatiZahtjev').modal('hide');
            $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
            alert(data);

          }
        });
      });

    });

    $(document).on('click', '#buttonPrihvati', function () {
      var id = $(this).val();
      $('#odbijPrihvatiZahtjev').data('id', id);
      $('#odbijPrihvatiZahtjev .modal-title').text('Prihvati zahtjev');
      $('#odbijPrihvatiZahtjev .modal-body p').text('Sigurni ste da želite prihvatiti zahtjev?');
      $('#odbijPrihvatiZahtjev .btn-primary')
        .attr('id', 'prihvati')
        .text('Prihvati');
      $('#odbijPrihvatiZahtjev').modal('show');

      $(document).off('click', '#prihvati').on('click', '#prihvati', function () {
        var id = $('#odbijPrihvatiZahtjev').data('id');
        var akcija = "prihvati";
        $.ajax({
          url: "biblioteke/dohvatiZahtjeve.php",
          method: "POST",
          data: { id: id, akcija: akcija },
          success: function (data) {
            $('#odbijPrihvatiZahtjev').modal('hide');
            $('#tablicaPodaci').load(location.href + " #tablicaPodaci");
            alert(data);

          }
        });
      });
    });


    $(document).on('change', '#sortiranjeDatum, #sortiranjeNazivGrupe', function () {
      var vrijednost = $('#sortiranjeDatum').val();
      var nazivGrupe = $('#sortiranjeNazivGrupe').val();
      var akcija = 'sortiraj';
      $.ajax({
        url: "biblioteke/dohvatiZahtjeve.php",
        method: "POST",
        data: { vrijednost: vrijednost, nazivGrupe: nazivGrupe, akcija: akcija },
        success: function (data) {
          $('.card-body').html(data);
        }
      });
    });



  });
</script>