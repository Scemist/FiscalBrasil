<?php

namespace Imposto\Domain\Pedido;

use Imposto\Domain\NotaFiscal\NotaFiscalInterface;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CFOP\CFOPAbstract;
use Imposto\Fiscal\CST\CST;

class ItemPedido
{
	private ?NotaFiscalInterface $notaFiscal = null;

	public function __construct(
		private string $nome,
		private float $preco,
		private float $quantidade,
		private string $unidade,
		private NCM $ncm,
		private CST $cst,
		private CFOPAbstract $cfop,
	) {}

	public function getQuantidade(): int
	{
		return $this->quantidade;
	}

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

		$aliquota = $this->notaFiscal->getRegimeTributario()->getAliquotaICMS(
			$this->getNCM(),
			$this->notaFiscal->getOrigem(),
			$this->notaFiscal->getDestino(),
			$this->getCST(),
		);

		return $aliquota * $this->getSubtotal();
	}

	public function getSubtotal(): float
	{
		return $this->preco * $this->quantidade;
	}

	public function getIPI(): float
	{
		$this->ensureNotaFiscal();

		$aliquota = $this->notaFiscal->getRegimeTributario()->getAliquotaIPI(
			$this->getNCM(),
			$this->getCST(),
		);
		return $aliquota * $this->getSubtotal();
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
			throw new \Exception('A Nota Fiscal precisa ser definida para ter esta informação.');
	}
}
