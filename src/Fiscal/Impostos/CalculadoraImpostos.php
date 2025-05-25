<?php

namespace Imposto\Fiscal\Impostos;

class CalculadoraImpostos
{
	public function calcularParaPedido(
		float $valorTotal,
		float $aliquotaICMS,
		float $aliquotaIPI,
	): array {
		$icms = $this->calcularICMS($valorTotal, $aliquotaICMS);
		$ipi = $this->calcularIPI($valorTotal, $aliquotaIPI);

		return [
			'ICMS' => $icms,
			'IPI' => $ipi,
			'TOTAL' => $icms + $ipi,
		];
	}
}