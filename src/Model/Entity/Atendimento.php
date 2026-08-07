<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Atendimento Entity
 *
 * @property int $id
 * @property int|null $consulta_id
 * @property int $paciente_id
 * @property int $profissional_id
 * @property \Cake\I18n\DateTime $iniciado_em
 * @property \Cake\I18n\DateTime|null $finalizado_em
 * @property string|null $motivo
 * @property string|null $sintomas
 * @property string|null $pressao_arterial
 * @property string|null $temperatura
 * @property string|null $peso
 * @property string|null $altura
 * @property string|null $glicemia
 * @property string|null $saturacao
 * @property string|null $orientacoes
 * @property string|null $encaminhamentos
 * @property string|null $observacoes
 * @property string $status
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Consulta $consulta
 * @property \App\Model\Entity\Paciente $paciente
 * @property \App\Model\Entity\Profissional $profissional
 * @property \App\Model\Entity\Prescrico[] $prescricoes
 */
class Atendimento extends Entity
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
        'consulta_id' => true,
        'paciente_id' => true,
        'profissional_id' => true,
        'iniciado_em' => true,
        'finalizado_em' => true,
        'motivo' => true,
        'sintomas' => true,
        'pressao_arterial' => true,
        'temperatura' => true,
        'peso' => true,
        'altura' => true,
        'glicemia' => true,
        'saturacao' => true,
        'orientacoes' => true,
        'encaminhamentos' => true,
        'observacoes' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'consulta' => true,
        'paciente' => true,
        'profissional' => true,
        'prescricoes' => true,
    ];
}
