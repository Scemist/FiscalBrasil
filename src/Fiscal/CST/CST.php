<?php

namespace Imposto\Fiscal\CST;

class CST implements SituacaoTributariaInterface
{
	public function __construct(private string $codigo)
	{
		if (!preg_match('/^\d{3}$/', $codigo))
			throw new \InvalidArgumentException("CST [{$codigo}] inválido: deve conter 3 dígitos numéricos.");
	}

	public function getCodigo(): string
	{
		return $this->codigo;
	}
}
