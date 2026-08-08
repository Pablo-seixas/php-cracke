<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Venda;
use Authorization\IdentityInterface;

final class VendaPolicy
{
    use PolicyTrait;

    /** View sales. */
    public function canView(IdentityInterface $identity, Venda $resource): bool
    {
        return $this->permitido($identity, 'vendas.criar');
    }

    /** Cancel sales. */
    public function canCancelar(IdentityInterface $identity, Venda $resource): bool
    {
        return $this->permitido($identity, 'vendas.cancelar');
    }
}
