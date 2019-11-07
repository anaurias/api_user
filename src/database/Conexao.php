<?php

class Conexao extends PDO
{
    private static $instancia;
    private static $host;
    private static $username;
    private static $password;
    private static $database;
    private static $port;

    public function Conexao($dsn, $username = "", $password = "")
    {

        parent::__construct($dsn, $username, $password);
    }

    public static function getInstance()
    {

        if (!isset(self::$instancia)) {
            try {
                self::$host = "localhost";
                self::$username = "postgres";
                self::$password = "docker";
                self::$database = "gobarber";
                self::$port = "5433";

                self::$instancia = new Conexao("pgsql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$database . "", self::$username, self::$password);
            } catch (PDOException $e) {
                echo "Erro ao conectar com base de dados" . $e->getMessage();
                exit();
            }
        }

        return self::$instancia;
    }

}
