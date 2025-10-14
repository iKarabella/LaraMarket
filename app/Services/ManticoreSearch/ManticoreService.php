<?php

namespace App\Services\ManticoreSearch;

use PDO;
use Exception;

class ManticoreService
{
    private PDO $client;

    public bool $ready;

    public function __construct($host, $port)
    {
        try {
            $this->client = new PDO('mysql:host=' . $host . ';port=' . $port, '', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $this->ready = true;
        }
        catch(Exception $e){
            $this->ready = false;
        }
    }

    public function dropTable($table)
    {
        return $this->exec("DROP TABLE IF EXISTS $table");
    }

    public function createTable(string $tableName, string $fields, ?string $morphology=null, ?string $mInflixLen=null)
    {
        $sql = "CREATE TABLE IF NOT EXISTS $tableName ($fields)".($morphology==null?'':" morphology='$morphology'").($mInflixLen==null?'':' min_infix_len = \'3\'');
        return $this->exec($sql);
    }

    public function addDocuments(string $table, string $columns, string $values)
    {
        return $this->exec("INSERT INTO $table ($columns) VALUES $values");
    }

    private function exec($sql)
    {
        if (!$this->ready) return null;
        try {
            return $this->client->exec($sql);
        }
        catch(Exception $e){
            return $e;
        }
    }

    public function get(Builder $query):array
    {
        if (!$this->ready) throw new Exception('Клиент мантикоры не готов');
        
        try {            
            return [
                'found'=>$this->client->query($query->build())->fetchAll(PDO::FETCH_ASSOC),
                'meta'=>$this->client->query('SHOW META;')->fetchAll(PDO::FETCH_ASSOC)
            ];
        }
        catch(Exception $e){
            throw $e;
        }
    }
}