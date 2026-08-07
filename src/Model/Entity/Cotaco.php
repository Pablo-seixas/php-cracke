<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Cotaco Entity
 *
 * @property int $id
 * @property int $solicitacao_compra_id
 * @property int $fornecedor_id
 * @property \Cake\I18n\Date|null $validade
 * @property int|null $prazo_entrega
 * @property string|null $condicoes_pagamento
 * @property string $valor_total
 * @property string $status
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\SolicitacaoCompra $solicitacao_compra
 * @property \App\Model\Entity\Fornecedor $fornecedor
 */
class Cotaco extends Entity
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
        'fornecedor_id' => true,
        'validade' => true,
        'prazo_entrega' => true,
        'condicoes_pagamento' => true,
        'valor_total' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'solicitacao_compra' => true,
        'fornecedor' => true,
    ];
}
