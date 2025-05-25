<?php

namespace Imposto\Domain\Pedido;

use Imposto\Domain\Item\ItemFiscalInterface;

class ItemPedido
{
	public function __construct(
		private ItemFiscalInterface $item,
		private int $quantidade,
	) {}

	public function getItem(): ItemFiscalInterface
	{
		return $this->item;
	}

	public function getQuantidade(): int
	{
		return $this->quantidade;
	}

	public function getSubtotal(): float
	{
		return $this->item->getPreco() * $this->quantidade;
	}
}
