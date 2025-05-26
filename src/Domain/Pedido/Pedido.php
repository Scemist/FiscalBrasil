<?php

namespace Imposto\Domain\Pedido;

use Imposto\Catalogo\TipoPessoa\TipoPessoa;
use Imposto\Catalogo\UFs\UF;
use Imposto\Domain\Pedido\ItemPedido;
use Imposto\Domain\NotaFiscal\NotaFiscalInterface;
use Imposto\Fiscal\RegimeTributario\RegimeTributarioInterface;
use Imposto\Domain\NotaFiscal\NotaFiscalDeProduto;

class Pedido
{
	private array $itens = [];

	public function __construct(
		private RegimeTributarioInterface $regimeTributario,
		private UF $origem,
		private UF $destino,
		private TipoPessoa $tipoPessoa,
	) {}

	public function addItem(ItemPedido $item): void
	{
		$this->itens[] = $item;
	}

	public function getNotaFiscal(): NotaFiscalInterface
	{
		if (empty($this->itens))
			throw new \Exception("Pedido sem itens.");

		return new NotaFiscalDeProduto($this->itens, $this->regimeTributario, $this->origem, $this->destino);
	}

	public function getSubtotal(): float
	{
		return array_reduce($this->itens, function (float $total, ItemPedido $item) {
			return $total + $item->getSubtotal();
		}, 0.0);
	}
}
