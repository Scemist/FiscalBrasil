Atualmente focando em venda de produtos no simples nacional

# NotaFiscal — Métodos

| Status | Descrição               | Método                              |
| ------ | ----------------------- | ----------------------------------- |
| ✅     | Subtotal bruto          | `$notaFiscal->getSubtotal()`        |
| ✅     | Total de descontos      | `$notaFiscal->getDesconto()`        |
| ✅     | Valor líquido           | `$notaFiscal->getValorLiquido()`    |
| ✅     | Total ICMS              | `$notaFiscal->getICMS()`            |
| ✅     | Total IPI               | `$notaFiscal->getIPI()`             |
| ✅     | Total com impostos      | `$notaFiscal->getTotalComImpostos()`|
| ✅     | XML NF-e 4.00           | `$notaFiscal->getXml()`             |
| ✅     | UF Origem               | `$notaFiscal->getOrigem()`          |
| ✅     | UF Destino              | `$notaFiscal->getDestino()`         |
| ✅     | Regime tributário       | `$notaFiscal->getRegimeTributario()`|
| ❌     | Data de emissão         | `$notaFiscal->getDataEmissao()`     |

# NotaFiscal->getXml() — formato de saída

Retorna XML no padrão NF-e 4.00 da SEFAZ. Estrutura resumida:

```xml
<?xml version="1.0" encoding="utf-8"?>
<NFe xmlns="http://www.portalfiscal.inf.br/nfe">
  <infNFe versao="4.00">
    <ide>...</ide>
    <emit>
      <CNPJ>...</CNPJ>
      <xNome>...</xNome>
      <enderEmit>...</enderEmit>
      <IE>...</IE>
      <CRT>1</CRT>       <!-- 1 = Simples Nacional -->
    </emit>
    <dest>
      <CPF>...</CPF>     <!-- ou CNPJ -->
      <xNome>...</xNome>
      <enderDest>...</enderDest>
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
        <ICMS><ICMSSN102>
          <orig>0</orig>
          <CSOSN>102</CSOSN>
        </ICMSSN102></ICMS>
        <IPI><IPINT><cEnq>001</cEnq><CST>53</CST></IPINT></IPI>
        <PIS><PISNT><CST>07</CST></PISNT></PIS>
        <COFINS><COFINSNT><CST>07</CST></COFINSNT></COFINS>
      </imposto>
    </det>
    <total><ICMSTot>
      <vICMS>0.00</vICMS>   <!-- Simples: ICMS recolhido via DAS, não destacado -->
      <vNF>1857.10</vNF>
    </ICMSTot></total>
    <transp>...</transp>
    <pag>...</pag>
  </infNFe>
</NFe>
```

# Tabelas auxiliares que serão necessárias no código fonte

## cfops (~80 a 100 linhas)

| Campo             | Tipo   | Descrição                                       |
| ----------------- | ------ | ----------------------------------------------- |
| cfop              | string | Código CFOP ex: "5102"                          |
| descricao         | string | Ex: "Venda dentro do estado"                    |
| icms\_aplicavel   | bool   | Se ICMS é aplicável                             |
| ipi\_aplicavel    | bool   | Se IPI é aplicável                              |
| destino\_uf       | string | (opcional) Se aplica a UF específica            |
| tipo\_operacao    | enum   | 'entrada' ou 'saida'                            |
| consumidor\_final | bool   | (opcional) Se é exclusivo para consumidor final |

## cst (~20 linhas)

| Campo        | Tipo   | Descrição                 |
| ------------ | ------ | ------------------------- |
| codigo       | string | Ex: "000"                 |
| descricao    | string | "Tributada integralmente" |
| aplica\_icms | bool   | true/false                |
| aplica\_st   | bool   | true/false                |
| aplica\_ipi  | bool   | true/false                |
| tipo         | enum   | "cst" ou "csosn"          |

## ncm (~5300 linhas)

| Campo            | Tipo    | Descrição                         |
| ---------------- | ------- | --------------------------------- |
| ncm              | string  | Código NCM ex: "92079010"         |
| descricao        | string  | Descrição resumida                |
| ipi\_aliquota    | decimal | Alíquota IPI aplicável ao NCM     |
| pis\_aliquota    | decimal | Padrão nacional (Simples = 0)     |
| cofins\_aliquota | decimal | Padrão nacional (Simples = 0)     |
| cest             | string  | Código CEST vinculado (opcional)  |
| aplica\_ipi      | bool    | Se geralmente tem IPI             |
| aplica\_st       | bool    | Se é comum usar ICMS-ST nesse NCM |

## icms_aliquotas (~729 linhas)

| Campo         | Tipo    | Descrição                                               |
| ------------- | ------- | ------------------------------------------------------- |
| origem\_uf    | string  | UF de origem                                            |
| destino\_uf   | string  | UF de destino                                           |
| interno       | decimal | Alíquota ICMS p/ vendas internas                        |
| interestadual | decimal | Alíquota padrão interestadual (ex: 12%, 7%)             |
| para\_pf      | decimal | (opcional) Se cliente é PF em outro estado (DIFAL)      |
| para\_pj      | decimal | (opcional) Se cliente é PJ contribuinte em outro estado |

## cest (~1800 linhas) (opcional para descrição)

| Campo     | Tipo   | Descrição            |
| --------- | ------ | -------------------- |
| cest      | string | Código CEST          |
| descricao | string | Descrição resumida   |
| ncm       | string | Código NCM vinculado |
