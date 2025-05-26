<?php

require_once 'vendor/autoload.php';

use Imposto\Catalogo\TipoPessoa\TipoPessoa;
use Imposto\Domain\Pedido\Pedido;
use Imposto\Domain\Pedido\ItemPedido;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CST\CST;
use Imposto\Fiscal\CFOP\CFOP;
use Imposto\Fiscal\RegimeTributario\SimplesNacional;
use Imposto\Catalogo\UFs\UF;
use Imposto\Catalogo\Unidade\Unidade;

$pedido = new Pedido(
	regimeTributario: new SimplesNacional(),
	origem: UF::SP,
	destino: UF::MG,
	tipoPessoa: TipoPessoa::PF,
);

$pedido->addItem(new ItemPedido(
	nome: 'Guitarra Stratocaster',
	preco: 1099.0,
	quantidade: 1,
	unidade: Unidade::UNIDADE,
	ncm: new NCM('9207.90.10'),
	cst: new CST('000'),
	cfop: new CFOP('5102'),
));

$pedido->addItem(new ItemPedido(
	nome: 'Pedal de Efeito',
	preco: 399.0,
	quantidade: 2,
	unidade: Unidade::UNIDADE,
	ncm: new NCM('9207.90.90'),
	cst: new CST('000'),
	cfop: new CFOP('5102')
));

$notaFiscal = $pedido->getNotaFiscal();

function asReal(float $valor): string
{
	return "R$ " . number_format($valor, 2, ',', '.');
}

echo 'Subtotal: ', asReal($notaFiscal->getSubtotal()), PHP_EOL;
echo 'ICMS: ', asReal($notaFiscal->getICMS()), PHP_EOL;
echo 'IPI: ', asReal($notaFiscal->getIPI()), PHP_EOL;
echo 'Total com Impostos: ', asReal($notaFiscal->getTotalComImpostos()), PHP_EOL;

echo "Nota Fiscal:\n";
echo $notaFiscal->getXml(), PHP_EOL;
