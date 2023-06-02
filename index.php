<?php

require "./config.php";
require "helpers/banco.php";
require "helpers/ajudantes.php";
require "./models/Tarefa.php";
require "./models/Anexo.php";
require "./models/RepositorioTarefas.php";

$repositorio_tarefas = new RepositorioTarefas($pdo);

// definindo o arquivo (rota) que deverá ser usado para tratar a requisição.
$rota = "tarefas";

if (array_key_exists("rota", $_GET)) {
    $rota = (string) $_GET["rota"];
}

// Incluir o arquivo que vai tratar a requisição.
if (is_file("controllers/{$rota}.php")) {
    require "controllers/{$rota}.php";
} else {
    echo "Rota não encontrada";
}
