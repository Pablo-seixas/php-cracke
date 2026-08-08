<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Compra;
use Authorization\IdentityInterface;

final class CompraPolicy
{
    use PolicyTrait;

    /** View purchase orders. */
    public function canView(IdentityInterface $identity, Compra $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }

    /** Receive purchase orders. */
    public function canReceber(IdentityInterface $identity, Compra $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }
}
