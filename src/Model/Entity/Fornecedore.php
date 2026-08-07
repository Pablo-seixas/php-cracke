<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Fornecedore Entity
 *
 * @property int $id
 * @property string $razao_social
 * @property string|null $nome_fantasia
 * @property string $cnpj
 * @property string|null $inscricao_estadual
 * @property string|null $email
 * @property string|null $telefone
 * @property string|null $endereco
 * @property string|null $contato_nome
 * @property int $prazo_medio_entrega
 * @property string|null $condicoes_pagamento
 * @property string|null $avaliacao
 * @property string|null $observacoes
 * @property bool $ativo
 * @property \Cake\I18n\DateTime|null $deleted
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Produto[] $produtos
 */
class Fornecedore extends Entity
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
        'razao_social' => true,
        'nome_fantasia' => true,
        'cnpj' => true,
        'inscricao_estadual' => true,
        'email' => true,
        'telefone' => true,
        'endereco' => true,
        'contato_nome' => true,
        'prazo_medio_entrega' => true,
        'condicoes_pagamento' => true,
        'avaliacao' => true,
        'observacoes' => true,
        'ativo' => true,
        'deleted' => true,
        'created' => true,
        'modified' => true,
        'produtos' => true,
    ];
}
