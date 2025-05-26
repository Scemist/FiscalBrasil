<?php

namespace Imposto\Fiscal\CST;

class CST
{
	public function __construct(private string $codigo)
	{
		if (!preg_match('/^\d{3}$/', $codigo))
			throw new \Exception("CST [$codigo] inválido: deve conter 3 dígitos numéricos.");
	}

	public function getCodigo(): string
	{
		return $this->codigo;
	}
}
