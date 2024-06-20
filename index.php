<?php
include 'biblioteke/provjeraPrijava.php';
$direktorij = getcwd();
$putanja = dirname($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang="hr">

<head>
    <title>Prijava</title>
    <meta charset="utf-8">
    <meta name="author" content="Marko Vresk">
    <meta name="keywords" content="Prijava">
    <meta name="description" content="15.02.2024.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/mvresk.css" rel="stylesheet" type="text/css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <header>
        <h2 class="naslov">Prijava</h2>

    </header>


    <div id="sadrzaj">
        <div id="formaPrijava">
            <h1>Prijava</h1>
            <div id="poruke"> <?php
            if (isset($poruke)) {
                echo "<p>$poruke</p>";
            }
            ?></div>
            <div id="prijavaRegistracija">
                <p id="prijavaPromjena"> <a href="index.php">Prijava</a></p>
                <p id="registracijaPromjena"><a href="registracija.php"> Registracija</a></p>
            </div>
            <form id="prijava" method="post" name="prijava"
                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <i class='bx bxs-user'></i>
                <input type="text" class="inputPrijava" name="emailP" placeholder="Korisničko ime"
                    value="<?php if (filter_input(INPUT_COOKIE, 'email')) {
                        echo filter_input(INPUT_COOKIE, 'email');
                    } ?>"><br>
                <i class='bx bxs-lock-alt'></i>
                <input type="password" class="inputPrijava" name="lozinkaP" placeholder="Lozinka"><br>
                <input type="submit" name="prijavaButton" id="prijavaButton" value="Prijavi se">
                <p id="nemaRačuna"> Nemaš račun? <a href="registracija.php">Registriraj se </a></p>
            </form><br>

        </div>
    </div>
</body>

</html>