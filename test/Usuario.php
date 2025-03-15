<?php

require_once __DIR__ . '/../vendor/autoload.php';

use \EZQuery\Query;

class Usuario
{
    public function buscar()
    {
        $query = new Query("tb_usuarios");
        $query->where("name", "value")
            ->where(column: function ($q) {
                $q->where("name", "value")
                  ->where("email", "value", boolean: "OR");
            }, boolean: "OR");
        return $query->get();
    }

    public function buscarPorId($id)
    {
        $query = new Query("tb_usuarios");
        return $query->where('id', $id)->findFirstOrDefault();
    }

    public function buscarPorEmail($email)
    {
        $query = new Query("tb_usuarios");
        return $query->where('email', $email)->findFirstOrDefault();
    }

    public function buscarPorNome($nome)
    {
        $query = new Query("tb_usuarios");
        return $query->select(['id', 'name', 'email'])->where('name', $nome)->get();
    }
}

$usuario = new Usuario();
print_r($usuario->buscar() . PHP_EOL);
print_r($usuario->buscarPorId('2') . PHP_EOL);
print_r($usuario->buscarPorEmail('test@gmail.com') . PHP_EOL);
print_r($usuario->buscarPorNome('test name'));
