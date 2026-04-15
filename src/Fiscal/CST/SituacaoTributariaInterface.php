<?php

namespace Imposto\Fiscal\CST;

/**
 * Interface comum para CST (Regime Normal) e CSOSN (Simples Nacional).
 * Permite que ItemPedido aceite qualquer um dos dois sistemas sem saber qual é.
 */
interface SituacaoTributariaInterface
{
    public function getCodigo(): string;
}
