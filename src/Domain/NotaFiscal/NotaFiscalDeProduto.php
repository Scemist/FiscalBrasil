<?php

namespace Imposto\Domain\NotaFiscal;

use Imposto\Catalogo\UFs\UF;
use Imposto\Domain\Destinatario\Destinatario;
use Imposto\Domain\Emitente\Emitente;
use Imposto\Domain\Pedido\ItemPedido;
use Imposto\Fiscal\RegimeTributario\RegimeTributarioInterface;
use Imposto\Fiscal\XML\NFeBuilder;

class NotaFiscalDeProduto implements NotaFiscalInterface
{
    public function __construct(
        private array $itens,
        private RegimeTributarioInterface $regimeTributario,
        private Emitente $emitente,
        private Destinatario $destinatario,
        private bool $consumidorFinal = true,
        private bool $presencial = false,
    ) {
        foreach ($this->itens as $item)
            $item->setNotaFiscal($this);
    }

    public function getItens(): array { return $this->itens; }
    public function getRegimeTributario(): RegimeTributarioInterface { return $this->regimeTributario; }
    public function getEmitente(): Emitente { return $this->emitente; }
    public function getDestinatario(): Destinatario { return $this->destinatario; }
    public function isConsumidorFinal(): bool { return $this->consumidorFinal; }
    public function isPresencial(): bool { return $this->presencial; }

    public function getOrigem(): UF
    {
        return $this->emitente->getEndereco()->getEstado();
    }

    public function getDestino(): UF
    {
        return $this->destinatario->getEndereco()->getEstado();
    }

    public function getSubtotal(): float
    {
        return array_reduce($this->itens, fn(float $total, ItemPedido $item) => $total + $item->getSubtotal(), 0.0);
    }

    public function getDesconto(): float
    {
        return array_reduce($this->itens, fn(float $total, ItemPedido $item) => $total + $item->getDesconto(), 0.0);
    }

    public function getValorLiquido(): float
    {
        return $this->getSubtotal() - $this->getDesconto();
    }

    public function getICMS(): float
    {
        return array_reduce($this->itens, fn(float $total, ItemPedido $item) => $total + $item->getICMS(), 0.0);
    }

    public function getIPI(): float
    {
        return array_reduce($this->itens, fn(float $total, ItemPedido $item) => $total + $item->getIPI(), 0.0);
    }

    public function getISS(): float
    {
        return 0.0;
    }

    public function getTotalComImpostos(): float
    {
        return $this->getValorLiquido() + $this->getICMS() + $this->getIPI();
    }

    public function getXml(array $config = []): string
    {
        return (new NFeBuilder())->build($this, $config);
    }
}
