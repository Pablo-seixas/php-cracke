<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Prescrico Entity
 *
 * @property int $id
 * @property int $atendimento_id
 * @property int $profissional_id
 * @property string $conteudo
 * @property \Cake\I18n\Date|null $validade
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Atendimento $atendimento
 * @property \App\Model\Entity\Profissional $profissional
 */
class Prescrico extends Entity
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
        'atendimento_id' => true,
        'profissional_id' => true,
        'conteudo' => true,
        'validade' => true,
        'created' => true,
        'modified' => true,
        'atendimento' => true,
        'profissional' => true,
    ];
}
