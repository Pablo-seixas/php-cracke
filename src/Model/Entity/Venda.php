<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Venda Entity
 *
 * @property int $id
 * @property string $numero
 * @property int|null $cliente_id
 * @property int $usuario_id
 * @property string $status
 * @property string $subtotal
 * @property string $desconto
 * @property string $acrescimo
 * @property string $total
 * @property \Cake\I18n\DateTime|null $finalizada_em
 * @property \Cake\I18n\DateTime|null $cancelada_em
 * @property string|null $motivo_cancelamento
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Cliente $cliente
 * @property \App\Model\Entity\Usuario $usuario
 * @property \App\Model\Entity\ContasReceber[] $contas_receber
 * @property \App\Model\Entity\Pagamento[] $pagamentos
 * @property \App\Model\Entity\VendasIten[] $vendas_itens
 */
class Venda extends Entity
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
        'numero' => true,
        'cliente_id' => true,
        'usuario_id' => true,
        'status' => true,
        'subtotal' => true,
        'desconto' => true,
        'acrescimo' => true,
        'total' => true,
        'finalizada_em' => true,
        'cancelada_em' => true,
        'motivo_cancelamento' => true,
        'created' => true,
        'modified' => true,
        'cliente' => true,
        'usuario' => true,
        'contas_receber' => true,
        'pagamentos' => true,
        'vendas_itens' => true,
    ];
}
