<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Produto Entity
 *
 * @property int $id
 * @property int $categoria_id
 * @property int|null $fabricante_id
 * @property string $codigo_interno
 * @property string|null $codigo_barras
 * @property string $nome
 * @property string $unidade_medida
 * @property string $preco_custo
 * @property string $preco_venda
 * @property string $estoque_minimo
 * @property string $estoque_maximo
 * @property bool $ativo
 * @property \Cake\I18n\DateTime|null $deleted
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Categoria $categoria
 * @property \App\Model\Entity\Fabricante $fabricante
 * @property \App\Model\Entity\ComprasIten[] $compras_itens
 * @property \App\Model\Entity\MovimentacoesEstoque[] $movimentacoes_estoque
 * @property \App\Model\Entity\SolicitacoesComprasIten[] $solicitacoes_compras_itens
 * @property \App\Model\Entity\VendasIten[] $vendas_itens
 * @property \App\Model\Entity\Fornecedore[] $fornecedores
 */
class Produto extends Entity
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
        'nome' => true,
        'unidade_medida' => true,
        'preco_custo' => true,
        'preco_venda' => true,
        'estoque_minimo' => true,
        'estoque_maximo' => true,
        'ativo' => true,
        'deleted' => true,
        'created' => true,
        'modified' => true,
        'categoria' => true,
        'fabricante' => true,
        'compras_itens' => true,
        'movimentacoes_estoque' => true,
        'solicitacoes_compras_itens' => true,
        'vendas_itens' => true,
        'fornecedores' => true,
    ];
}
