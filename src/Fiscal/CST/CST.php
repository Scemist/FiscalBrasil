<?php

namespace Imposto\Fiscal\CST;

class CST
{
	public function __construct(
		private string $codigo,
	) {}

	public function getCodigo(): string
	{
		return $this->codigo;
	}
}
