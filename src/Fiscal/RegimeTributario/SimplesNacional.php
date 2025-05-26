<?php

namespace Imposto\Fiscal\RegimeTributario;

use Imposto\Catalogo\UFs\UF;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CST\CST;

class SimplesNacional implements RegimeTributarioInterface
{
	// public function calcularICMS(array $itens, UF $origem, UF $destino): float
	// {
	// 	$total = 0.0;

	// 	foreach ($itens as $item) {
	// 		$produto = $item->getItem();
	// 		$cst = $produto->getCST()->getCodigo();

	// 		if ($this->getCstIndicaIsencaoDeICMS($cst))
	// 			continue;

	// 		$aliquota = $this->getAliquotaICMS(
	// 			$produto->getNCM()->getCodigo(),
	// 			$origem,
	// 			$destino,
	// 			$cst
	// 		);

	// 		$total += $item->getSubtotal() * $aliquota;
	// 	}

	// 	return $total;
	// }

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
		if ($cst === '000' && $ufOrigem !== $ufDestino)
			return 0.12;

		if ($cst === '000')
			return 0.18;

		// Outras regras...
		return 0.0;
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
