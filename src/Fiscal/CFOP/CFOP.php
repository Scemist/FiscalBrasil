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
		# Todo: buscar do DB ou config
		return '';
	}

	public function isSaida(): bool
	{
		return str_starts_with($this->codigo, '2') || str_starts_with($this->codigo, '5');
	}

	public function isEntrada(): bool
	{
		return str_starts_with($this->codigo, '1') || str_starts_with($this->codigo, '4');
	}

	public function isInterestadual(): bool
	{
		# Exemplo simples: pode depender do código, mas geralmente 6xxx e 7xxx
		return str_starts_with($this->codigo, '6') || str_starts_with($this->codigo, '7');
	}

	public function getTipoOperacao(): string
	{
		# Pode retornar valores como 'venda', 'compra', 'transferência', etc
		return '';
	}

	public function exigeNFe(): bool
	{
		# Indica se gera NF-e para esta CFOP
		return true;
	}

	public function isDevolucao(): bool
	{
		# Verifica se CFOP representa devolução
		return false;
	}
}
