<?php

class RepositorioTarefas
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function salvar(Tarefa $tarefa)
    {
        $nome = strip_tags(
            $this->pdo->escape_string(
                $tarefa->getNome()
            )
        );
        $descricao = strip_tags(
            $this->pdo->escape_string(
                $tarefa->getDescricao()
            )
        );
        $prioridade = $tarefa->getPrioridade();
        $prazo = $tarefa->getPrazo();
        $concluida = ($tarefa->getConcluida()) ? 1 : 0;

        if (is_object($prazo)) {
            $prazo = "'{$prazo->format('Y-m-d')}'";
        } elseif ($prazo == '') {
            $prazo = 'NULL';
        } else {
            $prazo = "'{$prazo}'";
        }

        $sqlGravar = "
            INSERT INTO tarefas
            (nome, descricao, prioridade, prazo, concluida)
            VALUES
            (
                '{$nome}',
                '{$descricao}',
                {$prioridade},
                {$prazo},
                {$concluida}
            )
        ";

        $this->pdo->query($sqlGravar);
    }

    public function atualizar(Tarefa $tarefa)
    {
        $id = $tarefa->getId();

        $nome = strip_tags(
            $this->pdo->escape_string(
                $tarefa->getNome()
            )
        );
        $descricao = strip_tags(
            $this->pdo->escape_string(
                $tarefa->getDescricao()
            )
        );
        $prioridade = $tarefa->getPrioridade();
        $prazo = $tarefa->getPrazo();
        $concluida = ($tarefa->getConcluida()) ? 1 : 0;

        if (is_object($prazo)) {
            $prazo = "'{$prazo->format('Y-m-d')}'";
        } elseif ($prazo == '') {
            $prazo = 'NULL';
        } else {
            $prazo = "'{$prazo}'";
        }

        $sqlEditar = "
            UPDATE tarefas SET
                nome = '{$nome}',
                descricao = '{$descricao}',
                prioridade = {$prioridade},
                prazo = {$prazo},
                concluida = {$concluida}
            WHERE id = {$id}
        ";

        $this->pdo->query($sqlEditar);
    }

    public function buscar($tarefa_id = 0): Tarefa|array
    {
        if ($tarefa_id > 0) {
            return $this->buscar_tarefa($tarefa_id);
        } else {
            return $this->buscar_tarefas();
        }
    }

    private function buscar_tarefas(): array
    {
        $sqlBusca = 'SELECT * FROM tarefas';
        $resultado = $this->pdo->query(
            $sqlBusca,
            PDO::FETCH_CLASS,
            'Tarefa'
        );

        $tarefas = [];

        foreach ($resultado as $tarefa) {
            $tarefa->setAnexos($this->buscar_anexos(
                $tarefa->getId()
            ));
        }

        $tarefas[] = $tarefa;

        return $tarefas;
    }

    private function buscar_tarefa($id): Tarefa
    {
        $id = $this->pdo->escape_string($id);
        $sqlBusca = 'SELECT * FROM tarefas WHERE id = ' . $id;
        $resultado = $this->pdo->query($sqlBusca);

        $tarefa = $resultado->fetch_object('Tarefa');
        $tarefa->setAnexos($this->buscar_anexos($tarefa->getId()));

        return $tarefa;
    }

    public function salvar_anexo(Anexo $anexo)
    {
        $nome = strip_tags(
            $this->pdo->escape_string(
                $anexo->getNome()
            )
        );
        $arquivo = strip_tags(
            $this->bd->escape_string(
                $anexo->getArquivo()
            )
        );

        $sqlGravar = "INSERT INTO anexos
            (tarefa_id, nome, arquivo)
            VALUES
            (
                {$anexo->getTarefaId()},
                '{$nome}',
                '{$arquivo}'
            )
            ";

        $this->bd->query($sqlGravar);
    }

    public function buscar_anexos($tarefa_id): array
    {
        $sqlBusca = "SELECT * FROM
         anexos WHERE tarefa_id = :$tarefa_id";

        $query = $this->pdo->prepare($sqlBusca);

        $query->execute([
            "tarefa_id" => $tarefa_id,
        ]);

        $anexos = [];

        while ($anexo = $query->fetchObject('Anexo')) {
            $anexos[] = $anexo;
        }

        return $anexos;
    }

    public function buscar_anexo(int $anexo_id): Anexo
    {
        $anexo_id = $this->bd->escape_string($anexo_id);
        $sqlBusca = "SELECT * FROM anexos WHERE id = {$anexo_id}";
        $resultado = $this->bd->query($sqlBusca);

        return $resultado->fetch_object('Anexo');
    }

    public function remover(int $id)
    {
        $id = $this->bd->escape_string($id);
        $sqlRemover = "DELETE FROM tarefas WHERE id = {$id}";

        $this->bd->query($sqlRemover);
    }

    public function remover_anexo($id)
    {
        $id = $this->bd->escape_string($id);
        $sqlRemover = "DELETE FROM anexos WHERE id = {$id}";

        $this->bd->query($sqlRemover);
    }
}
