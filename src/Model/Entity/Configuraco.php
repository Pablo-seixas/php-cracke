<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Configuraco Entity
 *
 * @property int $id
 * @property string $chave
 * @property array $valor
 * @property string|null $descricao
 * @property int|null $modified_by
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class Configuraco extends Entity
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
        'chave' => true,
        'valor' => true,
        'descricao' => true,
        'modified_by' => true,
        'created' => true,
        'modified' => true,
    ];
}
