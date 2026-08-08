<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\PagamentoInvalidoException;
use App\Model\Entity\Caixa;
use App\Model\Entity\Usuario;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use DomainException;

final class CaixaService
{
    /** Open one cash register for an operator. */
    public function abrir(Usuario $usuario, float $saldoInicial, ?string $observacoes = null): Caixa
    {
        if ($saldoInicial < 0) {
            throw new PagamentoInvalidoException('O saldo inicial não pode ser negativo.');
        }
        $caixas = TableRegistry::getTableLocator()->get('Caixas');
        if ($caixas->exists(['usuario_abertura_id' => $usuario->id, 'status' => 'aberto'])) {
            throw new DomainException('O usuário já possui um caixa aberto.');
        }
        $caixa = $caixas->newEntity([
            'usuario_abertura_id' => $usuario->id,
            'status' => 'aberto',
            'saldo_inicial' => $saldoInicial,
            'aberto_em' => DateTime::now(),
            'observacoes' => $observacoes,
        ]);
        $caixas->saveOrFail($caixa);
        $this->auditar($usuario, 'abertura_caixa', $caixa);

        return $caixa;
    }

    /** Register an immutable cash supply or withdrawal. */
    public function movimentar(Caixa $caixa, Usuario $usuario, string $tipo, float $valor, string $motivo): void
    {
        if ($caixa->status !== 'aberto') {
            throw new DomainException('O caixa está fechado.');
        }
        if (!in_array($tipo, ['entrada', 'saida'], true) || $valor <= 0 || trim($motivo) === '') {
            throw new PagamentoInvalidoException('Movimentação de caixa inválida.');
        }
        $movimentos = TableRegistry::getTableLocator()->get('MovimentacoesCaixa');
        $movimento = $movimentos->newEntity([
            'caixa_id' => $caixa->id, 'usuario_id' => $usuario->id,
            'tipo' => $tipo, 'valor' => $valor, 'motivo' => $motivo,
        ]);
        $movimentos->saveOrFail($movimento);
        $this->auditar($usuario, $tipo === 'entrada' ? 'suprimento_caixa' : 'sangria_caixa', $movimento);
    }

    /** Close a register using the calculated ledger balance. */
    public function fechar(Caixa $caixa, Usuario $usuario, ?string $observacoes = null): Caixa
    {
        if ($caixa->status !== 'aberto') {
            throw new DomainException('O caixa já está fechado.');
        }

        return ConnectionManager::get('default')->transactional(function () use (
            $caixa,
            $usuario,
            $observacoes,
        ): Caixa {
            $saldo = (float)$caixa->saldo_inicial;
            $movimentos = TableRegistry::getTableLocator()->get('MovimentacoesCaixa')->find()
                ->where(['caixa_id' => $caixa->id]);
            foreach ($movimentos as $movimento) {
                $saldo += $movimento->tipo === 'entrada' ? (float)$movimento->valor : -(float)$movimento->valor;
            }
            $caixa->status = 'fechado';
            $caixa->saldo_final = $saldo;
            $caixa->usuario_fechamento_id = $usuario->id;
            $caixa->fechado_em = DateTime::now();
            $caixa->observacoes = trim(($caixa->observacoes ?: '') . "\n" . ($observacoes ?: ''));
            TableRegistry::getTableLocator()->get('Caixas')->saveOrFail($caixa);
            $this->auditar($usuario, 'fechamento_caixa', $caixa);

            return $caixa;
        });
    }

    /** Persist a cash audit record. */
    private function auditar(Usuario $usuario, string $acao, object $registro): void
    {
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $auditorias->saveOrFail($auditorias->newEntity([
            'usuario_id' => $usuario->id, 'acao' => $acao, 'modulo' => 'caixa',
            'registro_id' => $registro->id, 'dados_posteriores' => $registro->toArray(),
        ]));
    }
}
