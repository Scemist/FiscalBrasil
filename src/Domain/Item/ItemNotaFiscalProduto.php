<?php

namespace Imposto\Domain\Item;

use Imposto\Domain\NotaFiscal\NotaFiscalInterface;
use Imposto\Fiscal\CST\CST;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CFOP\CFOPAbstract;

class ItemNotaFiscalProduto implements ItemFiscalInterface
{
	private ?NotaFiscalInterface $notaFiscal = null;

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

	public function getICMS(): float
	{
		$this->ensureNotaFiscal();

		return $this->notaFiscal->getRegimeTributario()->getAliquotaICMS() * $this->getSubtotal();
	}

	public function getIPI(): float
	{
		$this->ensureNotaFiscal();
		return 0.0;
	}

	public function getPis(): float
	{
		$this->ensureNotaFiscal();
		return 0.0;
	}

	public function getCofins(): float
	{
		$this->ensureNotaFiscal();
		return 0.0;
	}

	public function setNotaFiscal(NotaFiscalInterface $notaFiscal): void
	{
		$this->notaFiscal = $notaFiscal;
	}

	private function ensureNotaFiscal(): void
	{
		if ($this->notaFiscal === null)
			throw new \Exception("Nota fiscal não definida.");
	}
}
