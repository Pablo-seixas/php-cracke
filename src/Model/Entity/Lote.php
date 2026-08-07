<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Lote Entity
 *
 * @property int $id
 * @property int $medicamento_id
 * @property int|null $fornecedor_id
 * @property string $numero_lote
 * @property \Cake\I18n\Date|null $data_fabricacao
 * @property \Cake\I18n\Date $data_validade
 * @property string $quantidade_recebida
 * @property string $quantidade_disponivel
 * @property string $preco_custo
 * @property string|null $nota_fiscal
 * @property \Cake\I18n\DateTime $data_recebimento
 * @property int $usuario_id
 * @property bool $bloqueado
 * @property bool $recolhido
 * @property string|null $motivo_bloqueio
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Medicamento $medicamento
 * @property \App\Model\Entity\Fornecedore $fornecedor
 * @property \App\Model\Entity\Usuario $usuario
 * @property \App\Model\Entity\MovimentacoesEstoque[] $movimentacoes_estoque
 * @property \App\Model\Entity\VendasIten[] $vendas_itens
 */
class Lote extends Entity
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
        'medicamento_id' => true,
        'fornecedor_id' => true,
        'numero_lote' => true,
        'data_fabricacao' => true,
        'data_validade' => true,
        'quantidade_recebida' => true,
        'quantidade_disponivel' => true,
        'preco_custo' => true,
        'nota_fiscal' => true,
        'data_recebimento' => true,
        'usuario_id' => true,
        'bloqueado' => true,
        'recolhido' => true,
        'motivo_bloqueio' => true,
        'created' => true,
        'modified' => true,
        'medicamento' => true,
        'fornecedor' => true,
        'usuario' => true,
        'movimentacoes_estoque' => true,
        'vendas_itens' => true,
    ];
}
