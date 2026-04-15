<?php

namespace Imposto\Fiscal\RegimeTributario;

use Imposto\Catalogo\UFs\UF;
use Imposto\Domain\NotaFiscal\NotaFiscalInterface;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CST\CSOSN;
use Imposto\Fiscal\CST\SituacaoTributariaInterface;

class SimplesNacional implements RegimeTributarioInterface
{
    public function getCodigoRegimeTributario(): int
    {
        return 1;
    }

    public function getAliquotaICMS(NCM $ncm, UF $origem, UF $destino, SituacaoTributariaInterface $situacao): float
    {
        return 0.0;
    }

    public function getAliquotaIPI(NCM $ncm, SituacaoTributariaInterface $situacao): float
    {
        return 0.0;
    }

    public function getDIFAL(NCM $ncm, UF $origem, UF $destino): float
    {
        return 0.0;
    }

    public function getDeveGerarGNRE(NotaFiscalInterface $notaFiscal): bool
    {
        return false;
    }

    public function getBlocoICMSXml(NCM $ncm, SituacaoTributariaInterface $situacao, int $origemMercadoria, UF $origem, UF $destino, float $baseCalculo): array
    {
        $csosn = $situacao instanceof CSOSN ? $situacao : CSOSN::NaoTributada;

        return match($csosn) {
            CSOSN::STRecolhidaAnteriormente => [
                'tag'    => 'ICMSSN500',
                'campos' => ['orig' => $origemMercadoria, 'CSOSN' => '500', 'vBCSTRet' => '0.00', 'pST' => '0.00', 'vICMSSTRet' => '0.00'],
            ],
            CSOSN::TributadaComPermissaoDeCredito => [
                'tag'    => 'ICMSSN101',
                'campos' => ['orig' => $origemMercadoria, 'CSOSN' => '101', 'pCredSN' => '0.00', 'vCredICMSSN' => '0.00'],
            ],
            CSOSN::TributadaComCreditoEComST => [
                'tag'    => 'ICMSSN201',
                'campos' => ['orig' => $origemMercadoria, 'CSOSN' => '201', 'modBCST' => 4, 'pMVAST' => '0.00', 'vBCST' => '0.00', 'pICMSST' => '0.00', 'vICMSST' => '0.00', 'pCredSN' => '0.00', 'vCredICMSSN' => '0.00'],
            ],
            CSOSN::TributadaSemCreditoEComST, CSOSN::IsentaPorFaixaComST => [
                'tag'    => 'ICMSSN202',
                'campos' => ['orig' => $origemMercadoria, 'CSOSN' => $csosn->value, 'modBCST' => 4, 'pMVAST' => '0.00', 'vBCST' => '0.00', 'pICMSST' => '0.00', 'vICMSST' => '0.00'],
            ],
            CSOSN::Outros => [
                'tag'    => 'ICMSSN900',
                'campos' => ['orig' => $origemMercadoria, 'CSOSN' => '900', 'modBC' => 3, 'vBC' => number_format($baseCalculo, 2, '.', ''), 'pICMS' => '0.00', 'vICMS' => '0.00'],
            ],
            default => [
                'tag'    => 'ICMSSN' . $csosn->value,
                'campos' => ['orig' => $origemMercadoria, 'CSOSN' => $csosn->value],
            ],
        };
    }

    public function getBlocoIPIXml(NCM $ncm, SituacaoTributariaInterface $situacao, float $baseCalculo): array
    {
        return [
            'tag'    => 'IPINT',
            'campos' => ['cEnq' => '001', 'CST' => '53'],
        ];
    }

    public function getBlocoPISXml(SituacaoTributariaInterface $situacao, float $baseCalculo): array
    {
        return [
            'tag'    => 'PISNT',
            'campos' => ['CST' => '07'],
        ];
    }

    public function getBlocoCOFINSXml(SituacaoTributariaInterface $situacao, float $baseCalculo): array
    {
        return [
            'tag'    => 'COFINSNT',
            'campos' => ['CST' => '07'],
        ];
    }
}
