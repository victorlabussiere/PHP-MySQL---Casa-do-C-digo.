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

        $prazo = $tarefa->getPrazo();

        if (is_object($prazo)) {
            $prazo = $prazo->format('Y-m-d');
        }

        $sqlGravar = "
            INSERT INTO tarefas
            (nome, descricao, prioridade, prazo, concluida)
            VALUES
            (:nome, :descricao, :prioridade, :prazo, :concluida)
        ";

        $query = $this->pdo->prepare($sqlGravar);
        $query->execute([
            'nome' => strip_tags($tarefa->getNome()),
            'descricao' => strip_tags($tarefa->getDescricao()),
            'prioridade' => $tarefa->getPrioridade(),
            'prazo' => $prazo,
            'concluida' => ($tarefa->getConcluida()) ? 1 : 0,
        ]);
    }

    public function atualizar(Tarefa $tarefa)
    {
        $prazo = $tarefa->getPrazo();

        if (is_object($prazo)) {
            $prazo = $prazo->format('Y-m-d');
        }

        $sqlEditar = "
            UPDATE tarefas SET
                nome = :nome,
                descricao = :descricao,
                prioridade = :prioridade,
                prazo = :prazo,
                concluida = :concluida
            WHERE id = :id
        ";

        $query = $this->pdo->prepare($sqlEditar);
        $query->execute([
            'nome' => strip_tags($tarefa->getNome()),
            'descricao' => strip_tags($tarefa->getDescricao()),
            'prioridade' => $tarefa->getPrioridade(),
            'prazo' => $prazo,
            'concluida' => ($tarefa->getConcluida()) ? 1 : 0,
            'id' => $tarefa->getId(),
        ]);
    }

    public function buscar(int $tarefa_id = 0): Tarefa|array
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
                intval($tarefa->getId())
            ));

            $tarefas[] = $tarefa;
        }


        return $tarefas;
    }

    private function buscar_tarefa(int $id): Tarefa
    {
        $sqlBuscaTarefa = "SELECT * FROM tarefas WHERE id = :id";

        $query = $this->pdo->prepare($sqlBuscaTarefa);
        $query->execute([
            'id' => $id
        ]);

        $tarefa = $query->fetchObject('Tarefa');
        $tarefa->setAnexos($this->buscar_anexos(
            $tarefa->getId()
        ));

        return $tarefa;
    }

    public function salvar_anexo(Anexo $anexo)
    {
        $sqlGravarAnexos = "INSERT INTO anexos
            (tarefa_id, nome, arquivo)
            VALUES
            (:tarefa_id, :nome, :arquivo)    
        ";

        $query = $this->pdo->prepare($sqlGravarAnexos);
        $query->execute([
            'tarefa_id' => $anexo->getTarefaId(),
            'nome' => strip_tags($anexo->getNome()),
            'arquvio' => strip_tags($anexo->getArquivo())
        ]);
    }

    public function buscar_anexos(int $anexo_id): array
    {
        $sqlBuscaTodosAnexos = "SELECT * FROM anexos WHERE id = :id";

        $query = $this->pdo->prepare($sqlBuscaTodosAnexos);
        $query->execute([
            'id' => $anexo_id,
        ]);

        $anexos = [];

        while ($anexo = $query->fetchObject('Anexo')) {
            $anexos[] = $anexo;
        }

        return $anexos;
    }

    public function buscar_anexo(int $anexo_id): Anexo
    {
        $sqlBuscarAnexo = "SELECT * FROM anexos WHERE id = :id";
        $query = $this->pdo->prepare($sqlBuscarAnexo);
        $query->execute([
            'id' => $anexo_id
        ]);

        return $query->fetchObject('Anexo');
    }

    public function remover(int $id)
    {
        $sqlRemover = 'DELETE FROM tarefas WHERE id = :id';
        $query = $this->pdo->prepare($sqlRemover);
        $query->execute([
            'id' => $id
        ]);
    }

    public function remover_anexo($id)
    {
        $sqlRemoverAnexo = 'DELETE FROM anexos WHERE id = :id';
        $query = $this->pdo->prepare($sqlRemoverAnexo);
        $query->execute([
            'id' => $id
        ]);
    }
}
