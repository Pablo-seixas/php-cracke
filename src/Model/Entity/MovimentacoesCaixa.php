<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MovimentacoesCaixa Entity
 *
 * @property int $id
 * @property int $caixa_id
 * @property int|null $pagamento_id
 * @property int $usuario_id
 * @property string $tipo
 * @property string $valor
 * @property string $motivo
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\Caixa $caixa
 * @property \App\Model\Entity\Pagamento $pagamento
 * @property \App\Model\Entity\Usuario $usuario
 */
class MovimentacoesCaixa extends Entity
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
        'caixa_id' => true,
        'pagamento_id' => true,
        'usuario_id' => true,
        'tipo' => true,
        'valor' => true,
        'motivo' => true,
        'created' => true,
        'caixa' => true,
        'pagamento' => true,
        'usuario' => true,
    ];
}
