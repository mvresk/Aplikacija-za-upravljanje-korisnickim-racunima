<?php

class Baza
{

    const server = "localhost";
    const korisnik = "postgres";
    const lozinka = "marko";
    const baza = "TeorijaBazaPodataka";
    const port = "5432";

    private $veza = null;
    private $greska = '';

    function spojiDB()
    {
        $conn_string = "host=" . self::server . " port=" . self::port . " dbname=" . self::baza . " user=" . self::korisnik . " password=" . self::lozinka;
        $this->veza = pg_connect($conn_string);

        if (!$this->veza) {
            echo "Neuspješno spajanje na bazu: " . pg_last_error();
            $this->greska = pg_last_error();
        }

        return $this->veza;
    }

    function zatvoriDB()
    {
        pg_close($this->veza);
    }

    function selectDB($upit)
    {
        $rezultat = pg_query($this->veza, $upit);

        if (!$rezultat) {
            echo "Greška kod upita: {$upit} - " . pg_last_error();
            $this->greska = pg_last_error();
        }

        return $rezultat;
    }
    function updateDB($upit)
    {
        $rezultat = pg_query($this->veza, $upit);
        return $rezultat;
    }


}
?>