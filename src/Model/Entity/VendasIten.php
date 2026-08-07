<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VendasIten Entity
 *
 * @property int $id
 * @property int $venda_id
 * @property int|null $medicamento_id
 * @property int|null $produto_id
 * @property int|null $lote_id
 * @property int|null $receita_id
 * @property string $quantidade
 * @property string $preco_unitario
 * @property string $desconto
 * @property string $total
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Venda $venda
 * @property \App\Model\Entity\Medicamento $medicamento
 * @property \App\Model\Entity\Produto $produto
 * @property \App\Model\Entity\Lote $lote
 * @property \App\Model\Entity\Receita $receita
 */
class VendasIten extends Entity
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
        'venda_id' => true,
        'medicamento_id' => true,
        'produto_id' => true,
        'lote_id' => true,
        'receita_id' => true,
        'quantidade' => true,
        'preco_unitario' => true,
        'desconto' => true,
        'total' => true,
        'created' => true,
        'modified' => true,
        'venda' => true,
        'medicamento' => true,
        'produto' => true,
        'lote' => true,
        'receita' => true,
    ];
}
