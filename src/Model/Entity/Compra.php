<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Compra Entity
 *
 * @property int $id
 * @property int|null $solicitacao_compra_id
 * @property int $fornecedor_id
 * @property string $numero
 * @property string|null $nota_fiscal
 * @property string $status
 * @property string $valor_total
 * @property \Cake\I18n\DateTime $emitida_em
 * @property \Cake\I18n\DateTime|null $recebida_em
 * @property int $usuario_id
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\SolicitacaoCompra $solicitacao_compra
 * @property \App\Model\Entity\Fornecedor $fornecedor
 * @property \App\Model\Entity\Usuario $usuario
 * @property \App\Model\Entity\ComprasIten[] $compras_itens
 * @property \App\Model\Entity\ContasPagar[] $contas_pagar
 */
class Compra extends Entity
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
        'numero' => true,
        'nota_fiscal' => true,
        'status' => true,
        'valor_total' => true,
        'emitida_em' => true,
        'recebida_em' => true,
        'usuario_id' => true,
        'created' => true,
        'modified' => true,
        'solicitacao_compra' => true,
        'fornecedor' => true,
        'usuario' => true,
        'compras_itens' => true,
        'contas_pagar' => true,
    ];
}
