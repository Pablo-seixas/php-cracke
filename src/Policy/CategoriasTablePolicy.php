<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\CategoriasTable;
use Authorization\IdentityInterface;

final class CategoriasTablePolicy
{
    use PolicyTrait;

    /** Check permission to list categories. */
    public function canIndex(IdentityInterface $identity, CategoriasTable $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.visualizar');
    }

    /** Check permission to create categories. */
    public function canAdd(IdentityInterface $identity, CategoriasTable $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.criar');
    }
}
