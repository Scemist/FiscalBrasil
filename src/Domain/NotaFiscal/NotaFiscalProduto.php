<?php

namespace Imposto\Domain\NotaFiscal;

use Imposto\Domain\NotaFiscal\NotaFiscalInterface;
use Imposto\Fiscal\RegimeTributario\RegimeTributarioInterface;
use Imposto\Domain\Pedido\ItemPedido;

// Nota Fiscal do tipo Produto
class NotaFiscalProduto implements NotaFiscalInterface
{
	private array $itens; # ItemPedido
	private RegimeTributarioInterface $regimeTributario;

	public function __construct(array $itens, RegimeTributarioInterface $regimeTributario)
	{
		$this->itens = $itens;
		$this->regimeTributario = $regimeTributario;
	}

	public function getItens(): array
	{
		return $this->itens;
	}

	public function getRegimeTributario(): RegimeTributarioInterface
	{
		return $this->regimeTributario;
	}

	public function toXml(): string
	{
		$xml = "<notaFiscal>\n";

		foreach ($this->itens as $item) {
			$xml .= "  <item>\n";
			$xml .= "    <descricao>" . htmlspecialchars($item->getItem()->getNome(), ENT_XML1, 'UTF-8') . "</descricao>\n";
			$xml .= "    <quantidade>" . (int)$item->getQuantidade() . "</quantidade>\n";
			$xml .= "    <preco>" . (float)$item->getItem()->getPreco() . "</preco>\n";
			$xml .= "  </item>\n";
		}

		$xml .= "</notaFiscal>";
		return $xml;
	}

	public function getSubtotal(): float
	{
		return array_reduce($this->itens, function ($total, ItemPedido $item) {
			return $total + $item->getSubtotal();
		}, 0.0);
	}

	public function getICMS(): float
	{
		return $this->regimeTributario->calcularICMS($this->itens);
	}

	public function getIPI(): float
	{
		return $this->regimeTributario->calcularIPI($this->itens);
	}

	public function getISS(): float
	{
		// Nota fiscal de produto não tem ISS
		return 0.0;
	}

	public function getTotalComImpostos(): float
	{
		return $this->getSubtotal()
			+ $this->getICMS()
			+ $this->getIPI();
	}
}
