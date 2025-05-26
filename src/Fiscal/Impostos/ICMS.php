<?php

namespace Imposto\Fiscal\Impostos;

class ICMS
{
	public function calcularICMSST(ItemPedido $item, UF $ufOrigem, UF $ufDestino, bool $clienteContribuinteICMS): float
	{
		if (!$clienteContribuinteICMS) {
			// Se cliente não é contribuinte ICMS, não calcula ICMS-ST para ele
			return 0.0;
		}

		// Exemplo: busca a margem de valor agregado (MVA) para o produto (depende do NCM e UF)
		$mva = $this->buscarMVA($item->getNcm(), $ufDestino);

		// Base de cálculo ST = preço do produto * (1 + MVA)
		$baseCalculoST = $item->getPreco() * $item->getQuantidade() * (1 + $mva);

		// Alíquota interna do estado destino
		$aliquotaInternaDestino = 0.18; // Exemplo fixo, deveria vir de tabela

		// Alíquota interestadual
		$aliquotaInterestadual = 0.12; // Exemplo fixo, deveria vir de tabela

		// ICMS normal
		$icmsNormal = $aliquotaInterestadual * $item->getPreco() * $item->getQuantidade();

		// ICMS-ST = baseCalculoST * aliquotaInternaDestino - ICMS normal
		$icmsST = $baseCalculoST * $aliquotaInternaDestino - $icmsNormal;

		return max(0, $icmsST);
	}

	private function buscarMVA(string $ncm, UF $ufDestino): float
	{
		// Buscar MVA em tabela, por NCM e estado, ex:
		return 0.4; // Exemplo 40%
	}
}
