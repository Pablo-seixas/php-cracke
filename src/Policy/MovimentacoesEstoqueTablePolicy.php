<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\MovimentacoesEstoqueTable;
use Authorization\IdentityInterface;

final class MovimentacoesEstoqueTablePolicy
{
    use PolicyTrait;

    /** Check permission to browse the immutable stock ledger. */
    public function canIndex(IdentityInterface $identity, MovimentacoesEstoqueTable $resource): bool
    {
        return $this->permitido($identity, 'estoque.visualizar');
    }

    /** Check permission to register stock movements. */
    public function canAdd(IdentityInterface $identity, MovimentacoesEstoqueTable $resource): bool
    {
        return $this->permitido($identity, 'estoque.movimentar');
    }
}
