<?php

namespace Imposto\Fiscal\RegimeTributario;

class SimplesNacional implements RegimeTributarioInterface
{
	public function calcularICMS(array $itens): float
	{
		$total = 0.0;

		foreach ($itens as $item) {
			$produto = $item->getItem();
			$cst = $produto->getCST()->getCodigo();

			// CSTs que indicam isenção ou substituição tributária
			if (in_array($cst, ['040', '041', '060'])) {
				continue; // Isento ou ST
			}

			// Exemplo: aplica ICMS de 12%
			$aliquota = 0.12;
			$total += $item->getSubtotal() * $aliquota;
		}

		return $total;
	}

	public function calcularIPI(array $itens): float
	{
		$total = 0.0;

		foreach ($itens as $item) {
			$produto = $item->getItem();
			$cst = $produto->getCST()->getCodigo();

			// IPI geralmente não incide em itens isentos ou com CST especial
			if (in_array($cst, ['040', '041', '050'])) {
				continue;
			}

			// Exemplo: aplica IPI de 5%
			$aliquota = 0.05;
			$total += $item->getSubtotal() * $aliquota;
		}

		return $total;
	}

	public function calcularISS(array $itens): float
	{
		// No Simples Nacional, nota fiscal de produto não tem ISS
		return 0.0;
	}
}