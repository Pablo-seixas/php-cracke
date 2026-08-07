<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MovimentacoesEstoque Entity
 *
 * @property int $id
 * @property int|null $medicamento_id
 * @property int|null $produto_id
 * @property int|null $lote_id
 * @property string $tipo
 * @property string $quantidade_anterior
 * @property string $quantidade
 * @property string $quantidade_posterior
 * @property int $usuario_id
 * @property string $motivo
 * @property string|null $documento_tipo
 * @property int|null $documento_id
 * @property string|null $endereco_ip
 * @property string|null $observacao
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\Medicamento $medicamento
 * @property \App\Model\Entity\Produto $produto
 * @property \App\Model\Entity\Lote $lote
 * @property \App\Model\Entity\Usuario $usuario
 */
class MovimentacoesEstoque extends Entity
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
        'medicamento_id' => true,
        'produto_id' => true,
        'lote_id' => true,
        'tipo' => true,
        'quantidade_anterior' => true,
        'quantidade' => true,
        'quantidade_posterior' => true,
        'usuario_id' => true,
        'motivo' => true,
        'documento_tipo' => true,
        'documento_id' => true,
        'endereco_ip' => true,
        'observacao' => true,
        'created' => true,
        'medicamento' => true,
        'produto' => true,
        'lote' => true,
        'usuario' => true,
    ];
}
