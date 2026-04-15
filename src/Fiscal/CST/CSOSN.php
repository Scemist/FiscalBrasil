<?php

namespace Imposto\Fiscal\CST;

/**
 * Código de Situação da Operação no Simples Nacional.
 * Usado exclusivamente em NF-e de emitentes com CRT 1 (Simples Nacional ME/EPP).
 */
enum CSOSN: string implements SituacaoTributariaInterface
{
    case TributadaComPermissaoDeCredito         = '101';
    case TributadaSemPermissaoDeCredito         = '102';
    case IsentaPorFaixaDeReceitaBruta           = '103';
    case TributadaComCreditoEComST              = '201';
    case TributadaSemCreditoEComST              = '202';
    case IsentaPorFaixaComST                    = '203';
    case Imune                                  = '300';
    case NaoTributada                           = '400';
    case STRecolhidaAnteriormente               = '500';
    case Outros                                 = '900';

    public function getCodigo(): string
    {
        return $this->value;
    }

    public function isIsento(): bool
    {
        return match($this) {
            self::Imune, self::NaoTributada, self::STRecolhidaAnteriormente => true,
            default => false,
        };
    }

    public function temSubstituicaoTributaria(): bool
    {
        return match($this) {
            self::TributadaComCreditoEComST,
            self::TributadaSemCreditoEComST,
            self::IsentaPorFaixaComST,
            self::STRecolhidaAnteriormente => true,
            default => false,
        };
    }
}
