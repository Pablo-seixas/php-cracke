<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PerfisPermisso Entity
 *
 * @property int $perfil_id
 * @property int $permissao_id
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\Perfi $perfil
 * @property \App\Model\Entity\Permissao $permissao
 */
class PerfisPermisso extends Entity
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
        'created' => true,
        'perfil' => true,
        'permissao' => true,
    ];
}
