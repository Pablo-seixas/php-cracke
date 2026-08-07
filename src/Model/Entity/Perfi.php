<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Perfi Entity
 *
 * @property int $id
 * @property string $nome
 * @property string|null $descricao
 * @property bool $ativo
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Permisso[] $permissoes
 * @property \App\Model\Entity\Usuario[] $usuarios
 */
class Perfi extends Entity
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
        'descricao' => true,
        'ativo' => true,
        'created' => true,
        'modified' => true,
        'permissoes' => true,
        'usuarios' => true,
    ];
}
