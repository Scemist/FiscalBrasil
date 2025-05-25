<?php

namespace Imposto\Domain\Pedido;

use Imposto\Domain\Pedido\ItemPedido;
use Imposto\Domain\NotaFiscal\NotaFiscalInterface;
use Imposto\Fiscal\RegimeTributario\RegimeTributarioInterface;
use Imposto\Domain\NotaFiscal\NotaFiscalProduto;

class Pedido
{
	private array $itens = [];
	private NotaFiscalInterface $notaFiscal;

	public function __construct(
		private RegimeTributarioInterface $regimeTributario
	) {}

	public function addItem(ItemPedido $item): void
	{
		$this->itens[] = $item;
	}

	public function getNotaFiscal(): NotaFiscalInterface
	{
		if (empty($this->itens))
			throw new \Exception("Pedido sem itens.");

		if (!isset($this->notaFiscal))
			$this->notaFiscal = new NotaFiscalProduto($this->itens, $this->regimeTributario);

		return $this->notaFiscal;
	}

	public function getSubtotal(): float
	{
		return array_reduce($this->itens, function (float $total, ItemPedido $item) {
			return $total + $item->getSubtotal();
		}, 0.0);
	}

	public function getICMS(): float
	{
		return $this->getNotaFiscal()->getICMS();
	}

	public function getIPI(): float
	{
		return $this->getNotaFiscal()->getIPI();
	}

	public function getTotalComImpostos(): float
	{
		return $this->getNotaFiscal()->getTotalComImpostos();
	}
}
