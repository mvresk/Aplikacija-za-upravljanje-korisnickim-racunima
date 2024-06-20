<?php
include 'baza.php';
$veza = new Baza();
$veza->spojiDB();

if (filter_input(INPUT_POST, 'akcija') === "obriši") {
    $zahtjevId = filter_input(INPUT_POST, 'zahtjevId');
    $upit = "DELETE FROM ZahtjevzaGrupu WHERE zahtjev_id = '" . $zahtjevId . "'";
    $rezultat = $veza->selectDB($upit);
    if ($rezultat) {
        echo "Uspješno ste obrisali zahtjev";
    }

}

?>