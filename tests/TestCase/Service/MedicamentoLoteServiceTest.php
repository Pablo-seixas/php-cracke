<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Service\LoteService;
use App\Service\MedicamentoService;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DomainException;

final class MedicamentoLoteServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Usuarios',
        'app.Categorias',
        'app.Fabricantes',
        'app.Fornecedores',
        'app.Medicamentos',
        'app.Lotes',
        'app.Auditorias',
    ];

    public function testCadastroMedicamentoEStatusEstoque(): void
    {
        [$usuario, $categoria, $fabricante] = $this->referencias();
        $medicamentos = TableRegistry::getTableLocator()->get('Medicamentos');
        $medicamento = (new MedicamentoService($medicamentos))->salvar(
            $medicamentos->newEmptyEntity(),
            $this->dadosMedicamento($categoria->id, $fabricante->id),
            $usuario,
        );

        $this->assertSame('Dipirona Teste', $medicamento->nome_comercial);
        $this->assertSame('sem_estoque', $medicamento->statusEstoque());
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $this->assertSame(1, $auditorias->find()->where(['modulo' => 'medicamentos'])->count());
    }

    public function testMedicamentoDuplicadoNaoESalvo(): void
    {
        [$usuario, $categoria, $fabricante] = $this->referencias();
        $medicamentos = TableRegistry::getTableLocator()->get('Medicamentos');
        $service = new MedicamentoService($medicamentos);
        $data = $this->dadosMedicamento($categoria->id, $fabricante->id);
        $service->salvar($medicamentos->newEmptyEntity(), $data, $usuario);

        $this->expectException(PersistenceFailedException::class);
        $service->salvar($medicamentos->newEmptyEntity(), $data, $usuario);
    }

    public function testLoteVencidoERejeitado(): void
    {
        [$usuario, $categoria, $fabricante] = $this->referencias();
        $medicamentos = TableRegistry::getTableLocator()->get('Medicamentos');
        $medicamento = (new MedicamentoService($medicamentos))->salvar(
            $medicamentos->newEmptyEntity(),
            $this->dadosMedicamento($categoria->id, $fabricante->id),
            $usuario,
        );

        $this->expectException(DomainException::class);
        (new LoteService(TableRegistry::getTableLocator()->get('Lotes')))->criar([
            'medicamento_id' => $medicamento->id,
            'numero_lote' => 'VENCIDO-001',
            'data_validade' => '2020-01-01',
            'quantidade_recebida' => 10,
            'preco_custo' => 5,
        ], $usuario);
    }

    public function testLoteValidoPodeSerBloqueado(): void
    {
        [$usuario, $categoria, $fabricante] = $this->referencias();
        $medicamentos = TableRegistry::getTableLocator()->get('Medicamentos');
        $medicamento = (new MedicamentoService($medicamentos))->salvar(
            $medicamentos->newEmptyEntity(),
            $this->dadosMedicamento($categoria->id, $fabricante->id),
            $usuario,
        );
        $service = new LoteService(TableRegistry::getTableLocator()->get('Lotes'));
        $lote = $service->criar([
            'medicamento_id' => $medicamento->id,
            'numero_lote' => 'VALIDO-001',
            'data_validade' => '2030-12-31',
            'quantidade_recebida' => 10,
            'preco_custo' => 5,
        ], $usuario);
        $bloqueado = $service->alterarBloqueio($lote, $usuario, false, 'Teste de qualidade');

        $this->assertTrue($bloqueado->bloqueado);
        $this->assertFalse($bloqueado->recolhido);
        $this->assertSame('Teste de qualidade', $bloqueado->motivo_bloqueio);
    }

    /** @return array{0: \App\Model\Entity\Usuario, 1: object, 2: object} */
    private function referencias(): array
    {
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $categorias = TableRegistry::getTableLocator()->get('Categorias');
        $fabricantes = TableRegistry::getTableLocator()->get('Fabricantes');
        $usuario = $usuarios->newEntity([
            'nome' => 'Farmacêutico Teste',
            'email' => uniqid('farmaceutico-', true) . '@example.test',
            'senha' => 'ValidPassword!123',
            'ativo' => true,
        ]);
        $usuarios->saveOrFail($usuario);
        $categoria = $categorias->newEntity(['nome' => uniqid('Categoria '), 'ativo' => true]);
        $categorias->saveOrFail($categoria);
        $fabricante = $fabricantes->newEntity(['nome' => uniqid('Fabricante '), 'ativo' => true]);
        $fabricantes->saveOrFail($fabricante);

        return [$usuario, $categoria, $fabricante];
    }

    /** @return array<string, mixed> */
    private function dadosMedicamento(int $categoriaId, int $fabricanteId): array
    {
        return [
            'categoria_id' => $categoriaId,
            'fabricante_id' => $fabricanteId,
            'codigo_interno' => 'MED-' . uniqid(),
            'nome_comercial' => 'Dipirona Teste',
            'principio_ativo' => 'Dipirona monoidratada',
            'unidade_medida' => 'UN',
            'preco_custo' => 5,
            'preco_venda' => 8,
            'estoque_minimo' => 5,
            'estoque_maximo' => 100,
            'necessita_receita' => false,
            'controlado' => false,
            'refrigerado' => false,
            'ativo' => true,
        ];
    }
}
