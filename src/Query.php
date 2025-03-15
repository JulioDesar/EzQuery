<?php

namespace EZQuery;

class Query
{
    use TraitSqlQueryBuilder;

    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function findFirstOrDefault()
    {
        $sql = "SELECT {$this->columns} FROM {$this->table}";
        $sql .= $this->compileWheres();
        $sql .= " LIMIT 1";

        return $sql;
    }

    public function get()
    {
        $sql = "SELECT {$this->columns} FROM {$this->table}";
        $sql .= $this->compileWheres();

        return $sql;
    }
}
