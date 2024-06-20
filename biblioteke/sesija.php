<?php
class Sesija
{

    const KORISNIK = "korisnik";
    const ULOGA = "uloga";
    const SESSION_NAME = "prijava_sesija";

    static function kreirajSesiju()
    {
        if (session_id() == "") {
            session_name(self::SESSION_NAME);
            session_start();
        }
    }

    static function kreirajKorisnika($korisnik, $uloga = 4)
    {
        self::kreirajSesiju();
        $_SESSION[self::KORISNIK] = $korisnik;
        $_SESSION[self::ULOGA] = $uloga;
    }


}
?>