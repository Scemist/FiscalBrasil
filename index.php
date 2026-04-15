<?php

require_once 'vendor/autoload.php';

use Imposto\Catalogo\TipoPessoa\TipoPessoa;
use Imposto\Catalogo\UFs\UF;
use Imposto\Catalogo\Unidade\Unidade;
use Imposto\Domain\Destinatario\Destinatario;
use Imposto\Domain\Emitente\Emitente;
use Imposto\Domain\Endereco\Endereco;
use Imposto\Domain\Pedido\ItemPedido;
use Imposto\Domain\Pedido\Pedido;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CFOP\CFOP;
use Imposto\Fiscal\CST\CSOSN;
use Imposto\Fiscal\RegimeTributario\SimplesNacional;

$emitente = new Emitente(
    cnpj:                     '12345678900000',
    razaoSocial:              'Scemist Tecnologia LTDA',
    nomeFantasia:             'Scemist',
    inscricaoEstadual:        '000123456789',
    codigoRegimeTributario:   1,
    inscricaoMunicipal:       '12345',
    endereco:                  new Endereco(
        logradouro:      'Rua Comendador Gomes',
        numero:          '505',
        bairro:          'Vila Chica',
        municipioCodigo: '3509502',
        municipioNome:   'Campinas',
        estado:          UF::SP,
        cep:             '13069096',
        complemento:     '',
        telefone:        '18990000000',
    ),
);

$destinatario = new Destinatario(
    tipoPessoa:        TipoPessoa::PF,
    documento:         '12345678910',
    nome:              'João Maria da Silva',
    indicadorIE:       9,
    endereco:          new Endereco(
        logradouro:      'Rua Michigan',
        numero:          '531',
        bairro:          'Brooklin',
        municipioCodigo: '3550308',
        municipioNome:   'Sao Paulo',
        estado:          UF::SP,
        cep:             '04566000',
        complemento:     '0A',
        telefone:        '18991111111',
    ),
);

$pedido = new Pedido(
    emitente:          $emitente,
    destinatario:      $destinatario,
    regimeTributario:  new SimplesNacional(),
    consumidorFinal:   true,
    presencial:        false,
);

$pedido->addItem(new ItemPedido(
    nome:               'Guitarra Stratocaster',
    preco:              1099.0,
    quantidade:         1,
    unidade:            Unidade::UNIDADE,
    ncm:                new NCM('9207.90.10'),
    situacaoTributaria: CSOSN::TributadaSemPermissaoDeCredito,
    cfop:               new CFOP('5102'),
    origemMercadoria:   0,
    codigoProduto:      'GTR-001',
    codigoBarras:       'SEM GTIN',
));

$pedido->addItem(new ItemPedido(
    nome:               'Pedal de Efeito Overdrive',
    preco:              399.0,
    quantidade:         2,
    unidade:            Unidade::UNIDADE,
    ncm:                new NCM('9207.90.90'),
    situacaoTributaria: CSOSN::NaoTributada,
    cfop:               new CFOP('5102'),
    origemMercadoria:   0,
    desconto:           39.90,
    codigoProduto:      'PDL-002',
    codigoBarras:       'SEM GTIN',
));

$notaFiscal = $pedido->getNotaFiscal();

echo 'Subtotal: ',           $notaFiscal->getSubtotal(), PHP_EOL;
echo 'Desconto: ',           $notaFiscal->getDesconto(), PHP_EOL;
echo 'Valor Liquido: ',      $notaFiscal->getValorLiquido(), PHP_EOL;
echo 'ICMS: ',               $notaFiscal->getICMS(), PHP_EOL;
echo 'IPI: ',                $notaFiscal->getIPI(), PHP_EOL;
echo 'Total com Impostos: ', $notaFiscal->getTotalComImpostos(), PHP_EOL;
echo PHP_EOL;
echo $notaFiscal->getXml(), PHP_EOL;
