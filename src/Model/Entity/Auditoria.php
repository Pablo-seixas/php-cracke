<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Auditoria Entity
 *
 * @property int $id
 * @property int|null $usuario_id
 * @property string $acao
 * @property string $modulo
 * @property int|null $registro_id
 * @property array|null $dados_anteriores
 * @property array|null $dados_posteriores
 * @property string|null $endereco_ip
 * @property string|null $user_agent
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\Usuario $usuario
 */
class Auditoria extends Entity
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
        'acao' => true,
        'modulo' => true,
        'registro_id' => true,
        'dados_anteriores' => true,
        'dados_posteriores' => true,
        'endereco_ip' => true,
        'user_agent' => true,
        'created' => true,
        'usuario' => true,
    ];
}
