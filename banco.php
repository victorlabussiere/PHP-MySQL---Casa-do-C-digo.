<?php

// DB Connection.
$bdServidor = '127.0.0.1';
$bdUsuario = 'sistematarefas';
$bdSenha = 'sistema';
$bdBanco = 'tarefas';

$mysqli = mysqli_connect($bdServidor, $bdUsuario, $bdSenha, $bdBanco);


try {
    $pdo = new PDO(BD_DSN, BD_USUARIO, BD_SENHA);
} catch (PDOException $e) {
    echo "Falha na conexão com o banco de dados: "
        . $e->getMessage();

    die();
}

// Starting db connection
if (!$mysqli) {
    mysqli_connect_errno();
    echo "Problemas para conectar no banco. Erro: ";
    echo mysqli_connect_error();
    die();
}

// Get entire db data
function buscar_tarefas($mysqli)
{
    $sqlBusca = 'SELECT * FROM tarefas';

    $resultado = mysqli_query(
        $mysqli,
        $sqlBusca
    );

    $tarefas = [];
    while ($tarefa = mysqli_fetch_assoc($resultado)) $tarefas[] = $tarefa;
    return $tarefas;
};

// Creating data
function gravar_tarefa($mysqli, $tarefa)
{
    if (!$tarefa) return header('Location: tarefas.php');

    $sqlGravar = "
        INSERT INTO tarefas
        (nome, descricao, prazo, prioridade, concluida)
        VALUES
        (
            '{$tarefa['nome']}',
            '{$tarefa['descricao']}',
            '{$tarefa['prazo']}',
            {$tarefa['prioridade']},
            '{$tarefa['concluida']}'
        )
    ";
    mysqli_query($mysqli, $sqlGravar);

    return header('Location: tarefas.php');
}

// Reading a single data
function buscar_tarefa($id)
{
    $sqlBusca = "SELECT * FROM tarefas WHERE id = " . $id;
    $bdServidor = '127.0.0.1';
    $bdUsuario = 'sistematarefas';
    $bdSenha = 'sistema';
    $bdBanco = 'tarefas';

    $resultado = mysqli_query(mysqli_connect($bdServidor, $bdUsuario, $bdSenha, $bdBanco), $sqlBusca);
    return mysqli_fetch_assoc($resultado);
}

// Editing data
function editar_tarefa($mysqli, $tarefa)
{
    $sqlEditar = "UPDATE tarefas SET
                    nome = '{$tarefa['nome']}',
                    descricao = '{$tarefa['descricao']}',
                    prioridade = {$tarefa['prioridade']},
                    prazo = '{$tarefa['prazo']}',
                    concluida = '{$tarefa['concluida']}'
                WHERE id = {$tarefa['id']}
    ";
    mysqli_query(
        $mysqli,
        $sqlEditar
    );
}

// Deleting data
function remover_tarefa($mysqli, $id)
{
    $sqlRemover = "DELETE FROM tarefas WHERE id = {$id}";
    mysqli_query(
        $mysqli,
        $sqlRemover
    );
}

// Saving attatchment file
function gravar_anexo($mysqli, $anexo)
{
    $sqlGravar = "INSERT INTO anexos (tarefa_id, nome, arquivo)
		VALUES
			(
				{$anexo['tarefa_id']},
				'{$anexo['nome']}',
				'{$anexo['arquivo']}'
			)
	";
    mysqli_query($mysqli, $sqlGravar);
}

// Reading attatchment files
function buscar_anexos($mysqli, $tarefa_id)
{
    $sql = "SELECT * FROM anexos 
        WHERE tarefa_id = {$tarefa_id}
    ";

    $resultado = mysqli_query(mysqli_connect(BD_SERVIDOR, BD_USUARIO, BD_SENHA, BD_BANCO), $sql); // fetching data from db
    $anexos = [];

    while ($anexo = mysqli_fetch_assoc($resultado)) $anexos[] = $anexo;
    return $anexos;
}

// Getting single attatchment file
function buscar_anexo($mysqli, $id)
{
    $sqlBusca = 'SELECT	* FROM anexos WHERE	id = ' . $id;
    $resultado = mysqli_query($mysqli, $sqlBusca);
    return mysqli_fetch_assoc($resultado);
}

// Deleting attatchment file 
function remover_anexo($mysqli, $id)
{
    $sqlRemover = "DELETE FROM anexos WHERE	id = {$id}";
    mysqli_query($mysqli, $sqlRemover);
}
