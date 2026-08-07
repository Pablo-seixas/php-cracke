<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProntuariosHistorico Entity
 *
 * @property int $id
 * @property int $prontuario_id
 * @property int $versao
 * @property array $dados
 * @property int $usuario_id
 * @property string|null $motivo
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\Prontuario $prontuario
 * @property \App\Model\Entity\Usuario $usuario
 */
class ProntuariosHistorico extends Entity
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
        'prontuario_id' => true,
        'versao' => true,
        'dados' => true,
        'usuario_id' => true,
        'motivo' => true,
        'created' => true,
        'prontuario' => true,
        'usuario' => true,
    ];
}
