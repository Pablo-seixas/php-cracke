<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\EspecialidadesTable;
use Authorization\IdentityInterface;

final class EspecialidadesTablePolicy
{
    use PolicyTrait;

    /** Browse specialties. */
    public function canIndex(IdentityInterface $identity, EspecialidadesTable $resource): bool
    {
        return $this->permitido($identity, 'agenda.visualizar');
    }

    /** Create specialties. */
    public function canAdd(IdentityInterface $identity, EspecialidadesTable $resource): bool
    {
        return $this->permitido($identity, 'agenda.criar');
    }
}
