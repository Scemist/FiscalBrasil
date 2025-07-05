Atualmente focando em venda de produtos no simples nacional

# NotaFiscal-> deve conter estes métodos

| Status |Descrição             | Método                  |
| ------ | -------------------- | ----------------------- |
| ✅     | Subtotal             | $notaFiscal->getSubtotal() |
| ✅     | ICMS                 | $notaFiscal->getICMS() |
| ✅     | IPI                  | $notaFiscal->getIPI() |
| ✅     | Total com Impostos   | $notaFiscal->getTotalComImpostos() |
| ✅     | Nota Fiscal          | $notaFiscal->getXml() |
| ❌     | ISO 8601 ou DateTime | $notaFiscal->getDataEmissao() |
| ❌     | UF Origem            | $notaFiscal->getOrigem() |
| ❌     | UF Destino           | $notaFiscal->getDestino() |
| ❌     | Simples Nacional etc | $notaFiscal->getRegimeTributario() |
| ❌     | Soma dos produtos    | $notaFiscal->getSubtotal() |
| ❌     | Total ICMS           | $notaFiscal->getICMS() |
| ❌     | Total IPI            | $notaFiscal->getIPI() |
| ❌     | Soma com ICMS + IPI  | $notaFiscal->getTotalComImpostos() |

# NotaFiscal->getXml() deve se parecer com isto:

```xml
<notaFiscal>
  <regimeTributario>Simples Nacional</regimeTributario>
  <origem>SP</origem>
  <destino>MG</destino>
  <tipoPessoa>PF</tipoPessoa>
  <consumidorFinal>true</consumidorFinal>
  <contribuinteICMS>false</contribuinteICMS>
  <presencial>false</presencial>
  <dataEmissao>2025-07-05T14:32:00-03:00</dataEmissao>

  <subtotal>1897.00</subtotal>
  <totalDesconto>0.00</totalDesconto>
  <totalICMS>227.64</totalICMS>
  <totalIPI>94.85</totalIPI>
  <totalComImpostos>2219.49</totalComImpostos>

  <itens>
    <item>
      <descricao>Guitarra Stratocaster</descricao>
      <quantidade>1</quantidade>
      <unidade>UN</unidade>
      <precoUnitario>1099.00</precoUnitario>
      <descontoPercentual>0.0</descontoPercentual>
      <valorTotal>1099.00</valorTotal>
      <ncm>9207.90.10</ncm>
      <cest>28.038.00</cest>
      <origem>0</origem>
      <cfop>5102</cfop>
      <cst>102</cst>
      <baseCalculoICMS>1099.00</baseCalculoICMS>
      <aliquotaICMS>12.00</aliquotaICMS>
      <valorICMS>131.88</valorICMS>
      <aliquotaIPI>5.00</aliquotaIPI>
      <valorIPI>54.95</valorIPI>
      <valorPIS>0.00</valorPIS>
      <valorCOFINS>0.00</valorCOFINS>
    </item>
    <item>
      <descricao>Pedal de Efeito</descricao>
      <quantidade>2</quantidade>
      <unidade>UN</unidade>
      <precoUnitario>399.00</precoUnitario>
      <descontoPercentual>0.0</descontoPercentual>
      <valorTotal>798.00</valorTotal>
      <ncm>9207.90.90</ncm>
      <cest>28.038.00</cest>
      <origem>0</origem>
      <cfop>5102</cfop>
      <cst>102</cst>
      <baseCalculoICMS>798.00</baseCalculoICMS>
      <aliquotaICMS>12.00</aliquotaICMS>
      <valorICMS>95.76</valorICMS>
      <aliquotaIPI>5.00</aliquotaIPI>
      <valorIPI>39.90</valorIPI>
      <valorPIS>0.00</valorPIS>
      <valorCOFINS>0.00</valorCOFINS>
    </item>
  </itens>
</notaFiscal>
```

# Tabelas auxiliares que serão neccessárias no código fonte

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
