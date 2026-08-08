<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\ComprasTable;
use Authorization\IdentityInterface;

final class ComprasTablePolicy
{
    use PolicyTrait;

    /** Browse purchase orders. */
    public function canIndex(IdentityInterface $identity, ComprasTable $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }
}
