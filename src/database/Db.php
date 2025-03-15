<?php

namespace EZQuery\Database;

class Db
{
    private ?\mysqli $mysqli = null;
    private ?\mysqli_stmt $stmt = null;
    private ?\mysqli_result $result = null;

    public function executeAll(string $query, &...$args)
    {
        try {
            $this->execute($query, ...$args);
            if (!$this->result->num_rows)
                return [];

            return $this->fetchAll();
        } finally {
            $this->close();
        }
    }

    public function executeRow(string $query, &...$args)
    {
        try {
            $this->execute($query, ...$args);
            if (!$this->result->num_rows)
                return [];

            return $this->fetchArray();
        } finally {
            $this->close();
        }
    }

    public function executeScalar(string $query, &...$args)
    {
        try {
            $this->execute($query, ...$args);
            if (!$this->result->num_rows || !$this->result->field_count)
                return null;

            return $this->fetchArray()[0];
        } finally {
            $this->close();
        }
    }

    public function execute(string $query, &...$args)
    {
        $this->connect();

        if (count($args)) $this->executeStmt($query, $args);
        else $this->result = $this->mysqli->query($query);

        return $this;
    }

    private function executeStmt(string $query, array $args)
    {
        if (!($this->stmt = mysqli_prepare($this->mysqli, $query))) {
            $this->stmt = null;
            throw new \Exception(mysqli_error($this->mysqli));
        }

        $this->stmt->bind_param($this->getParamsType($args), ...$args);
        $this->stmt->execute();

        if ($this->stmt->error) throw new \Exception($this->stmt->error);
        $this->result = $this->stmt->get_result();
    }

    public function fetchArray()
    {
        if ($this->result === null || !$this->result->num_rows)
            return null;

        if ($this->stmt !== null)
            return $this->result->fetch_assoc();

        return $this->result->fetch_array();
    }

    public function fetchAll()
    {
        $rows = [];

        while ($row = $this->fetchArray())
            $rows[] = $row;

        return $rows;
    }

    private function getParamsType($params)
    {
        $types = '';

        foreach ($params as $param) {
            $types .= $this->getParamType($param);
        }

        return $types;
    }

    private function getParamType($value)
    {
        if (is_string($value))  return 's'; // string
        if (is_int($value))  return 'i'; // integer
        if (is_float($value) || is_double($value))  return 'd'; // double
        // You may need to handle NULL values separately, as mysqli_stmt_bind_param does not support null directly.
        if (is_null($value)) return 's'; // Treating null as string for safety, handle separately in your query logic.

        return 'b'; // blob
    }

    public function connect()
    {
        if ($this->mysqli)
            $this->close();

        if ($this->mysqli = mysqli_connect($host, $user, $password, $database))
            return $this;

        $error = mysqli_connect_error();
        $this->mysqli = null;
        throw new \Exception($error);
    }

    public function close()
    {
        if (!$this->mysqli)
            return;

        if ($this->stmt !== null)
            $this->stmt->close();

        $this->mysqli->close();
        $this->mysqli = null;
    }
}
