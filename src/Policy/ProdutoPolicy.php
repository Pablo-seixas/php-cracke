<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Produto;
use Authorization\IdentityInterface;

final class ProdutoPolicy
{
    use PolicyTrait;

    /** Check permission to edit products. */
    public function canEdit(IdentityInterface $identity, Produto $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.editar');
    }

    /** Check permission to inactivate products. */
    public function canInativar(IdentityInterface $identity, Produto $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.editar');
    }
}
