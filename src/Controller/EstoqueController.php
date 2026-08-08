<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Repository\EstoqueRepository;
use App\Service\EstoqueService;
use Cake\Http\Response;
use DomainException;

final class EstoqueController extends AppController
{
    /** Display the stock ledger and operational alerts. */
    public function index(): void
    {
        $movimentacoes = $this->fetchTable('MovimentacoesEstoque');
        $this->Authorization->authorize($movimentacoes, 'index');
        $query = $movimentacoes->find()
            ->contain(['Medicamentos', 'Produtos', 'Lotes', 'Usuarios'])
            ->orderByDesc('MovimentacoesEstoque.created');
        $repository = new EstoqueRepository();
        $this->set('movimentacoes', $this->paginate($query));
        $this->set('medicamentosCriticos', $repository->medicamentosCriticos());
        $this->set('produtosCriticos', $repository->produtosCriticos());
        $this->set('lotesProximos', $repository->lotesProximosVencimento());
    }

    /** Register a manual movement without allowing ledger edits or deletion. */
    public function movimentar(): ?Response
    {
        $movimentacoes = $this->fetchTable('MovimentacoesEstoque');
        $this->Authorization->authorize($movimentacoes, 'add');
        if ($this->request->is('post')) {
            try {
                $dados = $this->request->getData();
                $dados['endereco_ip'] = $this->request->clientIp();
                (new EstoqueService())->movimentar($dados, $this->usuarioAtual());
                $this->Flash->success('Movimentação registrada com sucesso.');

                return $this->redirect(['action' => 'index']);
            } catch (DomainException $exception) {
                $this->Flash->error($exception->getMessage());
            }
        }
        $this->set('medicamentos', $this->fetchTable('Medicamentos')->find('list')->where(['ativo' => true])->all());
        $this->set('produtos', $this->fetchTable('Produtos')->find('list', keyField: 'id', valueField: 'nome')->where(['ativo' => true])->all());
        $this->set('lotes', $this->fetchTable('Lotes')->find('list', keyField: 'id', valueField: 'numero_lote')->where(['recolhido' => false])->all());

        return null;
    }

    /** Return the authenticated domain user. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
