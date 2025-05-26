<?php

namespace Imposto\Domain\NotaFiscal;

use Imposto\Catalogo\UFs\UF;
use Imposto\Domain\NotaFiscal\NotaFiscalInterface;
use Imposto\Fiscal\RegimeTributario\RegimeTributarioInterface;
use Imposto\Domain\Pedido\ItemPedido;
use Imposto\Domain\Pedido\Pedido;

# Nota Fiscal do tipo Produto
class NotaFiscalDeProduto implements NotaFiscalInterface
{
	public function __construct(
		private array $itens, # ItemPedido
		private RegimeTributarioInterface $regimeTributario,
		private UF $origem,
		private UF $destino,
	) {
		foreach ($this->itens as $item)
			$item->setNotaFiscal($this);
	}

	public function getRegimeTributario(): RegimeTributarioInterface
	{
		return $this->regimeTributario;
	}

	public function getXml(): string
	{
		return $this->regimeTributario->getXml($this->itens);
	}

	public function getSubtotal(): float
	{
		return array_reduce($this->itens, function ($total, ItemPedido $item) {
			return $total + $item->getSubtotal();
		}, 0.0);
	}

	public function getICMS(): float
	{
		return array_reduce($this->itens, function (float $total, ItemPedido $item) {
			return $total + $item->getICMS();
		}, 0.0);
	}

	public function getIPI(): float
	{
		return array_reduce($this->itens, function (float $total, ItemPedido $item) {
			return $total + $item->getIPI();
		}, 0.0);
	}

	public function getISS(): float
	{
		# Nota fiscal de produto não tem ISS
		return 0.0;
	}

	public function getTotalComImpostos(): float
	{
		return $this->getSubtotal()
			+ $this->getICMS()
			+ $this->getIPI();
	}

	public function getOrigem(): UF
	{
		return $this->origem;
	}

	public function getDestino(): UF
	{
		return $this->destino;
	}
}
