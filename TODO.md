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
