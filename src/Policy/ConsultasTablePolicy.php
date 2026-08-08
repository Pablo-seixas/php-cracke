<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\ConsultasTable;
use Authorization\IdentityInterface;

final class ConsultasTablePolicy
{
    use PolicyTrait;

    /** Browse the agenda. */
    public function canIndex(IdentityInterface $identity, ConsultasTable $resource): bool
    {
        return $this->permitido($identity, 'agenda.visualizar');
    }

    /** Schedule consultations. */
    public function canAdd(IdentityInterface $identity, ConsultasTable $resource): bool
    {
        return $this->permitido($identity, 'agenda.criar');
    }
}
