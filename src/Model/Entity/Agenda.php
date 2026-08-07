<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Agenda Entity
 *
 * @property int $id
 * @property int $profissional_id
 * @property \Cake\I18n\DateTime $inicio
 * @property \Cake\I18n\DateTime $fim
 * @property string $tipo
 * @property string|null $motivo
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Profissional $profissional
 * @property \App\Model\Entity\Consulta[] $consultas
 */
class Agenda extends Entity
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
        'profissional_id' => true,
        'inicio' => true,
        'fim' => true,
        'tipo' => true,
        'motivo' => true,
        'created' => true,
        'modified' => true,
        'profissional' => true,
        'consultas' => true,
    ];
}
