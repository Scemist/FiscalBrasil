<?php

namespace Imposto\Fiscal\CFOP;

class CFOP
{
	public function __construct(private string $codigo) {}

	public function getCodigo(): string
	{
		return $this->codigo;
	}

	public function getDescricao(): string
	{
		# Todo: Buscar do banco de dados ou arquivo de configuração
		return '';
	}

	public function getIsSaida(): bool
	{
		# CFOPs iniciados com 2 são saídas
		return str_starts_with($this->codigo, '2');
	}

	public function getIsEntrada(): bool
	{
		# CFOPs iniciados com 1 são entradas
		return str_starts_with($this->codigo, '1');
	}
}
