<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Pagamento Entity
 *
 * @property int $id
 * @property int|null $venda_id
 * @property int|null $consulta_id
 * @property int|null $caixa_id
 * @property string $forma
 * @property string $valor
 * @property string $status
 * @property \Cake\I18n\DateTime|null $pago_em
 * @property \Cake\I18n\DateTime|null $estornado_em
 * @property string|null $referencia
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Venda $venda
 * @property \App\Model\Entity\Consulta $consulta
 * @property \App\Model\Entity\Caixa $caixa
 * @property \App\Model\Entity\MovimentacoesCaixa[] $movimentacoes_caixa
 */
class Pagamento extends Entity
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
        'consulta_id' => true,
        'caixa_id' => true,
        'forma' => true,
        'valor' => true,
        'status' => true,
        'pago_em' => true,
        'estornado_em' => true,
        'referencia' => true,
        'created' => true,
        'modified' => true,
        'venda' => true,
        'consulta' => true,
        'caixa' => true,
        'movimentacoes_caixa' => true,
    ];
}
