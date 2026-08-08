<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\EstoqueInsuficienteException;
use App\Exception\LoteBloqueadoException;
use App\Exception\MedicamentoInativoException;
use App\Exception\MedicamentoVencidoException;
use App\Model\Entity\MovimentacoesEstoque;
use App\Model\Entity\Usuario;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Date;
use Cake\ORM\TableRegistry;
use DomainException;

final class EstoqueService
{
    private const TIPOS_SAIDA = [
        'saida_venda', 'saida_manual', 'ajuste_negativo', 'transferencia',
        'avaria', 'perda', 'vencimento',
    ];

    /** Record an immutable stock movement and update lot availability atomically. */
    public function movimentar(array $dados, Usuario $responsavel): MovimentacoesEstoque
    {
        $quantidade = (float)($dados['quantidade'] ?? 0);
        if ($quantidade <= 0) {
            throw new DomainException('A quantidade movimentada deve ser maior que zero.');
        }
        $tipo = (string)($dados['tipo'] ?? '');
        $saida = in_array($tipo, self::TIPOS_SAIDA, true);
        $inventario = $tipo === 'inventario';

        return ConnectionManager::get('default')->transactional(function () use (
            $dados,
            $responsavel,
            $quantidade,
            $tipo,
            $saida,
            $inventario,
        ): MovimentacoesEstoque {
            $medicamentoId = $dados['medicamento_id'] ?? null;
            $produtoId = $dados['produto_id'] ?? null;
            if (($medicamentoId === null) === ($produtoId === null)) {
                throw new DomainException('Informe exatamente um medicamento ou produto.');
            }

            $anterior = $medicamentoId !== null
                ? $this->movimentarLote($dados, $quantidade, $saida, $inventario)
                : $this->saldoProduto((int)$produtoId);
            $posterior = $inventario ? $quantidade : ($saida ? $anterior - $quantidade : $anterior + $quantidade);
            if ($posterior < 0) {
                throw new EstoqueInsuficienteException('Estoque insuficiente para a movimentação.');
            }

            $movimentacoes = TableRegistry::getTableLocator()->get('MovimentacoesEstoque');
            $movimento = $movimentacoes->newEntity($dados + [
                'quantidade_anterior' => $anterior,
                'quantidade_posterior' => $posterior,
                'usuario_id' => $responsavel->id,
            ]);
            $movimentacoes->saveOrFail($movimento);
            $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
            $auditorias->saveOrFail($auditorias->newEntity([
                'usuario_id' => $responsavel->id,
                'acao' => $saida ? 'saida_estoque' : 'entrada_estoque',
                'modulo' => 'estoque',
                'registro_id' => $movimento->id,
                'dados_posteriores' => $movimento->toArray(),
                'endereco_ip' => $dados['endereco_ip'] ?? null,
            ]));

            return $movimento;
        });
    }

    /** Lock and validate a medication lot, returning its previous balance. */
    private function movimentarLote(array $dados, float $quantidade, bool $saida, bool $inventario): float
    {
        $loteId = $dados['lote_id'] ?? null;
        if ($loteId === null) {
            throw new DomainException('Movimentações de medicamentos exigem um lote.');
        }
        $lotes = TableRegistry::getTableLocator()->get('Lotes');
        $lote = $lotes->find()->contain(['Medicamentos'])->where(['Lotes.id' => $loteId])->epilog('FOR UPDATE')->firstOrFail();
        if ((int)$lote->medicamento_id !== (int)$dados['medicamento_id']) {
            throw new DomainException('O lote não pertence ao medicamento informado.');
        }
        if (!$lote->medicamento->ativo) {
            throw new MedicamentoInativoException('Medicamento inativo não pode ser movimentado.');
        }
        if ($saida && ($lote->bloqueado || $lote->recolhido)) {
            throw new LoteBloqueadoException('Lote bloqueado ou recolhido não pode ter saída.');
        }
        if ($saida && $lote->data_validade < Date::today()) {
            throw new MedicamentoVencidoException('Lote vencido não pode ter saída.');
        }

        $anterior = (float)$lote->quantidade_disponivel;
        $posterior = $inventario ? $quantidade : ($saida ? $anterior - $quantidade : $anterior + $quantidade);
        if ($posterior < 0) {
            throw new EstoqueInsuficienteException('Quantidade indisponível no lote selecionado.');
        }
        $lote->quantidade_disponivel = $posterior;
        $lotes->saveOrFail($lote);

        return $anterior;
    }

    /** Calculate the current product balance from its immutable ledger. */
    private function saldoProduto(int $produtoId): float
    {
        $produto = TableRegistry::getTableLocator()->get('Produtos')->get($produtoId);
        if (!$produto->ativo) {
            throw new MedicamentoInativoException('Produto inativo não pode ser movimentado.');
        }
        $movimentos = TableRegistry::getTableLocator()->get('MovimentacoesEstoque')->find()
            ->select(['tipo', 'quantidade'])
            ->where(['produto_id' => $produtoId])
            ->orderByAsc('id');
        $saldo = 0.0;
        foreach ($movimentos as $movimento) {
            $quantidade = (float)$movimento->quantidade;
            $saldo = $movimento->tipo === 'inventario'
                ? $quantidade
                : $saldo + (in_array($movimento->tipo, self::TIPOS_SAIDA, true) ? -$quantidade : $quantidade);
        }

        return $saldo;
    }
}
