<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FornecedoresProduto Entity
 *
 * @property int $id
 * @property int $fornecedor_id
 * @property int|null $produto_id
 * @property int|null $medicamento_id
 * @property string|null $codigo_fornecedor
 * @property string|null $ultimo_preco
 * @property int|null $prazo_entrega
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Fornecedore $fornecedor
 * @property \App\Model\Entity\Produto $produto
 * @property \App\Model\Entity\Medicamento $medicamento
 */
class FornecedoresProduto extends Entity
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
        'fornecedor_id' => true,
        'produto_id' => true,
        'medicamento_id' => true,
        'codigo_fornecedor' => true,
        'ultimo_preco' => true,
        'prazo_entrega' => true,
        'created' => true,
        'modified' => true,
        'fornecedor' => true,
        'produto' => true,
        'medicamento' => true,
    ];
}
