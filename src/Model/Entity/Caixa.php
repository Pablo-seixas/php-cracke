<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Caixa Entity
 *
 * @property int $id
 * @property int $usuario_abertura_id
 * @property int|null $usuario_fechamento_id
 * @property string $status
 * @property string $saldo_inicial
 * @property string|null $saldo_final
 * @property \Cake\I18n\DateTime $aberto_em
 * @property \Cake\I18n\DateTime|null $fechado_em
 * @property string|null $observacoes
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\UsuarioAbertura $usuario_abertura
 * @property \App\Model\Entity\UsuarioFechamento $usuario_fechamento
 * @property \App\Model\Entity\MovimentacoesCaixa[] $movimentacoes_caixa
 * @property \App\Model\Entity\Pagamento[] $pagamentos
 */
class Caixa extends Entity
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
        'usuario_abertura_id' => true,
        'usuario_fechamento_id' => true,
        'status' => true,
        'saldo_inicial' => true,
        'saldo_final' => true,
        'aberto_em' => true,
        'fechado_em' => true,
        'observacoes' => true,
        'created' => true,
        'modified' => true,
        'usuario_abertura' => true,
        'usuario_fechamento' => true,
        'movimentacoes_caixa' => true,
        'pagamentos' => true,
    ];
}
