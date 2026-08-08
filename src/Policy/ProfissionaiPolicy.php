<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Profissionai;
use Authorization\IdentityInterface;

final class ProfissionaiPolicy
{
    use PolicyTrait;

    /** Edit professionals. */
    public function canEdit(IdentityInterface $identity, Profissionai $resource): bool
    {
        return $this->permitido($identity, 'agenda.criar');
    }

    /** Inactivate professionals. */
    public function canInativar(IdentityInterface $identity, Profissionai $resource): bool
    {
        return $this->permitido($identity, 'agenda.criar');
    }
}
