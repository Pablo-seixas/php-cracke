<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\LotesTable;
use Authorization\IdentityInterface;

final class LotesTablePolicy
{
    use PolicyTrait;

    /** Check permission to browse lots. */
    public function canIndex(IdentityInterface $identity, LotesTable $resource): bool
    {
        return $this->permitido($identity, 'estoque.visualizar');
    }

    /** Check permission to create lots. */
    public function canAdd(IdentityInterface $identity, LotesTable $resource): bool
    {
        return $this->permitido($identity, 'estoque.movimentar');
    }
}
