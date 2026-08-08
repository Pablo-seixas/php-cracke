<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Fornecedore;
use App\Model\Entity\Usuario;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use DateTimeImmutable;

final class FornecedorService
{
    /** Create or update a supplier with normalized documents and audit. */
    public function salvar(Fornecedore $fornecedor, array $dados, Usuario $responsavel): Fornecedore
    {
        $dados['cnpj'] = preg_replace('/\D/', '', (string)($dados['cnpj'] ?? ''));

        return ConnectionManager::get('default')->transactional(function () use (
            $fornecedor,
            $dados,
            $responsavel,
        ): Fornecedore {
            $table = TableRegistry::getTableLocator()->get('Fornecedores');
            $acao = $fornecedor->isNew() ? 'criacao' : 'alteracao';
            $antes = $fornecedor->isNew() ? null : $fornecedor->toArray();
            $fornecedor = $table->patchEntity($fornecedor, $dados);
            $table->saveOrFail($fornecedor);
            $this->auditar($responsavel, $acao, $fornecedor, $antes);

            return $fornecedor;
        });
    }

    /** Soft-delete a supplier without losing purchase history. */
    public function inativar(Fornecedore $fornecedor, Usuario $responsavel): void
    {
        ConnectionManager::get('default')->transactional(function () use ($fornecedor, $responsavel): void {
            $table = TableRegistry::getTableLocator()->get('Fornecedores');
            $antes = $fornecedor->toArray();
            $fornecedor->ativo = false;
            $fornecedor->deleted = new DateTimeImmutable();
            $table->saveOrFail($fornecedor);
            $this->auditar($responsavel, 'inativacao', $fornecedor, $antes);
        });
    }

    /** Persist a supplier audit record. */
    private function auditar(Usuario $usuario, string $acao, Fornecedore $fornecedor, ?array $antes): void
    {
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $auditorias->saveOrFail($auditorias->newEntity([
            'usuario_id' => $usuario->id, 'acao' => $acao, 'modulo' => 'fornecedores',
            'registro_id' => $fornecedor->id, 'dados_anteriores' => $antes,
            'dados_posteriores' => $fornecedor->toArray(),
        ]));
    }
}
