<?php
 
class DB
{
    public function connect(): PDO
    {
        $conn = new PDO('mysql:host=213.171.200.36;dbname=kljefferson', 'kljefferson', 'Password20*');
        $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
        return $conn;
    }
}