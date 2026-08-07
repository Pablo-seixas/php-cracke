<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContasPagar Entity
 *
 * @property int $id
 * @property int|null $compra_id
 * @property int|null $fornecedor_id
 * @property string $descricao
 * @property string $valor
 * @property \Cake\I18n\Date $vencimento
 * @property int $parcela
 * @property int $total_parcelas
 * @property string $juros
 * @property string $multa
 * @property string $desconto
 * @property string $status
 * @property \Cake\I18n\DateTime|null $pago_em
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Compra $compra
 * @property \App\Model\Entity\Fornecedor $fornecedor
 */
class ContasPagar extends Entity
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
        'compra_id' => true,
        'fornecedor_id' => true,
        'descricao' => true,
        'valor' => true,
        'vencimento' => true,
        'parcela' => true,
        'total_parcelas' => true,
        'juros' => true,
        'multa' => true,
        'desconto' => true,
        'status' => true,
        'pago_em' => true,
        'created' => true,
        'modified' => true,
        'compra' => true,
        'fornecedor' => true,
    ];
}
