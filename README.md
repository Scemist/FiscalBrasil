## O Que é Esta Biblioteca?

Esta biblioteca calcula tributos e gera o XML da NF-e 4.00 (padrão SEFAZ) para vendas de produtos no Brasil. O foco atual é o regime **Simples Nacional**.

## O Que **não** é Esta Biblioteca?

Esta biblioteca não faz integrações com serviços externos e nem gera notas fiscais eletrônicas (NF-e) diretamente. Ela se concentra no cálculo de tributos e na estruturação dos dados necessários para a emissão de notas fiscais.


# Como Uso?

```php
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
    cnpj:                   '12345678900000',
    razaoSocial:            'Scemist Tecnologia LTDA',
    nomeFantasia:           'Scemist',
    inscricaoEstadual:      '000123456789',
    codigoRegimeTributario: 1,
    inscricaoMunicipal:     '12345',
    endereco: new Endereco(
        logradouro:      'Rua Comendador Gomes',
        numero:          '505',
        bairro:          'Vila Chica',
        municipioCodigo: '3509502',
        municipioNome:   'Campinas',
        estado:          UF::SP,
        cep:             '13069096',
    ),
);

$destinatario = new Destinatario(
    tipoPessoa:  TipoPessoa::PF,
    documento:   '12345678910',
    nome:        'João Maria da Silva',
    indicadorIE: IndicadorIE::NaoContribuinte,
    endereco: new Endereco(
        logradouro:      'Rua Michigan',
        numero:          '531',
        bairro:          'Brooklin',
        municipioCodigo: '3550308',
        municipioNome:   'Sao Paulo',
        estado:          UF::SP,
        cep:             '04566000',
    ),
);

$pedido = new Pedido(
    emitente:         $emitente,
    destinatario:     $destinatario,
    regimeTributario: new SimplesNacional(),
    consumidorFinal:  true,
    presencial:       false,
);

$pedido->addItem(new ItemPedido(
    nome:               'Guitarra Stratocaster',
    preco:              1099.0,
    quantidade:         1,
    unidade:            Unidade::UNIDADE,
    ncm:                new NCM('9207.90.10'),  // pontos aceitos; normalizado internamente
    situacaoTributaria: CSOSN::TributadaSemPermissaoDeCredito,
    cfop:               new CFOP('5102'),
    origemMercadoria:   0,
    codigoInterno:      'GTR-001',   // cProd — SKU interno da empresa
    codigoBarras:       'SEM GTIN',  // cEAN — GTIN ou 'SEM GTIN'
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
    codigoInterno:      'PDL-002',
    codigoBarras:       'SEM GTIN',
));

$notaFiscal = $pedido->getNotaFiscal();

echo 'Subtotal: ',           $notaFiscal->getSubtotal(), PHP_EOL;
echo 'Desconto: ',           $notaFiscal->getDesconto(), PHP_EOL;
echo 'Valor Liquido: ',      $notaFiscal->getValorLiquido(), PHP_EOL;
echo 'ICMS: ',               $notaFiscal->getICMS(), PHP_EOL;
echo 'IPI: ',                $notaFiscal->getIPI(), PHP_EOL;
echo 'Total com Impostos: ', $notaFiscal->getTotalComImpostos(), PHP_EOL;
echo $notaFiscal->getXml(), PHP_EOL;
```

## Resultado

```
Subtotal: 1897
Desconto: 39.9
Valor Liquido: 1857.1
ICMS: 0
IPI: 0
Total com Impostos: 1857.1
```

`getXml()` retorna um XML NF-e 4.00 pronto para assinar e transmitir:

```xml
<?xml version="1.0" encoding="utf-8"?>
<NFe xmlns="http://www.portalfiscal.inf.br/nfe">
  <infNFe versao="4.00">
    <ide>...</ide>
    <emit>
      <CNPJ>12345678900000</CNPJ>
      <xNome>Scemist Tecnologia LTDA</xNome>
      <CRT>1</CRT>
    </emit>
    <dest>
      <CPF>12345678910</CPF>
      <xNome>João Maria da Silva</xNome>
      <indIEDest>9</indIEDest>
    </dest>
    <det nItem="1">
      <prod>
        <xProd>Guitarra Stratocaster</xProd>
        <NCM>92079010</NCM>
        <CFOP>5102</CFOP>
        <vProd>1099.00</vProd>
      </prod>
      <imposto>
        <ICMS><ICMSSN102><orig>0</orig><CSOSN>102</CSOSN></ICMSSN102></ICMS>
        <IPI><IPINT><cEnq>001</cEnq><CST>53</CST></IPINT></IPI>
        <PIS><PISNT><CST>07</CST></PISNT></PIS>
        <COFINS><COFINSNT><CST>07</CST></COFINSNT></COFINS>
      </imposto>
    </det>
    <det nItem="2">
      <prod>
        <xProd>Pedal de Efeito Overdrive</xProd>
        <NCM>92079090</NCM>
        <vProd>798.00</vProd>
        <vDesc>39.90</vDesc>
      </prod>
      <imposto>
        <ICMS><ICMSSN400><orig>0</orig><CSOSN>400</CSOSN></ICMSSN400></ICMS>
        ...
      </imposto>
    </det>
    <total>
      <ICMSTot>
        <vICMS>0.00</vICMS>  <!-- Simples Nacional: ICMS recolhido via DAS -->
        <vNF>1857.10</vNF>
      </ICMSTot>
    </total>
  </infNFe>
</NFe>
```

# Quais os Requisitos para Usar?

### Emitente

Informações da sua empresa:

* Regime tributário (CRT: 1 = Simples Nacional ME, 2 = Simples Nacional EPP, 3 = Regime Normal)
* CNPJ, razão social, nome fantasia, inscrição estadual
* Endereço completo (logradouro, bairro, município IBGE, UF, CEP)

### Destinatário

Informações sobre o cliente:

* Tipo de pessoa (Física ou Jurídica) e CPF/CNPJ
* Indicador IE: `IndicadorIE::ContribuinteICMS`, `IndicadorIE::ContribuinteIsento`, `IndicadorIE::NaoContribuinte` ([verificar aqui](https://www.consultaie.com.br/))
* Endereço completo

### Produto (por item)

* **codigoInterno** — SKU ou código interno do produto na empresa (vira `<cProd>` no XML)
* **codigoBarras** — GTIN/EAN do produto, ou `'SEM GTIN'` se não tiver (vira `<cEAN>`)
* **NCM** — aceita formato com pontos (`9207.90.10`) ou sem (`92079010`)
* **CSOSN** (Simples Nacional) ou **CST** (Regime Normal)
* **CFOP** (ex: `5102` para venda dentro do estado)
* **Origem da mercadoria**: `0` = nacional, `1` = importado diretamente, `2` = importado de terceiros
* **Desconto** por item (opcional, em reais)

## Exemplos de Cadastros

Na pasta `examples/`, há arquivos JSON com estrutura relacional (um arquivo por entidade, ligados por IDs) simulando como ficaria num banco de dados.

## Onde Encontro os Dados Fiscais do Produto?

* **NCM**: [Portal Classif – Receita Federal](https://portalunico.siscomex.gov.br/classif/#/sumario)
* **CEST**: derivado do NCM; consulte seu contador se necessário
* **CFOP**: depende da operação (venda dentro do estado, interestadual, etc.)
* **CSOSN**: definido junto ao contador no cadastro do produto

# Status da Biblioteca

> Em desenvolvimento Alpha. Não recomendada para produção.

| Funcionalidade                        | Status |
| ------------------------------------- | ------ |
| Simples Nacional — cálculo de valores | ✅     |
| Simples Nacional — geração XML NF-e   | ✅     |
| Lucro Presumido / Lucro Real          | ❌     |
| DIFAL (EC 87/2015)                    | ❌     |
| Assinatura digital (certificado A1)   | ❌     |
| Transmissão SEFAZ                     | ❌     |
