<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Receita Entity
 *
 * @property int $id
 * @property int|null $prescricao_id
 * @property int $paciente_id
 * @property int $profissional_id
 * @property string $numero
 * @property string|null $tipo
 * @property \Cake\I18n\DateTime $emitida_em
 * @property \Cake\I18n\Date|null $validade
 * @property string|null $arquivo_path
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Prescrico $prescricao
 * @property \App\Model\Entity\Paciente $paciente
 * @property \App\Model\Entity\Profissionai $profissional
 * @property \App\Model\Entity\VendasIten[] $vendas_itens
 */
class Receita extends Entity
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
        'prescricao_id' => true,
        'paciente_id' => true,
        'profissional_id' => true,
        'numero' => true,
        'tipo' => true,
        'emitida_em' => true,
        'validade' => true,
        'arquivo_path' => true,
        'created' => true,
        'modified' => true,
        'prescricao' => true,
        'paciente' => true,
        'profissional' => true,
        'vendas_itens' => true,
    ];
}
