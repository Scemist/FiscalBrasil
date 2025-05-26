<?php

namespace Imposto\Domain\ProdutoFiscal;

class NCM
{
	public function __construct(private string $codigo)
	{
		if (!preg_match('/^\d{8}$/', $codigo))
			throw new \Exception("NCM [$codigo] inválido: deve conter 8 dígitos numéricos.");
	}

	public function getCodigo(): string
	{
		return $this->codigo;
	}
}
