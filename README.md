# Exemplo Uso

```php
<?php

require_once 'vendor/autoload.php';

use Imposto\Domain\Pedido\Pedido;
use Imposto\Domain\Pedido\ItemPedido;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CST\CST;
use Imposto\Fiscal\CFOP\CFOP5102;
use Imposto\Fiscal\RegimeTributario\SimplesNacional;
use Imposto\Catalogo\UFs\UF;

# Criação

$pedido = new Pedido(
	regimeTributario: new SimplesNacional(),
	origem: UF::SP,
	destino: UF::MG,
);

$pedido->addItem(new ItemPedido(
	nome: 'Guitarra Stratocaster',
	preco: 1099.0,
	quantidade: 1,
	unidade: 'un',
	ncm: new NCM('9207.90.10'),
	cst: new CST('000'),
	cfop: new CFOP5102(),
));

$pedido->addItem(new ItemPedido(
	nome: 'Pedal de Efeito',
	preco: 399.0,
	quantidade: 2,
	unidade: 'un',
	ncm: new NCM('9207.90.90'),
	cst: new CST('000'),
	cfop: new CFOP5102()
));

$notaFiscal = $pedido->getNotaFiscal();

# Utilização

function asReal(float $valor): string
{
	return "R$ " . number_format($valor, 2, ',', '.');
}

echo 'Subtotal: ', asReal($notaFiscal->getSubtotal()), PHP_EOL;
echo 'ICMS: ', asReal($notaFiscal->getICMS()), PHP_EOL;
echo 'IPI: ', asReal($notaFiscal->getIPI()), PHP_EOL;
echo 'Total com Impostos: ', asReal($notaFiscal->getTotalComImpostos()), PHP_EOL;

echo $notaFiscal->getXml(), PHP_EOL;
```

```
Subtotal: R$ 1.897,00
ICMS: R$ 227,64
IPI: R$ 94,85
Total com Impostos: R$ 2.219,49

<notaFiscal>
  <regimeTributario>Simples Nacional</regimeTributario>
  <origem>SP</origem>
  <destino>MG</destino>
  <dataEmissao>2025-05-26T01:34:33+00:00</dataEmissao>
  <subtotal>1897.00</subtotal>
  <icms>227.64</icms>
  <item>
    <descricao>Guitarra Stratocaster</descricao>
    <quantidade>1</quantidade>
    <preco>1099.00</preco>
    <icms>131.88</icms>
    <ipi>54.95</ipi>
    <pis>0.00</pis>
    <cofins>0.00</cofins>
  </item>
  <item>
    <descricao>Pedal de Efeito</descricao>
    <quantidade>2</quantidade>
    <preco>399.00</preco>
    <icms>95.76</icms>
    <ipi>39.90</ipi>
    <pis>0.00</pis>
    <cofins>0.00</cofins>
  </item>
</notaFiscal>
```