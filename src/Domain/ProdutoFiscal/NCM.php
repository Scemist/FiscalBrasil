<?php

namespace Imposto\Domain\ProdutoFiscal;

class NCM
{
	public function __construct(
		private string $codigo,
	) {}

	public function getCodigo(): string
	{
		return $this->codigo;
	}
}
