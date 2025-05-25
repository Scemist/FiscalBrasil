<?php

require_once 'vendor/autoload.php';

use Imposto\Domain\Pedido\Pedido;
use Imposto\Domain\Pedido\ItemPedido;
use Imposto\Domain\Item\ItemProduto;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CST\CST;
use Imposto\Fiscal\CFOP\CFOP5102;
use Imposto\Fiscal\RegimeTributario\SimplesNacional;

$guitarra = new ItemProduto(
	nome: 'Guitarra Stratocaster',
	preco: 1099.0,
	ncm: new NCM('9207.90.10'),
	cst: new CST('000'),
	cfop: new CFOP5102()
);

$pedal = new ItemProduto(
	nome: 'Pedal de Efeito',
	preco: 399.0,
	ncm: new NCM('9207.90.90'),
	cst: new CST('000'),
	cfop: new CFOP5102()
);

$pedido = new Pedido(new SimplesNacional());
$pedido->addItem(new ItemPedido($guitarra, 1));
$pedido->addItem(new ItemPedido($pedal, 2));

$notaFiscal = $pedido->getNotaFiscal();

function asReal(float $valor): string
{
	return "R$ " . number_format($valor, 2, ',', '.');
}

echo 'Subtotal: ', asReal($pedido->getSubtotal()), PHP_EOL;
echo 'ICMS: ', asReal($pedido->getICMS()), PHP_EOL;
echo 'IPI: ', asReal($pedido->getIPI()), PHP_EOL;
echo 'Total com Impostos: ', asReal($pedido->getTotalComImpostos()), PHP_EOL;

echo "Nota Fiscal:\n";
echo $notaFiscal->toXml(), PHP_EOL; // ou toArray(), toJson(), etc.