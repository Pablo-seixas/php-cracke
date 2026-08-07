<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Profissionai Entity
 *
 * @property int $id
 * @property int|null $usuario_id
 * @property int|null $especialidade_id
 * @property string $nome
 * @property string $cpf
 * @property string $tipo
 * @property string|null $registro_profissional
 * @property string|null $conselho
 * @property string|null $telefone
 * @property string|null $email
 * @property array $dias_atendimento
 * @property array $horarios
 * @property bool $ativo
 * @property \Cake\I18n\DateTime|null $deleted
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Usuario $usuario
 * @property \App\Model\Entity\Especialidade $especialidade
 */
class Profissionai extends Entity
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
        'especialidade_id' => true,
        'nome' => true,
        'cpf' => true,
        'tipo' => true,
        'registro_profissional' => true,
        'conselho' => true,
        'telefone' => true,
        'email' => true,
        'dias_atendimento' => true,
        'horarios' => true,
        'ativo' => true,
        'deleted' => true,
        'created' => true,
        'modified' => true,
        'usuario' => true,
        'especialidade' => true,
    ];
}
