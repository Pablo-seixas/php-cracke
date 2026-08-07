<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SolicitacoesComprasIten Entity
 *
 * @property int $id
 * @property int $solicitacao_compra_id
 * @property int|null $medicamento_id
 * @property int|null $produto_id
 * @property string $quantidade
 * @property string|null $preco_estimado
 * @property string|null $observacoes
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\SolicitacoesCompra $solicitacao_compra
 * @property \App\Model\Entity\Medicamento $medicamento
 * @property \App\Model\Entity\Produto $produto
 */
class SolicitacoesComprasIten extends Entity
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
        'solicitacao_compra_id' => true,
        'medicamento_id' => true,
        'produto_id' => true,
        'quantidade' => true,
        'preco_estimado' => true,
        'observacoes' => true,
        'created' => true,
        'modified' => true,
        'solicitacao_compra' => true,
        'medicamento' => true,
        'produto' => true,
    ];
}
