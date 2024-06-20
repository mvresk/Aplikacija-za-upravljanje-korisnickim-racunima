<?php
include 'biblioteke/dohvacanjePodatakaMojProfil.php';
Sesija::kreirajSesiju();
if (!isset($_SESSION["uloga"])) {
    echo '<script>
    window.location.href="index.php";
    </script>';
}
?>
<html lang="hr">

<head>
    <title>Registracija</title>
    <meta charset="utf-8">
    <meta name="author" content="Marko Vresk">
    <meta name="keywords" content="Registracija">
    <meta name="description" content="15.02.2024.">
    <link href="css/mvresk.css" rel="stylesheet" type="text/css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
        <div id="formaRegistracija">
            <h1>MOJ PROFIL</h1>
            <div id="poruke"> <?php
            if (isset($poruke)) {
                echo "<p>$poruke</p>";
            }
            ?></div>
            <form novalidate id="registracija" method="post" name="registracija" action="">
                <i class='bx bxs-user'></i>
                <input type="text" class="inputRegistracija" id="imeR" name="imeR" placeholder="Ime"
                    value="<?php echo $row['ime']; ?>"><br>
                <i class='bx bxs-user'></i>
                <input type="text" class="inputRegistracija" id="prezimeR" name="prezimeR" placeholder="Prezime"
                    value="<?php echo htmlspecialchars($row['prezime'], ENT_QUOTES, 'UTF-8'); ?>"><br>
                <i class='bx bxs-envelope'></i>
                <input type="text" class="inputRegistracija" id="korImeR" name="korImeR" placeholder="Korisničko Ime"
                    value="<?php echo $row['korisnicko_ime']; ?>"><br>
                <i class='bx bxs-envelope'></i>
                <input type="text" class="inputRegistracija" id="emailR" name="emailR" placeholder="Email"
                    value="<?php echo $row['email_adresa']; ?>"><br>
                <i class='bx bxs-calendar'></i>
                <input type="date" class="inputRegistracija" id="datumRođenja" name="datumRođenja"
                    placeholder="Datum rođenja" value="<?php echo $row['datum_rodenja']; ?>"><br>
                <i class='bx bxs-phone'></i>
                <input type="text" class="inputRegistracija" id="brojMobitela" name="brojMobitela"
                    placeholder="Broj mobitela" value="<?php echo $row['broj_mobitela']; ?>"><br>
                <input type="submit" name="azurirajMojProfilButton" id="registracijaButton"
                    class="azurirajMojProfilButton" value="Ažuriraj profil">

            </form><br>
            <div class="imaRacun">
                <a href="promjeniLozinku.php">
                    <p class="istiRed">Promjeni lozinku</p>

                </a>
                </br>
                <a href="pocetna.php">
                    <p class="istiRed">Natrag</p>

                </a>
            </div>
        </div>
    </div>
</body>

</html>