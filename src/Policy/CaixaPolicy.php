<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Caixa;
use Authorization\IdentityInterface;

final class CaixaPolicy
{
    use PolicyTrait;

    /** Register cash movements. */
    public function canMovimentar(IdentityInterface $identity, Caixa $resource): bool
    {
        return $this->permitido($identity, 'vendas.criar');
    }

    /** Close registers. */
    public function canFechar(IdentityInterface $identity, Caixa $resource): bool
    {
        return $this->permitido($identity, 'vendas.criar');
    }
}
