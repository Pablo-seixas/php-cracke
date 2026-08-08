<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Usuario;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use DateTimeImmutable;

final class CatalogoService
{
    /** Persist a catalog entity and its audit trail atomically. */
    public function salvar(Table $table, EntityInterface $entity, array $data, Usuario $responsavel): EntityInterface
    {
        return ConnectionManager::get('default')->transactional(function () use (
            $table,
            $entity,
            $data,
            $responsavel,
        ): EntityInterface {
            $acao = $entity->isNew() ? 'criacao' : 'alteracao';
            $antes = $entity->isNew() ? null : $entity->toArray();
            $entity = $table->patchEntity($entity, $data);
            $table->saveOrFail($entity);
            $this->auditar($responsavel, $acao, $table->getAlias(), $entity, $antes);

            return $entity;
        });
    }

    /** Soft-delete a catalog entity and retain its history. */
    public function inativar(Table $table, EntityInterface $entity, Usuario $responsavel): void
    {
        ConnectionManager::get('default')->transactional(function () use ($table, $entity, $responsavel): void {
            $antes = $entity->toArray();
            $entity->set('ativo', false);
            $entity->set('deleted', new DateTimeImmutable());
            $table->saveOrFail($entity);
            $this->auditar($responsavel, 'inativacao', $table->getAlias(), $entity, $antes);
        });
    }

    /** Persist a catalog audit record. */
    private function auditar(
        Usuario $responsavel,
        string $acao,
        string $modulo,
        EntityInterface $entity,
        ?array $antes,
    ): void {
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $auditorias->saveOrFail($auditorias->newEntity([
            'usuario_id' => $responsavel->id,
            'acao' => $acao,
            'modulo' => mb_strtolower($modulo),
            'registro_id' => $entity->get('id'),
            'dados_anteriores' => $antes,
            'dados_posteriores' => $entity->toArray(),
        ]));
    }
}
