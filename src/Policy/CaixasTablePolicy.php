<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\CaixasTable;
use Authorization\IdentityInterface;

final class CaixasTablePolicy
{
    use PolicyTrait;

    /** Browse registers. */
    public function canIndex(IdentityInterface $identity, CaixasTable $resource): bool
    {
        return $this->permitido($identity, 'vendas.criar');
    }

    /** Open registers. */
    public function canAdd(IdentityInterface $identity, CaixasTable $resource): bool
    {
        return $this->permitido($identity, 'vendas.criar');
    }
}
