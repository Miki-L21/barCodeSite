<?php

//version 1.4
// 2025/06/26 - Corrigido para PostgreSQL/Supabase

namespace src;

use PDO;

// Definir a constante fora de classes ou namespaces
define('_DEBUG', true);

class Connection
{
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Carregar configurações do env.php
        $config = require __DIR__ . '/../controller/env.php';
        
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->db_name = $config['dbname'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    public function getConnection()
    {
        $this->conn = null;
        try {
            // DSN para PostgreSQL
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch (\PDOException $exception) {
            if ($this->isDebug()) {
                echo "Erro na conexão: " . $exception->getMessage();
            }
            error_log("Database connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }

    function bindParamAuto($stmt, $param, $value)
    {
        if (is_int($value)) {
            $type = PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            $type = PDO::PARAM_BOOL;
        } elseif (is_null($value)) {
            $type = PDO::PARAM_NULL;
        } else {
            $type = PDO::PARAM_STR;
        }
        $stmt->bindParam($param, $value, $type);
    }

    public function getData($sql, $parameters = [])
    {
        try {
            // Verificar se a conexão existe
            if (!$this->conn) {
                $this->getConnection();
            }
            
            $stmt = $this->conn->prepare($sql);
            
            if (!empty($parameters)) {
                foreach ($parameters as $key => $value) {
                    $this->bindParamAuto($stmt, ':' . $key, $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            if ($this->isDebug()) {
                error_log("Database getData error: " . $e->getMessage());
            }
            return [
                'error' => true,
                'msg' => 'Erro na consulta: ' . $e->getMessage(), 
                'status' => 500
            ];
        }
    }

    public function setData($sql, $parameters = [])
    {
        try {
            // Verificar se a conexão existe
            if (!$this->conn) {
                $this->getConnection();
            }
            
            $stmt = $this->conn->prepare($sql);

            // Vincula os parâmetros, se fornecidos
            if (!empty($parameters)) {
                foreach ($parameters as $key => $value) {
                    $this->bindParamAuto($stmt, ':' . $key, $value);
                }
            }

            if ($this->isDebug()) {
                error_log("SQL: " . $sql);
                error_log("Parameters: " . json_encode($parameters));
            }

            // Executa a consulta
            $stmt->execute();

            // Obtém o número de linhas afetadas
            $affectedRows = $stmt->rowCount();

            // Verifica o tipo de operação (baseado no comando SQL)
            $operation = strtoupper(explode(' ', trim($sql))[0]);
            $response = ['status' => 200, 'operation' => $operation];

            switch ($operation) {
                case 'INSERT':
                    $response['msg'] = 'Registro inserido com sucesso.';
                    // Para PostgreSQL, usar RETURNING id ou currval() se necessário
                    $response['lastInsertId'] = $this->conn->lastInsertId();
                    break;

                case 'UPDATE':
                    $response['msg'] = $affectedRows > 0
                        ? "{$affectedRows} linha(s) atualizada(s) com sucesso."
                        : "Nenhuma linha foi atualizada. Verifique as condições.";
                    break;

                case 'DELETE':
                    $response['msg'] = $affectedRows > 0
                        ? "{$affectedRows} linha(s) apagadas com sucesso."
                        : "Nenhuma linha foi apagada. Verifique as condições.";
                    break;

                default:
                    $response['msg'] = 'Operação executada com sucesso.';
            }

            return $response;
        } catch (\PDOException $e) {
            if ($this->isDebug()) {
                error_log("Database setData error: " . $e->getMessage());
            }
            return [
                'status' => 500,
                'msg' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Método auxiliar para verificar se está em modo debug
     */
    private function isDebug()
    {
        return defined('_DEBUG') && _DEBUG === true;
    }

    public function montarSqlComParametros($sql, $params)
    {
        foreach ($params as $key => $value) {
            $placeholder = ':' . $key;
            $valor = is_numeric($value) ? $value : "'" . addslashes($value) . "'";
            $sql = str_replace($placeholder, $valor, $sql);
        }
        return $sql;
    }
}