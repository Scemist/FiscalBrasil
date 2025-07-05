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
		private bool $consumidorFinal = false,
		private bool $contribuinteICMS = false,
		private bool $presencial = false,
	) {
		$this->validar();
	}

	private function validar(): void
	{
		if ($this->tipoPessoa === TipoPessoa::PF && $this->contribuinteICMS)
			throw new \Exception('Pessoa Física não pode ser contribuinte de ICMS.');

		if ($this->tipoPessoa === TipoPessoa::PJ && !$this->contribuinteICMS && $this->consumidorFinal === false)
			throw new \Exception('Empresa sem IE não pode ser considerada não consumidora final e não contribuinte ao mesmo tempo.');
	}

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
