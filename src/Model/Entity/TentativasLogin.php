<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TentativasLogin Entity
 *
 * @property int $id
 * @property int|null $usuario_id
 * @property string $identificador
 * @property bool $sucesso
 * @property string|null $endereco_ip
 * @property string|null $user_agent
 * @property string|null $motivo
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\Usuario $usuario
 */
class TentativasLogin extends Entity
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
        'identificador' => true,
        'sucesso' => true,
        'endereco_ip' => true,
        'user_agent' => true,
        'motivo' => true,
        'created' => true,
        'usuario' => true,
    ];
}
