<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Medicamento;
use App\Model\Entity\Usuario;
use App\Model\Table\MedicamentosTable;
use App\Service\MedicamentoService;
use Cake\Http\Response;
use LogicException;
use Throwable;

final class MedicamentosController extends AppController
{
    protected MedicamentosTable $Medicamentos;

    /** Load the medicines table. */
    public function initialize(): void
    {
        parent::initialize();
        $this->Medicamentos = $this->fetchTable('Medicamentos');
    }

    /** Search and list medicines with stock lots. */
    public function index(): void
    {
        $this->Authorization->authorize($this->Medicamentos, 'index');
        $query = $this->Medicamentos->find()
            ->contain(['Categorias', 'Fabricantes', 'Lotes' => ['sort' => ['data_validade' => 'ASC']]])
            ->where(['Medicamentos.deleted IS' => null]);
        $busca = trim((string)$this->request->getQuery('busca'));
        if ($busca !== '') {
            $query->where(['OR' => [
                'Medicamentos.nome_comercial ILIKE' => "%{$busca}%",
                'Medicamentos.principio_ativo ILIKE' => "%{$busca}%",
                'Medicamentos.codigo_interno ILIKE' => "%{$busca}%",
                'Medicamentos.codigo_barras ILIKE' => "%{$busca}%",
            ]]);
        }
        if ($this->request->getQuery('controlado') === '1') {
            $query->where(['Medicamentos.controlado' => true]);
        }
        $this->set('medicamentos', $this->paginate($query->orderBy(['nome_comercial' => 'ASC'])));
        $this->set(compact('busca'));
    }

    /** Display medicine details, lots and stock history. */
    public function view(int $id): void
    {
        $medicamento = $this->Medicamentos->get($id, contain: [
            'Categorias',
            'Fabricantes',
            'Lotes' => ['sort' => ['data_validade' => 'ASC']],
            'MovimentacoesEstoque' => ['sort' => ['created' => 'DESC'], 'limit' => 20],
        ]);
        $this->Authorization->authorize($medicamento, 'view');
        $this->set(compact('medicamento'));
    }

    /** Create a medicine. */
    public function add(): ?Response
    {
        $this->Authorization->authorize($this->Medicamentos, 'add');
        $medicamento = $this->Medicamentos->newEmptyEntity();

        return $this->salvar($medicamento);
    }

    /** Edit a medicine. */
    public function edit(int $id): ?Response
    {
        $medicamento = $this->Medicamentos->get($id);
        $this->Authorization->authorize($medicamento, 'edit');

        return $this->salvar($medicamento);
    }

    /** Inactivate a medicine without physical deletion. */
    public function inativar(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $medicamento = $this->Medicamentos->get($id);
        $this->Authorization->authorize($medicamento, 'inativar');
        (new MedicamentoService($this->Medicamentos))->inativar($medicamento, $this->usuarioAtual());
        $this->Flash->success('Medicamento inativado com sucesso.');

        return $this->redirect(['action' => 'index']);
    }

    /** Handle create and edit forms through the domain service. */
    private function salvar(Medicamento $medicamento): ?Response
    {
        if ($this->request->is(['post', 'patch', 'put'])) {
            try {
                (new MedicamentoService($this->Medicamentos))->salvar(
                    $medicamento,
                    $this->request->getData(),
                    $this->usuarioAtual(),
                );
                $this->Flash->success('Medicamento salvo com sucesso.');

                return $this->redirect(['action' => 'index']);
            } catch (Throwable $exception) {
                $this->Flash->error('Revise os dados. Códigos e registros devem ser únicos.');
            }
        }
        $this->set('categorias', $this->Medicamentos->Categorias->find('list')->where(['ativo' => true])->all());
        $this->set('fabricantes', $this->Medicamentos->Fabricantes->find('list')->where(['ativo' => true])->all());
        $this->set(compact('medicamento'));

        return null;
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
