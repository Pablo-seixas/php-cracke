<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Model\Entity\Usuario;
use App\Service\CatalogoService;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

final class CatalogoServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Usuarios',
        'app.Categorias',
        'app.Fabricantes',
        'app.Produtos',
        'app.Auditorias',
    ];

    /** Create category, manufacturer and product with audit records. */
    public function testCriaCatalogoCompletoComAuditoria(): void
    {
        $service = new CatalogoService();
        $usuario = $this->usuario();
        $categorias = TableRegistry::getTableLocator()->get('Categorias');
        $fabricantes = TableRegistry::getTableLocator()->get('Fabricantes');
        $produtos = TableRegistry::getTableLocator()->get('Produtos');

        $categoria = $service->salvar($categorias, $categorias->newEmptyEntity(), [
            'nome' => 'Dermocosméticos', 'descricao' => 'Cuidados dermatológicos', 'ativo' => true,
        ], $usuario);
        $fabricante = $service->salvar($fabricantes, $fabricantes->newEmptyEntity(), [
            'nome' => 'Fabricante Teste', 'cnpj' => '11222333000181', 'ativo' => true,
        ], $usuario);
        $produto = $service->salvar($produtos, $produtos->newEmptyEntity(), [
            'categoria_id' => $categoria->id,
            'fabricante_id' => $fabricante->id,
            'codigo_interno' => 'PROD-TEST-001',
            'codigo_barras' => '7891234567890',
            'nome' => 'Protetor Solar Teste',
            'unidade_medida' => 'UN',
            'preco_custo' => '20.00',
            'preco_venda' => '35.00',
            'estoque_minimo' => '5',
            'estoque_maximo' => '50',
            'ativo' => true,
        ], $usuario);

        $this->assertNotNull($produto->id);
        $this->assertSame(3, TableRegistry::getTableLocator()->get('Auditorias')->find()->count());
    }

    /** Reject duplicate internal product codes. */
    public function testRejeitaProdutoDuplicado(): void
    {
        $service = new CatalogoService();
        $produtos = TableRegistry::getTableLocator()->get('Produtos');
        $categorias = TableRegistry::getTableLocator()->get('Categorias');
        $categoria = $service->salvar($categorias, $categorias->newEmptyEntity(), [
            'nome' => 'Categoria duplicidade', 'ativo' => true,
        ], $this->usuario());
        $existente = $service->salvar($produtos, $produtos->newEmptyEntity(), [
            'categoria_id' => $categoria->id,
            'codigo_interno' => 'DUP-001',
            'nome' => 'Produto original',
            'unidade_medida' => 'UN',
            'preco_custo' => '1.00',
            'preco_venda' => '2.00',
            'estoque_minimo' => '1',
            'estoque_maximo' => '10',
            'ativo' => true,
        ], $this->usuario());
        $duplicado = $produtos->newEntity($existente->toArray());
        $duplicado->unset('id');

        $this->assertFalse($produtos->save($duplicado));
        $this->assertNotEmpty($duplicado->getError('codigo_interno'));
    }

    /** Return a persisted operator for audit ownership. */
    private function usuario(): Usuario
    {
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $usuario = $usuarios->find()->first();
        if ($usuario instanceof Usuario) {
            return $usuario;
        }

        $usuario = $usuarios->newEntity([
            'nome' => 'Operador de Catálogo',
            'email' => 'catalogo@example.test',
            'senha' => 'ValidPassword!123',
            'ativo' => true,
        ]);
        $usuarios->saveOrFail($usuario);

        return $usuario;
    }
}
