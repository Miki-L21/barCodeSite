<?php
namespace app;
use src\Connection;
use PDO;

//require_once 'Database.php'; // Arquivo de conexão com a base de dados

class ControllerAnuncio {

    private $conn;
    private $database;

    public function __construct() {
        $this->database = new Connection();
        $this->conn = $this->database->getConnection();
    }

    public function getCompras($id) {
        $p['id']=$id;
        $lista = $this->database->getData("SELECT * FROM `produto_user` WHERE `id_user`=  :id", $p);
        echo json_encode($lista);
    }

}