<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Perfi;
use Authorization\IdentityInterface;

final class PerfiPolicy
{
    use PolicyTrait;

    /** Check permission to edit a profile. */
    public function canEdit(IdentityInterface $identity, Perfi $resource): bool
    {
        return $this->permitido($identity, 'usuarios.gerenciar');
    }
}
