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
    <title>Pregled grupa</title>
    <meta charset="utf-8">
    <meta name="author" content="Marko Vresk">
    <meta name="keywords" content="Pregled grupa">
    <meta name="description" content="15.02.2024.">
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
        <div class="modal fade" id="pregledajGrupu" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
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
                                <input type="text" name="datumKreiranja" id="datumKreiranja" class="form-control"
                                    disabled />
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
                                <input type="text" name="prezimeVlasnika" id="prezimeVlasnika" class="form-control"
                                    disabled />
                            </div>
                            <div class="mb-3">
                                <label for="">E-mail adresa vlasnika</label>
                                <input type="text" name="emailAdresa" id="emailAdresa" class="form-control" disabled />
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
                            <h4>Informacije o grupama</h4>
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
                                                         LEFT JOIN članovi_grupa cg ON g.grupa_id = cg.grupa_id AND cg.korisnik_id = '{$korisnik_id}'
                                                         LEFT JOIN korisnici k ON g.kreirao = k.korisnik_id
                                                         WHERE cg.korisnik_id IS NULL";
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
                                                        id="buttonPošaljiZahtjev">Pošalji zahtjev</button>
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
            $('#ime').prop('readonly', true);
            $.ajax({
                url: "biblioteke/dohvatiPodatkePregledGrupa.php",
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
                url: "biblioteke/dohvatiPodatkePregledGrupa.php",
                method: "POST",
                data: { filtriraj: filtriraj, akcija: akcija },
                success: function (data) {
                    $('.card-body').html(data);
                }
            });
        });

        $(document).on('click', '#buttonPošaljiZahtjev', function () {
            var id = $(this).val();
            var akcija = "pošaljiZahtjev";
            $.ajax({
                url: "biblioteke/dohvatiPodatkePregledGrupa.php",
                method: "POST",
                data: { id: id, akcija: akcija },
                success: function (data) {
                    alert(data);

                }
            });

        });


    });
</script>