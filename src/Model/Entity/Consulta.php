<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Consulta Entity
 *
 * @property int $id
 * @property int $paciente_id
 * @property int $profissional_id
 * @property int|null $agenda_id
 * @property \Cake\I18n\DateTime $inicio
 * @property \Cake\I18n\DateTime $fim
 * @property string $status
 * @property string|null $motivo
 * @property \Cake\I18n\DateTime|null $confirmado_em
 * @property \Cake\I18n\DateTime|null $cancelado_em
 * @property string|null $observacoes
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Paciente $paciente
 * @property \App\Model\Entity\Profissional $profissional
 * @property \App\Model\Entity\Agenda $agenda
 * @property \App\Model\Entity\Atendimento $atendimento
 * @property \App\Model\Entity\Pagamento[] $pagamentos
 */
class Consulta extends Entity
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
        'profissional_id' => true,
        'agenda_id' => true,
        'inicio' => true,
        'fim' => true,
        'status' => true,
        'motivo' => true,
        'confirmado_em' => true,
        'cancelado_em' => true,
        'observacoes' => true,
        'created' => true,
        'modified' => true,
        'paciente' => true,
        'profissional' => true,
        'agenda' => true,
        'atendimento' => true,
        'pagamentos' => true,
    ];
}
