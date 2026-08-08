<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\SolicitacoesComprasTable;
use Authorization\IdentityInterface;

final class SolicitacoesComprasTablePolicy
{
    use PolicyTrait;

    /** Browse requests. */
    public function canIndex(IdentityInterface $identity, SolicitacoesComprasTable $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }

    /** Create requests. */
    public function canAdd(IdentityInterface $identity, SolicitacoesComprasTable $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }
}
