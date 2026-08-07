<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CotacoesIten Entity
 *
 * @property int $id
 * @property int $cotacao_id
 * @property int $solicitacao_compra_item_id
 * @property string $quantidade
 * @property string $preco_unitario
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Cotaco $cotacao
 * @property \App\Model\Entity\SolicitacaoCompraItem $solicitacao_compra_item
 */
class CotacoesIten extends Entity
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
        'cotacao_id' => true,
        'solicitacao_compra_item_id' => true,
        'quantidade' => true,
        'preco_unitario' => true,
        'created' => true,
        'modified' => true,
        'cotacao' => true,
        'solicitacao_compra_item' => true,
    ];
}
