<?php

namespace Imposto\Fiscal\CFOP;

class CFOPAbstract
{
	protected string $cfop;
	protected string $descricao;
	protected string $tipo;
	protected string $aplicacao;
	protected string $estadoOrigem;
	protected string $estadoDestino;

	public function __construct(
		?string $cfop = null,
		?string $descricao = null,
		?string $tipo = null,
		?string $aplicacao = null,
		?string $estadoOrigem = null,
		?string $estadoDestino = null,
	) {
		$this->cfop = $cfop;
		$this->descricao = $descricao;
		$this->tipo = $tipo;
		$this->aplicacao = $aplicacao;
		$this->estadoOrigem = $estadoOrigem;
		$this->estadoDestino = $estadoDestino;
	}
}