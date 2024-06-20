<?php
include 'biblioteke/provjeraIspravnostiPodatakazaPromjenuLozinke.php';
$direktorij = getcwd();
$putanja = dirname($_SERVER['REQUEST_URI']);
?>
<html lang="hr">

<head>
    <title>Promijeni lozinku</title>
    <meta charset="utf-8">
    <meta name="author" content="Marko Vresk">
    <meta name="keywords" content="Promijeni lozinku">
    <meta name="description" content="12.05.2024.">
    <link href="css/mvresk.css" rel="stylesheet" type="text/css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

    <header>
        <h2 class="naslov">Promijeni lozinku</h2>

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
                <i class='bx bxs-lock-alt'></i>
                <input type="password" class="inputRegistracija" id="lozinkaStara" name="lozinkaStara"
                    placeholder="Stara lozinka"><br>
                <i class='bx bxs-lock-alt'></i>
                <input type="password" class="inputRegistracija" id="lozinkaNova" name="lozinkaNova"
                    placeholder="Nova lozinka"><br>
                <i class='bx bxs-lock-alt'></i>
                <input type="password" class="inputRegistracija" id="potvrdaLozinkaNova" name="potvrdaLozinkaNova"
                    placeholder="Potvrdi novu lozinku"><br>
                <input type="submit" name="provjeraIspravnostiButton" id="registracijaButton" value="Spremi promjene">

            </form><br>
            <div class="imaRacun">
                <a href="mojProfil.php">
                    <p class="istiRed">Natrag</p>
                </a>
            </div>
        </div>
    </div>
</body>

</html>
<script type="text/javascript">

</script>