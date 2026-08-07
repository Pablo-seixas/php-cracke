<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Prontuario Entity
 *
 * @property int $id
 * @property int $paciente_id
 * @property string|null $historico_clinico
 * @property string|null $alergias
 * @property string|null $medicamentos_uso
 * @property string|null $observacoes
 * @property int $versao
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Paciente $paciente
 * @property \App\Model\Entity\ProntuariosHistorico[] $prontuarios_historicos
 */
class Prontuario extends Entity
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
        'paciente_id' => true,
        'historico_clinico' => true,
        'alergias' => true,
        'medicamentos_uso' => true,
        'observacoes' => true,
        'versao' => true,
        'created' => true,
        'modified' => true,
        'paciente' => true,
        'prontuarios_historicos' => true,
    ];
}
