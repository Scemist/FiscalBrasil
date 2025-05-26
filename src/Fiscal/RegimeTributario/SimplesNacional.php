<?php

namespace Imposto\Fiscal\RegimeTributario;

use Imposto\Catalogo\UFs\UF;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CST\CST;

class SimplesNacional implements RegimeTributarioInterface
{
	public function calcularIPI(array $itens): float
	{
		$total = 0.0;

		foreach ($itens as $item) {
			$cst = $item->getCST()->getCodigo();

			if ($this->getCstIndicaIsencaoDeIPI($cst))
				continue;

			$aliquota = $this->getAliquotaIPI(
				$item->getNCM()->getCodigo(),
				$cst
			);

			$total += $item->getSubtotal() * $aliquota;
		}

		return $total;
	}

	public function calcularISS(array $itens): float
	{
		return 0.0;
	}

	public function getAliquotaICMS(NCM $ncm, UF $ufOrigem, UF $ufDestino, CST $cst): float
	{
		// Exemplo de regra: interestadual com CST 000 -> 12%
		if ($cst->getCodigo() === '000' && $ufOrigem !== $ufDestino)
			return 0.12;

		if ($cst === '000')
			return 0.18;

		// Outras regras...
		return 0.0;
	}

	public function getXml(array $itens): string
	{
		$xml = "<notaFiscal>\n";

		foreach ($itens as $item) {
			$xml .= "  <item>\n";
			$xml .= "    <descricao>" . htmlspecialchars($item->getNome(), ENT_XML1, 'UTF-8') . "</descricao>\n";
			$xml .= "    <quantidade>" . (int)$item->getQuantidade() . "</quantidade>\n";
			$xml .= "    <preco>" . (float)$item->getPreco() . "</preco>\n";
			$xml .= "    <icms>" . (float)$item->getICMS() . "</icms>\n";
			$xml .= "  </item>\n";
		}

		$xml .= "</notaFiscal>";
		return $xml;
	}

	public function getAliquotaIPI(string $ncm, string $cst): float
	{
		// Exemplo de alíquota genérica
		if (in_array($cst, ['050', '099']))
			return 0.10;

		return 0.05;
	}

	public function getCstIndicaIsencaoDeIPI(string $cst): bool
	{
		return in_array($cst, ['040', '041', '050']);
	}

	public function getCstIndicaIsencaoDeICMS(string $cst): bool
	{
		return in_array($cst, ['040', '041', '060']);
	}
}
