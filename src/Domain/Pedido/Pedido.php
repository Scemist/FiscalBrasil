<?php

namespace Imposto\Domain\Pedido;

use Imposto\Domain\Destinatario\Destinatario;
use Imposto\Domain\Emitente\Emitente;
use Imposto\Domain\NotaFiscal\NotaFiscalDeProduto;
use Imposto\Domain\NotaFiscal\NotaFiscalInterface;
use Imposto\Fiscal\RegimeTributario\RegimeTributarioInterface;

class Pedido
{
    private array $itens = [];

    public function __construct(
        private Emitente $emitente,
        private Destinatario $destinatario,
        private RegimeTributarioInterface $regimeTributario,
        private bool $consumidorFinal = true,
        private bool $presencial = false,
    ) {
        $this->validar();
    }

    private function validar(): void
    {
        if ($this->destinatario->isPessoaFisica() && $this->destinatario->isContribuinteICMS())
            throw new \InvalidArgumentException('Pessoa Física não pode ser contribuinte de ICMS.');

        if (!$this->destinatario->isPessoaFisica()
            && !$this->destinatario->isContribuinteICMS()
            && !$this->consumidorFinal)
            throw new \InvalidArgumentException('Empresa sem IE não pode ser considerada não consumidora final e não contribuinte ao mesmo tempo.');
    }

    public function addItem(ItemPedido $item): void
    {
        $this->itens[] = $item;
    }

    public function getNotaFiscal(): NotaFiscalInterface
    {
        if (empty($this->itens))
            throw new \RuntimeException('Pedido sem itens.');

        return new NotaFiscalDeProduto(
            itens: $this->itens,
            regimeTributario: $this->regimeTributario,
            emitente: $this->emitente,
            destinatario: $this->destinatario,
            consumidorFinal: $this->consumidorFinal,
            presencial: $this->presencial,
        );
    }

    public function getSubtotal(): float
    {
        return array_reduce($this->itens, fn(float $total, ItemPedido $item) => $total + $item->getSubtotal(), 0.0);
    }
}
