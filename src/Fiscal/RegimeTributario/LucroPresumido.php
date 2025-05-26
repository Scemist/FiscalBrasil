<?php

namespace Imposto\Fiscal\RegimeTributario;

use Imposto\Catalogo\UFs\UF;
use Imposto\Domain\NotaFiscal\NotaFiscalDeProduto;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CST\CST;
use Imposto\Fiscal\GNRE\GNRE;

class LucroPresumido implements RegimeTributarioInterface
{
	public function calcularDIFAL(ItemPedido $item, UF $ufOrigem, UF $ufDestino): float
	{
		// Lógica simplificada para cálculo do DIFAL

		// Se origem e destino são diferentes, aplica a regra do DIFAL
		if ($ufOrigem === $ufDestino) {
			return 0.0;
		}

		// Suponha que o ICMS interno do estado destino seja 18%
		$aliquotaInternaDestino = 0.18;

		// Suponha que a alíquota interestadual seja 12%
		$aliquotaInterestadual = 0.12;

		// Base de cálculo é o preço total do item
		$baseCalculo = $item->getPreco() * $item->getQuantidade();

		// DIFAL é a diferença entre a alíquota interna e interestadual aplicada na base
		$difal = ($aliquotaInternaDestino - $aliquotaInterestadual) * $baseCalculo;

		// Evita valor negativo (caso seja)
		return max(0, $difal);
	}
}
