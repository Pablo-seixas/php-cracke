<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Paciente Entity
 *
 * @property int $id
 * @property int|null $cliente_id
 * @property string $nome_completo
 * @property string|null $nome_social
 * @property string|null $cpf
 * @property \Cake\I18n\Date $data_nascimento
 * @property string|null $sexo
 * @property string|null $telefone
 * @property string|null $email
 * @property string|null $endereco
 * @property string|null $responsavel
 * @property string|null $contato_emergencia
 * @property string|null $alergias
 * @property string|null $observacoes
 * @property bool $ativo
 * @property \Cake\I18n\DateTime|null $deleted
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Cliente $cliente
 * @property \App\Model\Entity\Prontuario $prontuario
 * @property \App\Model\Entity\Atendimento[] $atendimentos
 * @property \App\Model\Entity\Consulta[] $consultas
 * @property \App\Model\Entity\ContasReceber[] $contas_receber
 * @property \App\Model\Entity\Receita[] $receitas
 */
class Paciente extends Entity
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
        'cliente_id' => true,
        'nome_completo' => true,
        'nome_social' => true,
        'cpf' => true,
        'data_nascimento' => true,
        'sexo' => true,
        'telefone' => true,
        'email' => true,
        'endereco' => true,
        'responsavel' => true,
        'contato_emergencia' => true,
        'alergias' => true,
        'observacoes' => true,
        'ativo' => true,
        'deleted' => true,
        'created' => true,
        'modified' => true,
        'cliente' => true,
        'prontuario' => true,
        'atendimentos' => true,
        'consultas' => true,
        'contas_receber' => true,
        'receitas' => true,
    ];
}
