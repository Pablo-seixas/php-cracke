<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\SolicitacoesCompra;
use Authorization\IdentityInterface;

final class SolicitacoesCompraPolicy
{
    use PolicyTrait;

    /** View a request. */
    public function canView(IdentityInterface $identity, SolicitacoesCompra $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }

    /** Register and select quotations. */
    public function canCotar(IdentityInterface $identity, SolicitacoesCompra $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }

    /** Approve or reject requests. */
    public function canAprovar(IdentityInterface $identity, SolicitacoesCompra $resource): bool
    {
        return $this->permitido($identity, 'compras.aprovar');
    }

    /** Emit approved orders. */
    public function canEmitir(IdentityInterface $identity, SolicitacoesCompra $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }
}
