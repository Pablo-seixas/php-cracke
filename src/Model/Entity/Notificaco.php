<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Notificaco Entity
 *
 * @property int $id
 * @property int|null $usuario_id
 * @property string $tipo
 * @property string $titulo
 * @property string $mensagem
 * @property array|null $dados
 * @property \Cake\I18n\DateTime|null $lida_em
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\Usuario $usuario
 */
class Notificaco extends Entity
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
        'usuario_id' => true,
        'tipo' => true,
        'titulo' => true,
        'mensagem' => true,
        'dados' => true,
        'lida_em' => true,
        'created' => true,
        'usuario' => true,
    ];
}
