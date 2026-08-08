<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Response;

final class RelatoriosController extends AppController
{
    /** Export sales using a spreadsheet-compatible CSV file. */
    public function vendas(): Response
    {
        $this->Authorization->skipAuthorization();
        $usuario = $this->request->getAttribute('identity')->getOriginalData();
        $permitido = $usuario instanceof Usuario
            && ($usuario->isAdministrador() || $usuario->possuiPermissao('relatorios.exportar'));
        if (!$permitido) {
            throw new ForbiddenException('Sem permissão para exportar relatórios.');
        }
        $linhas = ['ID;Data;Status;Subtotal;Desconto;Total'];
        $vendas = $this->fetchTable('Vendas')->find()->orderByDesc('created')->limit(5000)->all();
        foreach ($vendas as $venda) {
            $linhas[] = implode(';', [
                $venda->id, $venda->created->format('d/m/Y H:i'), $venda->status,
                $venda->subtotal, $venda->desconto, $venda->total,
            ]);
        }
        $conteudo = "\xEF\xBB\xBF" . implode("\r\n", $linhas);

        return $this->response->withType('csv')
            ->withDownload('vendas-' . date('Y-m-d') . '.csv')
            ->withStringBody($conteudo);
    }
}
