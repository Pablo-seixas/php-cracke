<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContasReceber Entity
 *
 * @property int $id
 * @property int|null $venda_id
 * @property int|null $paciente_id
 * @property string $descricao
 * @property string $valor
 * @property \Cake\I18n\Date $vencimento
 * @property int $parcela
 * @property int $total_parcelas
 * @property string $juros
 * @property string $multa
 * @property string $desconto
 * @property string $status
 * @property \Cake\I18n\DateTime|null $recebido_em
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Venda $venda
 * @property \App\Model\Entity\Paciente $paciente
 */
class ContasReceber extends Entity
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
        'paciente_id' => true,
        'descricao' => true,
        'valor' => true,
        'vencimento' => true,
        'parcela' => true,
        'total_parcelas' => true,
        'juros' => true,
        'multa' => true,
        'desconto' => true,
        'status' => true,
        'recebido_em' => true,
        'created' => true,
        'modified' => true,
        'venda' => true,
        'paciente' => true,
    ];
}
