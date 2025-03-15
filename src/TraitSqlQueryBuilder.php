<?php

namespace EZQuery;

use \Closure;

trait TraitSqlQueryBuilder
{
    private $columns = "*";
    private $wheres = [];
    private $whereConditions = [];

    /**
     * Define as colunas a serem selecionadas.
     *
     * @param string|array $columns Nome da(s) coluna(s) a ser(em) selecionada(s).
     * @return self
     */
    public function select($columns = "*")
    {
        $this->columns = is_array($columns) ? implode(", ", $columns) : $columns;
        return $this;
    }

    /**
     * Adiciona uma condição WHERE à query.
     *
     * @param string|Closure $column Nome da coluna ou um callback para agrupar condições.
     * @param string|null $operator Operador de comparação (ex: '=', '!=', '<', 'LIKE').
     * @param mixed|null $value Valor da condição.
     * @param string $boolean Operador lógico ('AND' ou 'OR').
     * @return self
     */
    public function where($column, $operator = null, $value = null, $boolean = 'AND') {        
        if ($column instanceof Closure) {
            $query = new self($this->table);
            $column($query);
            if (!empty($query->getWheres())) {
                $this->wheres[] = ['type' => 'Nested', 'query' => $query->getWheres(), 'boolean' => $boolean];
            }
        } else {
            if (func_num_args() === 2) {
                $value = $operator;
                $operator = '=';
            }
            $this->wheres[] = ['type' => 'Basic', 'column' => $column, 'operator' => $operator, 'value' => $value, 'boolean' => $boolean];
        }
        return $this;
    }

    /**
     * Retorna a lista de condições WHERE.
     *
     * @return array
     */
    private function getWheres()
    {
        return $this->wheres;
    }

    /**
     * Monta a cláusula WHERE.
     *
     * @return string
     */
    private function compileWheres()
    {
        if (empty($this->wheres)) return '';

        $sqlParts = [];
        foreach ($this->wheres as $where) {
            if ($where['type'] === 'Basic') {
                $sqlParts[] = "{$where['boolean']} {$where['column']} {$where['operator']} ?";
            } elseif ($where['type'] === 'Nested') {
                $nestedConditions = $this->compileNestedWheres($where['query']);
                $sqlParts[] = "{$where['boolean']} ({$nestedConditions})";
            }
        }

        return ' WHERE ' . ltrim(implode(' ', $sqlParts), 'AND OR');
    }

    /**
     * Compila condições WHERE aninhadas corretamente.
     *
     * @param array $nestedWheres
     * @return string
     */
    private function compileNestedWheres($nestedWheres)
    {
        $sqlParts = [];
        foreach ($nestedWheres as $where) {
            if ($where['type'] === 'Basic') {
                $sqlParts[] = "{$where['boolean']} {$where['column']} {$where['operator']} ?";
            }
        }
        return ltrim(implode(' ', $sqlParts), 'AND OR');
    }

    /**
     * Retorna a query SQL final.
     *
     * @return string
     */
    public function toSql()
    {
        $sql = "SELECT {$this->columns} FROM {$this->table}";
        $sql .= $this->compileWheres();
        return $sql;
    }
}
