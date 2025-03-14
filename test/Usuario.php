<?php

require_once __DIR__ . '/../vendor/autoload.php';

use \EZQuery\Query;

class Usuario
{
    private $query;

    public function __construct()
    {
        $this->query = new Query('usuarios');
    }

    public function listar()
    {
        return $this
            ->query
            ->select()
            ->whereNot('ativo', true)
            ->get();
    }

    public function buscarPorId($id)
    {
        return $this->query->where('id', $id)->findFirstOrDefault();
    }

    public function buscarPorEmail($email)
    {
        return $this->query->where('email', $email)->findFirstOrDefault();
    }

    public function buscarPorNome($nome)
    {
        return $this->query->whereLike('nome', "%$nome%")->get();
    }
}

$usuario = new Usuario();
print_r($usuario->listar());
