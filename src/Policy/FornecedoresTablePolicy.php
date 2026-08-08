<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\FornecedoresTable;
use Authorization\IdentityInterface;

final class FornecedoresTablePolicy
{
    use PolicyTrait;

    /** Browse suppliers. */
    public function canIndex(IdentityInterface $identity, FornecedoresTable $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }

    /** Create suppliers. */
    public function canAdd(IdentityInterface $identity, FornecedoresTable $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }
}
