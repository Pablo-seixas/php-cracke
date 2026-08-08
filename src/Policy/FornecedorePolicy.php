<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Fornecedore;
use Authorization\IdentityInterface;

final class FornecedorePolicy
{
    use PolicyTrait;

    /** Edit suppliers. */
    public function canEdit(IdentityInterface $identity, Fornecedore $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }

    /** Inactivate suppliers. */
    public function canInativar(IdentityInterface $identity, Fornecedore $resource): bool
    {
        return $this->permitido($identity, 'compras.criar');
    }
}
