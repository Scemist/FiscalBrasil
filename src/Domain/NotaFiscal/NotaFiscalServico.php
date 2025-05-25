<?php

namespace Imposto\Domain\NotaFiscal;

class NotaFiscalServico
{
	private string $descricao;
	private float $valorUnitario;
	private int $quantidade;

	public function __construct(string $descricao, float $valorUnitario, int $quantidade)
	{
		$this->descricao = $descricao;
		$this->valorUnitario = $valorUnitario;
		$this->quantidade = $quantidade;
	}

	public function getDescricao(): string
	{
		return $this->descricao;
	}

	public function getValorUnitario(): float
	{
		return $this->valorUnitario;
	}

	public function getQuantidade(): int
	{
		return $this->quantidade;
	}

	public function getTotal(): float
	{
		return $this->valorUnitario * $this->quantidade;
	}


}