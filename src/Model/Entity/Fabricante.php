<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Fabricante Entity
 *
 * @property int $id
 * @property string $nome
 * @property string|null $cnpj
 * @property bool $ativo
 * @property \Cake\I18n\DateTime|null $deleted
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Medicamento[] $medicamentos
 * @property \App\Model\Entity\Produto[] $produtos
 */
class Fabricante extends Entity
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
        'nome' => true,
        'cnpj' => true,
        'ativo' => true,
        'deleted' => true,
        'created' => true,
        'modified' => true,
        'medicamentos' => true,
        'produtos' => true,
    ];
}
