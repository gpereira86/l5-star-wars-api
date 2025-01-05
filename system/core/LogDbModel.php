<?php

namespace system\core;

use system\core\DbConection;

abstract class LogDbModel
{
    protected $dataSet;       // Armazena os dados do modelo
    protected $query;       // Armazena a query SQL construída
    protected $erro;        // Armazena erros ocorridos durante operações
    protected $parametros;  // Parâmetros para consultas preparadas
    protected $tabela;      // Nome da tabela do banco de dados
    protected $ordem;       // Cláusula de ordenação da query
    protected $limite;      // Cláusula de limite da query
    protected $offset;      // Cláusula de offset da query

    public function __construct(string $tabela)
    {
        $this->tabela = $tabela;
    }

    public function __set($nome, $valor)
    {
        if (empty($this->dataSet)) {
            $this->dataSet = new \stdClass();
        }

        $this->dataSet->$nome = $valor;
    }

    public function __isset($nome)
    {
        return isset($this->dataSet->$nome);
    }

    public function __get($nome)
    {
        return ($this->dataSet->$nome ?? null);
    }

    public function ordem(string $ordem)
    {
        $this->ordem = " ORDER BY {$ordem}";
        return $this;
    }

    public function limite(string $limite)
    {
        $this->limite = " LIMIT {$limite}";
        return $this;
    }

    public function offset(string $offset)
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    public function dados()
    {
        return $this->dataSet;
    }

    private function dataFilter(array $dataSet)
    {
        $filter = [];

        foreach ($dataSet as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }

        return $filter;
    }

    private function validateLogData(array $dados):bool
    {

        if (empty($array) || $array !== null){
            return false;
        }

        foreach ($dados as $key => $value) {
            if (empty($value)) {return false;}
        }

        return true;

    }

    protected function register(array $dados)
    {
        if (!$this->validateLogData($dados)) {
            return null;
        }

        try {
            $colunas = implode(',', array_keys($dados));
            $valores = ':' . implode(',:', array_keys($dados));
            $query = "INSERT INTO " . $this->tabela . "({$colunas}) VALUES ({$valores})";

            $stmt = DbConection::getInstancia()->prepare($query);
            $stmt->execute($this->dataFilter($dados));

            return DbConection::getInstancia()->lastInsertId();
        } catch (\PDOException $ex) {
            $this->erro = $ex->getCode();
            return null;
        }
    }

    public function save()
    {
        return $this->register($this->dataSet);
    }


}