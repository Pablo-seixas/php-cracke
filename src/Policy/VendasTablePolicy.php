<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\VendasTable;
use Authorization\IdentityInterface;

final class VendasTablePolicy
{
    use PolicyTrait;

    /** Browse sales. */
    public function canIndex(IdentityInterface $identity, VendasTable $resource): bool
    {
        return $this->permitido($identity, 'vendas.criar');
    }

    /** Create sales. */
    public function canAdd(IdentityInterface $identity, VendasTable $resource): bool
    {
        return $this->permitido($identity, 'vendas.criar');
    }
}
