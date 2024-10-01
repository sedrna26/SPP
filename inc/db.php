<?php

class Database
{
    private $hostname = "localhost";
    private $database = "spp";
    private $username = "root";
    private $password = "";
    private $charset = "utf8";

    /**
     * Se conecta a la base de datos y devuelve un objeto PDO.
     * 
     * @return La conexión a la base de datos.
     */
    function conectar()
    {
        try {
            $conexion = "mysql:host=" . $this->hostname . ";dbname=" . $this->database . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $pdo = new PDO($conexion, $this->username, $this->password, $options);

            return $pdo;
        } catch (PDOException $e) {
            echo 'Error conexion: ' . $e->getMessage();
            exit;
        }
    }
}
