<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Model\Table\LotesTable;
use App\Service\LoteService;
use Cake\Http\Response;
use DomainException;
use LogicException;
use Throwable;

final class LotesController extends AppController
{
    protected LotesTable $Lotes;

    /** Load the lots table. */
    public function initialize(): void
    {
        parent::initialize();
        $this->Lotes = $this->fetchTable('Lotes');
    }

    /** List lots ordered by FEFO. */
    public function index(): void
    {
        $this->Authorization->authorize($this->Lotes, 'index');
        $query = $this->Lotes->find()
            ->contain(['Medicamentos', 'Fornecedores'])
            ->orderBy(['data_validade' => 'ASC']);
        $this->set('lotes', $this->paginate($query));
    }

    /** Register a received lot. */
    public function add(): ?Response
    {
        $this->Authorization->authorize($this->Lotes, 'add');
        $lote = $this->Lotes->newEmptyEntity();
        if ($this->request->is('post')) {
            try {
                (new LoteService($this->Lotes))->criar($this->request->getData(), $this->usuarioAtual());
                $this->Flash->success('Lote cadastrado com sucesso.');

                return $this->redirect(['action' => 'index']);
            } catch (DomainException $exception) {
                $this->Flash->error($exception->getMessage());
            } catch (Throwable $exception) {
                $this->Flash->error('Não foi possível cadastrar o lote. Verifique os dados e a duplicidade.');
            }
        }
        $this->set('medicamentos', $this->Lotes->Medicamentos->find('list')->where(['ativo' => true])->all());
        $this->set('fornecedores', $this->Lotes->Fornecedores->find('list')->where(['ativo' => true])->all());
        $this->set(compact('lote'));

        return null;
    }

    /** Block or recall a lot. */
    public function bloquear(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $lote = $this->Lotes->get($id);
        $this->Authorization->authorize($lote, 'bloquear');
        $recolher = $this->request->getData('tipo') === 'recolher';
        (new LoteService($this->Lotes))->alterarBloqueio(
            $lote,
            $this->usuarioAtual(),
            $recolher,
            (string)$this->request->getData('motivo'),
        );
        $this->Flash->success($recolher ? 'Lote recolhido.' : 'Lote bloqueado.');

        return $this->redirect(['action' => 'index']);
    }

    /** Return the authenticated domain user. */
    private function usuarioAtual(): Usuario
    {
        $usuario = $this->request->getAttribute('identity')?->getOriginalData();
        if (!$usuario instanceof Usuario) {
            throw new LogicException('Identidade autenticada inválida.');
        }

        return $usuario;
    }
}
