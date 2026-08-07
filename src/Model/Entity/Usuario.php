<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
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

    /**
     * @var array<string>
     */
    protected array $_hidden = ['senha'];

    /**
     * Hash a newly assigned password.
     */
    protected function _setSenha(string $senha): string
    {
        if ($senha === '' || str_starts_with($senha, '$2y$')) {
            return $senha;
        }

        return (new DefaultPasswordHasher())->hash($senha);
    }

    /**
     * Check a permission loaded through Perfis.Permissoes.
     */
    public function possuiPermissao(string $codigo): bool
    {
        foreach ($this->perfis ?? [] as $perfil) {
            foreach ($perfil->permissoes ?? [] as $permissao) {
                if ($permissao->codigo === $codigo) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check whether the user belongs to the administrator profile.
     */
    public function isAdministrador(): bool
    {
        foreach ($this->perfis ?? [] as $perfil) {
            if ($perfil->nome === 'Administrador') {
                return true;
            }
        }

        return false;
    }
}
