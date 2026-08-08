<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use DateTimeImmutable;

/**
 * Medicamento Entity
 *
 * @property int $id
 * @property int $categoria_id
 * @property int $fabricante_id
 * @property string $codigo_interno
 * @property string|null $codigo_barras
 * @property string $nome_comercial
 * @property string $principio_ativo
 * @property string|null $forma_farmaceutica
 * @property string|null $concentracao
 * @property string|null $apresentacao
 * @property string $unidade_medida
 * @property string|null $registro_sanitario
 * @property string $preco_custo
 * @property string $preco_venda
 * @property string $estoque_minimo
 * @property string $estoque_maximo
 * @property string|null $localizacao
 * @property bool $necessita_receita
 * @property string|null $tipo_receita
 * @property bool $controlado
 * @property bool $refrigerado
 * @property bool $ativo
 * @property \Cake\I18n\DateTime|null $deleted
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Categoria $categoria
 * @property \App\Model\Entity\Fabricante $fabricante
 * @property \App\Model\Entity\ComprasIten[] $compras_itens
 * @property \App\Model\Entity\FornecedoresProduto[] $fornecedores_produtos
 * @property \App\Model\Entity\Lote[] $lotes
 * @property \App\Model\Entity\MovimentacoesEstoque[] $movimentacoes_estoque
 * @property \App\Model\Entity\SolicitacoesComprasIten[] $solicitacoes_compras_itens
 * @property \App\Model\Entity\VendasIten[] $vendas_itens
 */
class Medicamento extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'categoria_id' => true,
        'fabricante_id' => true,
        'codigo_interno' => true,
        'codigo_barras' => true,
        'nome_comercial' => true,
        'principio_ativo' => true,
        'forma_farmaceutica' => true,
        'concentracao' => true,
        'apresentacao' => true,
        'unidade_medida' => true,
        'registro_sanitario' => true,
        'preco_custo' => true,
        'preco_venda' => true,
        'estoque_minimo' => true,
        'estoque_maximo' => true,
        'localizacao' => true,
        'necessita_receita' => true,
        'tipo_receita' => true,
        'controlado' => true,
        'refrigerado' => true,
        'ativo' => true,
        'deleted' => true,
        'created' => true,
        'modified' => true,
        'categoria' => true,
        'fabricante' => true,
        'compras_itens' => true,
        'fornecedores_produtos' => true,
        'lotes' => true,
        'movimentacoes_estoque' => true,
        'solicitacoes_compras_itens' => true,
        'vendas_itens' => true,
    ];

    /** Calculate sellable stock from loaded lots. */
    public function estoqueAtual(): float
    {
        $hoje = new DateTimeImmutable('today');
        $total = 0.0;
        foreach ($this->lotes ?? [] as $lote) {
            $validade = $lote->data_validade?->toNative();
            if (!$lote->bloqueado && !$lote->recolhido && $validade >= $hoje) {
                $total += (float)$lote->quantidade_disponivel;
            }
        }

        return $total;
    }

    /** Classify stock using the configured minimum and maximum. */
    public function statusEstoque(): string
    {
        $quantidade = $this->estoqueAtual();

        return match (true) {
            $quantidade <= 0 => 'sem_estoque',
            $quantidade <= (float)$this->estoque_minimo => 'critico',
            $quantidade >= (float)$this->estoque_maximo => 'elevado',
            default => 'normal',
        };
    }
}
