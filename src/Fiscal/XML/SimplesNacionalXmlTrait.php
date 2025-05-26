<?php

namespace Imposto\Domain\NotaFiscal;

trait SimplesNacionalXmlTrait
{
	public function toXml(): string
	{
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xml .= "<NFe>\n";
		$xml .= "  <infNFe>\n";

		// Dados básicos da nota
		$xml .= "    <ide>\n";
		$xml .= "      <nNF>" . htmlspecialchars($this->numero, ENT_XML1, 'UTF-8') . "</nNF>\n";
		$xml .= "      <serie>" . htmlspecialchars($this->serie, ENT_XML1, 'UTF-8') . "</serie>\n";
		$xml .= "      <dhEmi>" . $this->dataEmissao->format('Y-m-d\TH:i:sP') . "</dhEmi>\n";
		$xml .= "      <tpNF>1</tpNF> <!-- 1=Saída, 0=Entrada -->\n";
		$xml .= "      <idDest>1</idDest> <!-- 1=Interna, 2=Interestadual, 3=Exterior -->\n";
		$xml .= "      <cMunFG>" . $this->codigoMunicipio . "</cMunFG>\n";
		$xml .= "      <tpImp>1</tpImp> <!-- Formato do DANFE -->\n";
		$xml .= "    </ide>\n";

		// Emitente
		$xml .= "    <emit>\n";
		$xml .= "      <CNPJ>" . $this->emitente->getCNPJ() . "</CNPJ>\n";
		$xml .= "      <xNome>" . htmlspecialchars($this->emitente->getNome(), ENT_XML1, 'UTF-8') . "</xNome>\n";
		$xml .= "      <enderEmit>\n";
		$xml .= "        <xLgr>" . htmlspecialchars($this->emitente->getEndereco()->getLogradouro(), ENT_XML1, 'UTF-8') . "</xLgr>\n";
		$xml .= "        <nro>" . htmlspecialchars($this->emitente->getEndereco()->getNumero(), ENT_XML1, 'UTF-8') . "</nro>\n";
		$xml .= "        <xBairro>" . htmlspecialchars($this->emitente->getEndereco()->getBairro(), ENT_XML1, 'UTF-8') . "</xBairro>\n";
		$xml .= "        <cMun>" . $this->emitente->getEndereco()->getCodigoMunicipio() . "</cMun>\n";
		$xml .= "        <UF>" . $this->emitente->getEndereco()->getUF() . "</UF>\n";
		$xml .= "        <CEP>" . $this->emitente->getEndereco()->getCEP() . "</CEP>\n";
		$xml .= "      </enderEmit>\n";
		$xml .= "      <IE>" . $this->emitente->getInscricaoEstadual() . "</IE>\n";
		$xml .= "    </emit>\n";

		// Destinatário
		$xml .= "    <dest>\n";
		$xml .= "      <CNPJ>" . $this->destinatario->getCNPJ() . "</CNPJ>\n";
		$xml .= "      <xNome>" . htmlspecialchars($this->destinatario->getNome(), ENT_XML1, 'UTF-8') . "</xNome>\n";
		$xml .= "      <enderDest>\n";
		$xml .= "        <xLgr>" . htmlspecialchars($this->destinatario->getEndereco()->getLogradouro(), ENT_XML1, 'UTF-8') . "</xLgr>\n";
		$xml .= "        <nro>" . htmlspecialchars($this->destinatario->getEndereco()->getNumero(), ENT_XML1, 'UTF-8') . "</nro>\n";
		$xml .= "        <xBairro>" . htmlspecialchars($this->destinatario->getEndereco()->getBairro(), ENT_XML1, 'UTF-8') . "</xBairro>\n";
		$xml .= "        <cMun>" . $this->destinatario->getEndereco()->getCodigoMunicipio() . "</cMun>\n";
		$xml .= "        <UF>" . $this->destinatario->getEndereco()->getUF() . "</UF>\n";
		$xml .= "        <CEP>" . $this->destinatario->getEndereco()->getCEP() . "</CEP>\n";
		$xml .= "      </enderDest>\n";
		$xml .= "      <indIEDest>1</indIEDest> <!-- 1=Contribuinte ICMS -->\n";
		$xml .= "      <IE>" . $this->destinatario->getInscricaoEstadual() . "</IE>\n";
		$xml .= "    </dest>\n";

		// Itens
		foreach ($this->itens as $index => $item) {
			$numItem = $index + 1;
			$xml .= "    <det nItem=\"$numItem\">\n";
			$xml .= "      <prod>\n";
			$xml .= "        <cProd>" . htmlspecialchars($item->getCodigoProduto(), ENT_XML1, 'UTF-8') . "</cProd>\n";
			$xml .= "        <xProd>" . htmlspecialchars($item->getNome(), ENT_XML1, 'UTF-8') . "</xProd>\n";
			$xml .= "        <NCM>" . $item->getNCM()->getCodigo() . "</NCM>\n";
			$xml .= "        <CFOP>" . $item->getCFOP()->getCodigo() . "</CFOP>\n";
			$xml .= "        <uCom>" . htmlspecialchars($item->getUnidade(), ENT_XML1, 'UTF-8') . "</uCom>\n";
			$xml .= "        <qCom>" . number_format($item->getQuantidade(), 4, '.', '') . "</qCom>\n";
			$xml .= "        <vUnCom>" . number_format($item->getPreco(), 2, '.', '') . "</vUnCom>\n";
			$xml .= "        <vProd>" . number_format($item->getSubtotal(), 2, '.', '') . "</vProd>\n";
			$xml .= "      </prod>\n";

			// Impostos - ICMS (exemplo simplificado)
			$xml .= "      <imposto>\n";
			$xml .= "        <ICMS>\n";
			$xml .= "          <ICMS00>\n";
			$xml .= "            <orig>0</orig> <!-- Origem da mercadoria -->\n";
			$xml .= "            <CST>" . $item->getCST()->getCodigo() . "</CST>\n";
			$xml .= "            <modBC>0</modBC>\n";
			$xml .= "            <vBC>" . number_format($item->getSubtotal(), 2, '.', '') . "</vBC>\n";
			$xml .= "            <pICMS>" . number_format($item->getAliquotaICMS() * 100, 2, '.', '') . "</pICMS>\n";
			$xml .= "            <vICMS>" . number_format($item->getICMS(), 2, '.', '') . "</vICMS>\n";
			$xml .= "          </ICMS00>\n";
			$xml .= "        </ICMS>\n";

			// IPI (simplificado)
			$xml .= "        <IPI>\n";
			$xml .= "          <cEnq>999</cEnq> <!-- Código de enquadramento -->\n";
			$xml .= "          <IPITrib>\n";
			$xml .= "            <vBC>" . number_format($item->getSubtotal(), 2, '.', '') . "</vBC>\n";
			$xml .= "            <pIPI>0.00</pIPI>\n";
			$xml .= "            <vIPI>0.00</vIPI>\n";
			$xml .= "          </IPITrib>\n";
			$xml .= "        </IPI>\n";

			// PIS (simplificado)
			$xml .= "        <PIS>\n";
			$xml .= "          <PISAliq>\n";
			$xml .= "            <vBC>" . number_format($item->getSubtotal(), 2, '.', '') . "</vBC>\n";
			$xml .= "            <pPIS>0.65</pPIS>\n";
			$xml .= "            <vPIS>" . number_format($item->getPis(), 2, '.', '') . "</vPIS>\n";
			$xml .= "          </PISAliq>\n";
			$xml .= "        </PIS>\n";

			// COFINS (simplificado)
			$xml .= "        <COFINS>\n";
			$xml .= "          <COFINSAliq>\n";
			$xml .= "            <vBC>" . number_format($item->getSubtotal(), 2, '.', '') . "</vBC>\n";
			$xml .= "            <pCOFINS>3.00</pCOFINS>\n";
			$xml .= "            <vCOFINS>" . number_format($item->getCofins(), 2, '.', '') . "</vCOFINS>\n";
			$xml .= "          </COFINSAliq>\n";
			$xml .= "        </COFINS>\n";

			$xml .= "      </imposto>\n";
			$xml .= "    </det>\n";
		}

		// Totais
		$xml .= "    <total>\n";
		$xml .= "      <ICMSTot>\n";
		$xml .= "        <vBC>" . number_format($this->getTotalBaseICMS(), 2, '.', '') . "</vBC>\n";
		$xml .= "        <vICMS>" . number_format($this->getTotalICMS(), 2, '.', '') . "</vICMS>\n";
		$xml .= "        <vProd>" . number_format($this->getTotalProdutos(), 2, '.', '') . "</vProd>\n";
		$xml .= "        <vNF>" . number_format($this->getValorTotalNota(), 2, '.', '') . "</vNF>\n";
		$xml .= "      </ICMSTot>\n";
		$xml .= "    </total>\n";

		$xml .= "  </infNFe>\n";
		$xml .= "</NFe>";

		return $xml;
	}
}
