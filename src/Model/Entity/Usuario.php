<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Usuario Entity
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $senha
 * @property bool $ativo
 * @property \Cake\I18n\DateTime|null $bloqueado_ate
 * @property \Cake\I18n\DateTime|null $ultimo_login
 * @property \Cake\I18n\DateTime|null $deleted
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Profissionai $profissionai
 * @property \App\Model\Entity\Auditoria[] $auditorias
 * @property \App\Model\Entity\Compra[] $compras
 * @property \App\Model\Entity\Lote[] $lotes
 * @property \App\Model\Entity\MovimentacoesCaixa[] $movimentacoes_caixa
 * @property \App\Model\Entity\MovimentacoesEstoque[] $movimentacoes_estoque
 * @property \App\Model\Entity\Notificaco[] $notificacoes
 * @property \App\Model\Entity\ProntuariosHistorico[] $prontuarios_historicos
 * @property \App\Model\Entity\TentativasLogin[] $tentativas_login
 * @property \App\Model\Entity\Venda[] $vendas
 * @property \App\Model\Entity\Perfi[] $perfis
 */
class Usuario extends Entity
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
        'email' => true,
        'senha' => true,
        'ativo' => true,
        'bloqueado_ate' => true,
        'ultimo_login' => true,
        'deleted' => true,
        'created' => true,
        'modified' => true,
        'profissionai' => true,
        'auditorias' => true,
        'compras' => true,
        'lotes' => true,
        'movimentacoes_caixa' => true,
        'movimentacoes_estoque' => true,
        'notificacoes' => true,
        'prontuarios_historicos' => true,
        'tentativas_login' => true,
        'vendas' => true,
        'perfis' => true,
    ];
}
