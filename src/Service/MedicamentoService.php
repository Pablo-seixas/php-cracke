<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Medicamento;
use App\Model\Entity\Usuario;
use App\Model\Table\MedicamentosTable;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use DateTimeImmutable;

final class MedicamentoService
{
    /**
     * Create the medicine service.
     *
     * @param \App\Model\Table\MedicamentosTable $medicamentos Medicine repository.
     */
    public function __construct(private readonly MedicamentosTable $medicamentos)
    {
    }

    /** @param array<string, mixed> $data Validated form data. */
    public function salvar(Medicamento $medicamento, array $data, Usuario $responsavel): Medicamento
    {
        return ConnectionManager::get('default')->transactional(function () use (
            $medicamento,
            $data,
            $responsavel,
        ): Medicamento {
            $acao = $medicamento->isNew() ? 'criacao' : 'alteracao';
            $antes = $medicamento->isNew() ? null : $medicamento->toArray();
            $medicamento = $this->medicamentos->patchEntity($medicamento, $data);
            $this->medicamentos->saveOrFail($medicamento);
            $this->auditar($responsavel, $acao, $medicamento, $antes);

            return $medicamento;
        });
    }

    /**
     * Soft-delete a medicine and audit the operation.
     *
     * @param \App\Model\Entity\Medicamento $medicamento Medicine to inactivate.
     * @param \App\Model\Entity\Usuario $responsavel Authenticated operator.
     * @return void
     */
    public function inativar(Medicamento $medicamento, Usuario $responsavel): void
    {
        ConnectionManager::get('default')->transactional(function () use ($medicamento, $responsavel): void {
            $antes = $medicamento->toArray();
            $medicamento->ativo = false;
            $medicamento->deleted = new DateTimeImmutable();
            $this->medicamentos->saveOrFail($medicamento);
            $this->auditar($responsavel, 'inativacao', $medicamento, $antes);
        });
    }

    /** @param array<string, mixed>|null $antes Previous entity data. */
    private function auditar(
        Usuario $responsavel,
        string $acao,
        Medicamento $medicamento,
        ?array $antes,
    ): void {
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $auditorias->saveOrFail($auditorias->newEntity([
            'usuario_id' => $responsavel->id,
            'acao' => $acao,
            'modulo' => 'medicamentos',
            'registro_id' => $medicamento->id,
            'dados_anteriores' => $antes,
            'dados_posteriores' => $medicamento->toArray(),
        ]));
    }
}
