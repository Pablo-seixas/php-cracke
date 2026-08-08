<?php
declare(strict_types=1);

namespace App\Repository;

use Cake\Datasource\ResultSetInterface;
use Cake\I18n\Date;
use Cake\ORM\TableRegistry;

final class EstoqueRepository
{
    private const TIPOS_SAIDA = [
        'saida_venda', 'saida_manual', 'ajuste_negativo', 'transferencia',
        'avaria', 'perda', 'vencimento',
    ];

    /** Return medicines currently at or below minimum stock. */
    public function medicamentosCriticos(): array
    {
        $medicamentos = TableRegistry::getTableLocator()->get('Medicamentos')->find()
            ->contain(['Lotes' => function ($query) {
                return $query->where([
                    'Lotes.bloqueado' => false,
                    'Lotes.recolhido' => false,
                    'Lotes.data_validade >=' => Date::today(),
                ]);
            }])
            ->where(['Medicamentos.ativo' => true, 'Medicamentos.deleted IS' => null])
            ->all();

        return array_values(array_filter(
            $medicamentos->toArray(),
            fn($medicamento): bool => $medicamento->estoqueAtual() <= (float)$medicamento->estoque_minimo,
        ));
    }

    /** Return available lots expiring inside the configured alert window. */
    public function lotesProximosVencimento(int $dias = 90): ResultSetInterface
    {
        return TableRegistry::getTableLocator()->get('Lotes')->find()
            ->contain(['Medicamentos'])
            ->where([
                'Lotes.quantidade_disponivel >' => 0,
                'Lotes.recolhido' => false,
                'Lotes.data_validade >=' => Date::today(),
                'Lotes.data_validade <=' => Date::today()->addDays($dias),
            ])
            ->orderByAsc('Lotes.data_validade')
            ->all();
    }

    /** Return products at or below minimum using their immutable ledger. */
    public function produtosCriticos(): array
    {
        $produtos = TableRegistry::getTableLocator()->get('Produtos')->find()
            ->contain(['MovimentacoesEstoque' => fn($query) => $query->orderByAsc('id')])
            ->where(['Produtos.ativo' => true, 'Produtos.deleted IS' => null])
            ->all();
        $criticos = [];
        foreach ($produtos as $produto) {
            $saldo = 0.0;
            foreach ($produto->movimentacoes_estoque as $movimento) {
                $quantidade = (float)$movimento->quantidade;
                $saldo = $movimento->tipo === 'inventario'
                    ? $quantidade
                    : $saldo + (in_array($movimento->tipo, self::TIPOS_SAIDA, true) ? -$quantidade : $quantidade);
            }
            if ($saldo <= (float)$produto->estoque_minimo) {
                $criticos[] = ['produto' => $produto, 'saldo' => $saldo];
            }
        }

        return $criticos;
    }
}
