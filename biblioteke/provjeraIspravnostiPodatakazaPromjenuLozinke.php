<?php
include 'biblioteke/baza.php';
include 'biblioteke/sesija.php';
$veza = new Baza();
$veza->spojiDB();
Sesija::kreirajSesiju();
$poruke = "";
$stara_lozinka = filter_input(INPUT_POST, 'lozinkaStara');
$nova_lozinka = filter_input(INPUT_POST, 'lozinkaNova');
$potvrda_nove_lozinke = filter_input(INPUT_POST, 'potvrdaLozinkaNova');
$hashLozinka = "";
$br = 0;
$kor_ime = $_SESSION['korisnik'];
$pom = 0;


if (filter_input(INPUT_POST, 'provjeraIspravnostiButton')) {
    $reLozinka = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{7,20}$/';
    if (!preg_match($reLozinka, $nova_lozinka)) {
        $poruke .= "Lozinka ne zadovoljava sve specificirane uvjete(barem 1 malo i veliko slovo,1 broj ,najmanje 7 ,a najviše 20 znakova.Zabranjeno je korištenje specijalnih znakova)!</br>";
        $br++;
    }

    $query_verify_password = "SELECT verify($1, $2) AS password_matched";
    $params = array($kor_ime, $stara_lozinka);
    $result_verify_password = pg_query_params($veza->spojiDB(), $query_verify_password, $params);
    if ($result_verify_password) {
        $row_verify_password = pg_fetch_assoc($result_verify_password);
        $password_matched = intval($row_verify_password['password_matched']) === 1;
        if ($password_matched) {
            $pom++;
        } else {
            $poruke .= "Stara lozinka nije ispravna.</br>";
            $br++;
        }
    }

    if ($nova_lozinka != $potvrda_nove_lozinke) {
        $poruke .= "Lozinke nisu identične.</br>";
        $br++;
    }

    if ($br == 0) {
        $upit4 = "UPDATE korisnici SET lozinka=crypt('" . $nova_lozinka . "', gen_salt('md5')) WHERE korisnicko_ime='{$_SESSION["korisnik"]}'";
        $rezultat4 = $veza->updateDB($upit4);
        echo '<script>alert("Uspješno ste promijenili lozinku!!!"); window.location.href = "mojProfil.php";</script>';
    }


}



?>