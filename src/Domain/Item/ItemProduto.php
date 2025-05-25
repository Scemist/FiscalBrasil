<?php

namespace Imposto\Domain\Item;

use Imposto\Fiscal\CST\CST;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CFOP\CFOPAbstract;

class ItemProduto implements ItemFiscalInterface
{
	public function __construct(
		private string $nome,
		private float $preco,
		private NCM $ncm,
		private CST $cst,
		private CFOPAbstract $cfop,
	) {}

	public function getNome(): string
	{
		return $this->nome;
	}

	public function getPreco(): float
	{
		return $this->preco;
	}

	public function getNCM(): NCM
	{
		return $this->ncm;
	}

	public function getCST(): CST
	{
		return $this->cst;
	}

	public function getCFOP(): CFOPAbstract
	{
		return $this->cfop;
	}
}
