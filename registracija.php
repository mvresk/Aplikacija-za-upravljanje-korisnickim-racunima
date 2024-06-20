<?php
include 'biblioteke/provjeraRegistracija.php';
$direktorij = getcwd();
$putanja = dirname($_SERVER['REQUEST_URI']);
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
</head>

<body>

    <header>
        <h2 class="naslov">Registracija</h2>

    </header>

    <div id="sadrzaj">
        <div id="formaRegistracija">
            <h1>Registracija</h1>
            <div id="poruke"> <?php
            if (isset($poruke)) {
                echo "<p>$poruke</p>";
            }
            ?></div>
            <form novalidate id="registracija" method="post" name="registracija" action="">
                <div id="prijavaRegistracija">
                    <p id="prijavaPromjenaR"> <a href="index.php">Prijava</a></p>
                    <p id="registracijaPromjenaR"><a href="registracija.php"> Registracija</a></p>
                </div>
                <i class='bx bxs-user'></i>
                <input type="text" class="inputRegistracija" id="imeR" name="imeR" placeholder="Ime"><br>
                <i class='bx bxs-user'></i>
                <input type="text" class="inputRegistracija" id="prezimeR" name="prezimeR" placeholder="Prezime"><br>
                <i class='bx bxs-envelope'></i>
                <input type="text" class="inputRegistracija" id="korImeR" name="korImeR" placeholder="Korisničko Ime"><br>
                <i class='bx bxs-envelope'></i>
                <input type="text" class="inputRegistracija" id="emailR" name="emailR" placeholder="Email"><br>
                <i class='bx bxs-calendar'></i>
                <input type="date" class="inputRegistracija" id="datumRođenja" name="datumRođenja" placeholder="Datum rođenja"><br>
                <i class='bx bxs-lock-alt'></i>
                <input type="password" class="inputRegistracija" id="lozinkaR" name="lozinkaR" placeholder="Lozinka"><br>
                <i class='bx bxs-lock-alt'></i>
                <input type="password" class="inputRegistracija" id="potvrdaLozinkeR" name="potvrdaLozinkeR" placeholder="Ponovljena Lozinka"><br>
                <input type="submit" name="registracijaButton" id="registracijaButton" value="Registriraj se">

            </form><br>
            <div class="imaRacun">
                <p class="istiRed">Već ste član?</p><a href="index.php">
                    <p class="istiRed">Prijavite se</p>
                </a>
            </div>
        </div>
    </div>
</body>


</html>
