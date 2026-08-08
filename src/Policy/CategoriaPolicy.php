<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Categoria;
use Authorization\IdentityInterface;

final class CategoriaPolicy
{
    use PolicyTrait;

    /** Check permission to edit categories. */
    public function canEdit(IdentityInterface $identity, Categoria $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.editar');
    }

    /** Check permission to inactivate categories. */
    public function canInativar(IdentityInterface $identity, Categoria $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.editar');
    }
}
