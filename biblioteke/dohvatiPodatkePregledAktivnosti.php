<?php
include 'baza.php';
$br = 0;
$veza = new Baza();
$veza->spojiDB();
if (filter_input(INPUT_POST, 'akcija') === "pregledaj") {
    $aktivnostid = filter_input(INPUT_POST, 'id');
    $upit2 = "SELECT *, to_char(datum, 'YYYY-MM-DD HH24:MI:SS') as datum
            FROM KorisniciAktivnostiView
            WHERE aktivnost_id='" . $aktivnostid . "'";
    $rezultat2 = $veza->selectDB($upit2);
    $red = pg_fetch_array($rezultat2);
    $rezultat3[] = array(
        "ime" => $red['ime'],
        "prezime" => $red['prezime'],
        "korisnicko_ime" => $red['korisnicko_ime'],
        "naziv_aktivnosti" => $red['naziv_aktivnosti'],
        "opis_aktivnosti" => $red['opis_aktivnosti'],
        "datum" => $red['datum']


    );
    echo json_encode($rezultat3);

}

if (filter_input(INPUT_POST, 'akcija') === "pretraziISortiraj") {

    $filtriraj = filter_input(INPUT_POST, 'filtriraj');
    $vrijednost = filter_input(INPUT_POST, 'vrijednost');


    $upit = "SELECT * FROM KorisniciAktivnostiView WHERE naziv_aktivnosti LIKE '{$filtriraj}%'";

    if ($vrijednost === '0') {
        $upit .= " ORDER BY datum ASC";
    } elseif ($vrijednost === '1') {
        $upit .= " ORDER BY datum DESC";
    }

    $rezultat = $veza->selectDB($upit);
    ?>
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
            while ($red = pg_fetch_array($rezultat)) {
                ?>
                <tr>
                    <td><?= $red['ime'] ?></td>
                    <td><?= $red['prezime'] ?></td>
                    <td><?= $red['korisnicko_ime'] ?></td>
                    <td><?= $red['naziv_aktivnosti'] ?></td>
                    <td><?= $red['opis_aktivnosti'] ?></td>
                    <td><?= $red['datum'] ?></td>
                    <td>
                        <button type="button" value="<?= $red['korisnik_id'] ?>" class="btn btn-success"
                            id="buttonPregledaj">Pregledaj</button>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
?>
