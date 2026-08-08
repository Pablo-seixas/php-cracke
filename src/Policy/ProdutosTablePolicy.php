<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\ProdutosTable;
use Authorization\IdentityInterface;

final class ProdutosTablePolicy
{
    use PolicyTrait;

    /** Check permission to list products. */
    public function canIndex(IdentityInterface $identity, ProdutosTable $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.visualizar');
    }

    /** Check permission to create products. */
    public function canAdd(IdentityInterface $identity, ProdutosTable $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.criar');
    }
}
