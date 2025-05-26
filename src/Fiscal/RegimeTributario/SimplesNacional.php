<?php

namespace Imposto\Fiscal\RegimeTributario;

use Imposto\Catalogo\UFs\UF;
use Imposto\Domain\NotaFiscal\NotaFiscalDeProduto;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CST\CST;

class SimplesNacional implements RegimeTributarioInterface
{
	public function getAliquotaIPI(NCM $ncm, CST $cst): float
	{
		if ($this->getCstIndicaIsencaoDeIPI($cst))
			return 0.0;

		if (in_array($cst->getCodigo(), ['050', '099']))
			return 0.10;

		return 0.05;
	}

	public function getAliquotaICMS(NCM $ncm, UF $ufOrigem, UF $ufDestino, CST $cst): float
	{
		if ($this->getCstIndicaIsencaoDeICMS($cst))
			return 0.0;

		if ($cst->getCodigo() === '000' && $this->getIsInterestadual($ufOrigem, $ufDestino))
			return 0.12;

		if ($cst->getCodigo() === '000')
			return 0.18;

		return 0.0;
	}

	private function getIsInterestadual(UF $ufOrigem, UF $ufDestino): bool
	{
		return $ufOrigem !== $ufDestino;
	}

	public function getAliquotaISS(): float
	{
		# Simples Nacional geralmente não tem ISS sobre produtos
		return 0.0;
	}

	public function getAliquotaPIS(NCM $ncm, CST $cst): float
	{
		return 0.0165;
	}

	public function getAliquotaCOFINS(NCM $ncm, CST $cst): float
	{
		return 0.076;
	}

	public function getCstIndicaIsencaoDeIPI(CST $cst): bool
	{
		return in_array($cst->getCodigo(), ['040', '041', '050']);
	}

	public function getCstIndicaIsencaoDeICMS(CST $cst): bool
	{
		return in_array($cst->getCodigo(), ['040', '041', '060']);
	}

	public function getXml(NotaFiscalDeProduto $notaFiscal): string
	{
		$xml = "<notaFiscal>\n";
		$xml .= "  <regimeTributario>Simples Nacional</regimeTributario>\n";
		$xml .= "  <origem>" . $notaFiscal->getOrigem()->value . "</origem>\n";
		$xml .= "  <destino>" . $notaFiscal->getDestino()->value . "</destino>\n";
		$xml .= "  <dataEmissao>" . date('Y-m-d\TH:i:sP') . "</dataEmissao>\n";
		$xml .= "  <subtotal>" . number_format($notaFiscal->getSubtotal(), 2, '.', '') . "</subtotal>\n";
		$xml .= "  <icms>" . number_format($notaFiscal->getICMS(), 2, '.', '') . "</icms>\n";

		foreach ($notaFiscal->getItens() as $item) {
			$xml .= "  <item>\n";
			$xml .= "    <descricao>" . htmlspecialchars($item->getNome(), ENT_XML1, 'UTF-8') . "</descricao>\n";
			$xml .= "    <quantidade>" . (int)$item->getQuantidade() . "</quantidade>\n";
			$xml .= "    <preco>" . number_format($item->getPreco(), 2, '.', '') . "</preco>\n";
			$xml .= "    <icms>" . number_format($item->getICMS(), 2, '.', '') . "</icms>\n";
			$xml .= "    <ipi>" . number_format($item->getIPI(), 2, '.', '') . "</ipi>\n";
			$xml .= "    <pis>" . number_format($item->getPis(), 2, '.', '') . "</pis>\n";
			$xml .= "    <cofins>" . number_format($item->getCofins(), 2, '.', '') . "</cofins>\n";
			$xml .= "  </item>\n";
		}

		$xml .= "</notaFiscal>";

		return $xml;
	}
}
