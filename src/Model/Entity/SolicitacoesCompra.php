<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SolicitacoesCompra Entity
 *
 * @property int $id
 * @property string $numero
 * @property int $solicitante_id
 * @property int|null $aprovador_id
 * @property int|null $fornecedor_escolhido_id
 * @property string $status
 * @property string $justificativa
 * @property string $valor_estimado
 * @property string|null $valor_aprovado
 * @property \Cake\I18n\DateTime|null $solicitada_em
 * @property \Cake\I18n\DateTime|null $aprovada_em
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Solicitante $solicitante
 * @property \App\Model\Entity\Aprovador $aprovador
 * @property \App\Model\Entity\Fornecedore $fornecedor_escolhido
 */
class SolicitacoesCompra extends Entity
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
        'numero' => true,
        'solicitante_id' => true,
        'aprovador_id' => true,
        'fornecedor_escolhido_id' => true,
        'status' => true,
        'justificativa' => true,
        'valor_estimado' => true,
        'valor_aprovado' => true,
        'solicitada_em' => true,
        'aprovada_em' => true,
        'created' => true,
        'modified' => true,
        'solicitante' => true,
        'aprovador' => true,
        'fornecedor_escolhido' => true,
    ];
}
