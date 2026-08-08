<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Lote;
use App\Model\Entity\Usuario;
use App\Model\Table\LotesTable;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use DateTimeImmutable;
use DomainException;

final class LoteService
{
    /**
     * Create the lot service.
     *
     * @param \App\Model\Table\LotesTable $lotes Lot repository.
     */
    public function __construct(private readonly LotesTable $lotes)
    {
    }

    /** @param array<string, mixed> $data Validated lot data. */
    public function criar(array $data, Usuario $responsavel): Lote
    {
        $validade = new DateTimeImmutable((string)($data['data_validade'] ?? ''));
        if ($validade < new DateTimeImmutable('today')) {
            throw new DomainException('Não é permitido cadastrar lote vencido.');
        }
        $data['usuario_id'] = $responsavel->id;
        $data['quantidade_disponivel'] = $data['quantidade_recebida'] ?? 0;
        $data['data_recebimento'] ??= new DateTimeImmutable();

        return ConnectionManager::get('default')->transactional(function () use ($data, $responsavel): Lote {
            $lote = $this->lotes->newEntity($data);
            $this->lotes->saveOrFail($lote);
            $this->auditar($lote, $responsavel, 'criacao_lote');

            return $lote;
        });
    }

    /** Block or recall an existing lot with a mandatory reason. */
    public function alterarBloqueio(Lote $lote, Usuario $responsavel, bool $recolher, string $motivo): Lote
    {
        if (trim($motivo) === '') {
            throw new DomainException('Informe o motivo do bloqueio ou recolhimento.');
        }

        return ConnectionManager::get('default')->transactional(function () use (
            $lote,
            $responsavel,
            $recolher,
            $motivo,
        ): Lote {
            $lote->bloqueado = true;
            $lote->recolhido = $recolher;
            $lote->motivo_bloqueio = $motivo;
            $this->lotes->saveOrFail($lote);
            $this->auditar($lote, $responsavel, $recolher ? 'recolhimento_lote' : 'bloqueio_lote');

            return $lote;
        });
    }

    /** Persist a lot audit record. */
    private function auditar(Lote $lote, Usuario $responsavel, string $acao): void
    {
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $auditorias->saveOrFail($auditorias->newEntity([
            'usuario_id' => $responsavel->id,
            'acao' => $acao,
            'modulo' => 'lotes',
            'registro_id' => $lote->id,
            'dados_posteriores' => $lote->toArray(),
        ]));
    }
}
