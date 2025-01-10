<?php

namespace system\core;

use system\core\DbConection;

abstract class LogDbModel
{
    protected $erro;        // Armazena erros ocorridos durante operações
    protected $tabela;      // Nome da tabela do banco de dados

    public function __construct(string $tabela)
    {
        $this->tabela = $tabela;
    }

    private function dataFilter(array $dataSet)
    {
        $filter = [];

        foreach ($dataSet as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }

        return $filter;
    }

    public function validateLogData(array $data): array
    {

        $errors = [];

        if (empty($data['request_method'])) {
            $errors[] = 'request_method is required';
        }

        if (empty($data['endpoint'])) {
            $errors[] = 'endpoint is required';
        }

        if (empty($data['response_code'])) {
            $errors[] = 'response_code is required';
        }

        return $errors;

    }

    protected function register(array $data)
    {

        try {
            $columns = implode(',', array_keys($data));  // Exemplo: 'timestamp, request_method, endpoint, response_code'
            $values = ':' . implode(',:', array_keys($data));  // Exemplo: ':timestamp, :request_method, :endpoint'

            $query = "INSERT INTO " . $this->tabela . "({$columns}) VALUES ({$values})";

            $stmt = DbConection::getInstance()->prepare($query);
            $stmt->execute($this->dataFilter($data));

            return DbConection::getInstance()->lastInsertId();
        } catch (\PDOException $ex) {
            $this->erro = $ex->getCode();
            return null;
        }
    }


    public function save(array $data)
    {
        return $this->register($data);
    }


}