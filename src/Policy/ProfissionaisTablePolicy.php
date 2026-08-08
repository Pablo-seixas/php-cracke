<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\ProfissionaisTable;
use Authorization\IdentityInterface;

final class ProfissionaisTablePolicy
{
    use PolicyTrait;

    /** Browse professionals. */
    public function canIndex(IdentityInterface $identity, ProfissionaisTable $resource): bool
    {
        return $this->permitido($identity, 'agenda.visualizar');
    }

    /** Create professionals. */
    public function canAdd(IdentityInterface $identity, ProfissionaisTable $resource): bool
    {
        return $this->permitido($identity, 'agenda.criar');
    }
}
