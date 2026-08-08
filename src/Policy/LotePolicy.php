<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Lote;
use Authorization\IdentityInterface;

final class LotePolicy
{
    use PolicyTrait;

    /** Check permission to block or recall a lot. */
    public function canBloquear(IdentityInterface $identity, Lote $resource): bool
    {
        return $this->permitido($identity, 'estoque.movimentar');
    }
}
