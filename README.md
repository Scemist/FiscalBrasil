## O Que é Esta Biblioteca?

Esta biblioteca é uma solução para calcular impostos e todos os valores relacionados sobre vendas no Brasil.

## O Que **não** é Esta Biblioteca?

Esta biblioteca não faz integrações com serviços externos e nem gera notas fiscais eletrônicas (NF-e) diretamente. Ela se concentra no cálculo de tributos e na estruturação dos dados necessários para a emissão de notas fiscais.

# 💡 Como Uso?

Gerando um pedido com dois itens

```php
<?php

require_once 'vendor/autoload.php';

use Imposto\Domain\Pedido\Pedido;
use Imposto\Domain\Pedido\ItemPedido;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CST\CST;
use Imposto\Fiscal\CFOP\CFOP;
use Imposto\Fiscal\RegimeTributario\SimplesNacional;
use Imposto\Catalogo\UFs\UF;
use Imposto\Catalogo\Unidade\Unidade;

# Criando um Pedido

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

# Utilizando os Dados Gerados

$notaFiscal = $pedido->getNotaFiscal();

echo
    'Subtotal: ',           $notaFiscal->getSubtotal(), PHP_EOL,
    'ICMS: ',               $notaFiscal->getICMS(), PHP_EOL,
    'IPI: ',                $notaFiscal->getIPI(), PHP_EOL,
    'Total com Impostos: ', $notaFiscal->getTotalComImpostos(), PHP_EOL,
    'Nota Fiscal:', PHP_EOL,
    $notaFiscal->getXml(), PHP_EOL;

```

## 🚀 Resultado

```
Subtotal: 1897,00
ICMS: 227,64
IPI: 94,85
Total com Impostos: 2219,49

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

# 📝 Quais os Requisitos para Usar?

Para o correto cálculo de impostos no Brasil, algumas informações são obrigatórias e esta biblioteca não pode inferir automaticamente. Você precisará informar.

Dividimos essas informações em três grupos:

### 🏢 **Empresa**

Informações sobre sua própria empresa, como:

* Regime tributário (Simples Nacional, Lucro Presumido etc.)
* Estado de origem da mercadoria

Esses dados são informados ao criar o **Pedido**.

### 👤 **Cliente**

Informações sobre o destinatário da nota fiscal:

* Tipo de pessoa (Física ou Jurídica)
* Estado de destino
* Se é consumidor final
* Se é contribuinte de ICMS ([Consulta se Tem IE no Destino](https://www.consultaie.com.br/))

Também informadas ao criar o **Pedido**.

### 📦 **Produto**

Informações fiscais fixas sobre o produto:

* **NCM** (Classificação Fiscal)
* **CEST** (se aplicável)
* **Origem da mercadoria**
* **Aplica IPI?**
* **Aplica ST (Substituição Tributária)?**

Essas informações devem estar cadastradas no banco de dados e são utilizadas automaticamente ao criar os **itens do Pedido**.

## 🧾 Exemplos de Cadatros

Na pasta `exemplos/`, você encontrará arquivos JSON simulando a estrutura das tabelas `produtos` e `pedidos` com todos os campos obrigatórios. Eles servem como referência para seu sistema de cadastro.

## 📃 Onde Encontro Estes Dados ao Fazer o Cadastro do Produto?

* **NCM** (Classificação Fiscal): disponível no site da Receita Federal ou em ferramentas como:
  👉 [Portal Classif – Receita Federal](https://portalunico.siscomex.gov.br/classif/#/sumario)

* **CEST**: normalmente derivado do NCM. Se não souber, busque por "CEST \[nome do produto]" ou consulte seu contador.

* **Origem da mercadoria** (Código de origem):

  * `0`: Nacional
  * `1`: Importado diretamente
  * `2`: Importado adquirido de terceiros

* **Aplica IPI?**

  * ✅ Sim: se sua empresa fabrica ou importa o produto
  * ❌ Não: se apenas revende produtos prontos comprados no Brasil

* **Aplica ST?**

  * ✅ Sim: cada estado tem uma Tabela de Produtos Sujeito a Substituição Tributária (ST). Se o produto estiver nessa tabela, marque como sim.
  * ❌ Não: se não ou se estiver em dúvida (o sistema pode validar depois)

## ✅ Conclusão

Todos esses campos são **obrigatórios** para que a biblioteca calcule corretamente os tributos e gere a nota fiscal.
Garanta que eles estejam corretamente preenchidos no cadastro de produtos.

# 📍 Status da Biblioteca

> Em desenvolvimento Alpha. Não é recomendada para produção ainda.