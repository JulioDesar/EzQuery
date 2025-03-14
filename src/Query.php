<?php

namespace EZQuery;

use EZQuery\Database\Connection;

class Query
{
    use Conditions;

    private $table;
    private $columns = "*";

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function select($columns = "*")
    {
        $this->columns = is_array($columns) ? implode(", ", $columns) : $columns;
        return $this;
    }

    public function findFirstOrDefault()
    {
        $sql = "SELECT {$this->columns} FROM {$this->table}";
        $sql .= $this->buildWhereClause();
        $sql .= " LIMIT 1";

        $stmt = Connection::getConnection()->prepare($sql);
        $stmt->execute($this->getWhereValues());

        return $stmt->fetch() ?: null;
    }

    public function get()
    {
        $sql = "SELECT {$this->columns} FROM {$this->table}";
        $sql .= $this->buildWhereClause();
        return $sql;
        // $stmt = Connection::getConnection()->prepare($sql);
        // $stmt->execute($this->getWhereValues());

        // return $stmt->fetchAll();
    }
}
