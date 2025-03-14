<?php

namespace EZQuery;

trait Conditions {
    private $whereConditions = [];

    public function where($column, $value) {
        return $this->whereClauseFactory($column, '=', $value);
    }

    public function whereNot($column, $value) {
        return $this->whereClauseFactory($column, '<>', $value);
    }

    public function whereIn($column, array $values) {
        return $this->whereClauseFactory($column, 'IN', $values);
    }

    public function whereNotIn($column, array $values) {
        return $this->whereClauseFactory($column, 'NOT IN', $values);
    }

    public function whereLike($column, $value) {
        return $this->whereClauseFactory($column, 'LIKE', $value);
    }

    public function whereNotLike($column, $value) {
        return $this->whereClauseFactory($column, 'NOT LIKE', $value);
    }

    private function whereClauseFactory($column, $operator, $value) {
        $this->whereConditions[] = [$column, $operator, $value];
        return $this;
    }

    private function buildWhereClause() {
        if (empty($this->whereConditions)) {
            return "";
        }
        $clauses = [];
        foreach ($this->whereConditions as $condition) {
            list($col, $op, $val) = $condition;
            if (is_array($val)) {
                $placeholders = implode(',', array_fill(0, count($val), '?'));
                $clauses[] = "$col $op ($placeholders)";
            } else {
                $clauses[] = "$col $op ?";
            }
        }
        return " WHERE " . implode(" AND ", $clauses);
    }

    private function getWhereValues() {
        $values = [];
        foreach ($this->whereConditions as $condition) {
            $values = array_merge($values, (array)$condition[2]);
        }
        return $values;
    }
}
