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
	consumidorFinal: true,
	contribuinteICMS: false,
	presencial: false,
);

$pedido->addItem(new ItemPedido(
	nome: 'Guitarra Stratocaster',
	preco: 1099.0,
	quantidade: 1,
	unidade: Unidade::UNIDADE,
	ncm: new NCM('92079010'),
	cst: new CST('000'),
	cfop: new CFOP('5102'),
));

$pedido->addItem(new ItemPedido(
	nome: 'Pedal de Efeito',
	preco: 399.0,
	quantidade: 2,
	unidade: Unidade::UNIDADE,
	ncm: new NCM('92079090'),
	cst: new CST('000'),
	cfop: new CFOP('5102')
));

$notaFiscal = $pedido->getNotaFiscal();

echo
	'Subtotal: ',           $notaFiscal->getSubtotal(), PHP_EOL,
	'ICMS: ',               $notaFiscal->getICMS(), PHP_EOL,
	'IPI: ',                $notaFiscal->getIPI(), PHP_EOL,
	'Total com Impostos: ', $notaFiscal->getTotalComImpostos(), PHP_EOL,
	'Nota Fiscal:', PHP_EOL,
	$notaFiscal->getXml(), PHP_EOL;
