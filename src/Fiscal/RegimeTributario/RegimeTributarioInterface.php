<?php

namespace Imposto\Fiscal\RegimeTributario;

use Imposto\Catalogo\UFs\UF;
use Imposto\Domain\NotaFiscal\NotaFiscalInterface;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CST\SituacaoTributariaInterface;

interface RegimeTributarioInterface
{
    /**
     * Código de Regime Tributário para o campo CRT da NF-e.
     * 1 = Simples Nacional, 2 = Simples Nacional (excesso receita), 3 = Regime Normal
     */
    public function getCodigoRegimeTributario(): int;

    public function getAliquotaICMS(NCM $ncm, UF $origem, UF $destino, SituacaoTributariaInterface $situacao): float;
    public function getAliquotaIPI(NCM $ncm, SituacaoTributariaInterface $situacao): float;
    public function getDIFAL(NCM $ncm, UF $origem, UF $destino): float;
    public function getDeveGerarGNRE(NotaFiscalInterface $notaFiscal): bool;

    /**
     * Retorna array com 'tag' (nome da subtag XML) e 'campos' (campo => valor)
     * para geração do bloco de imposto no NFeBuilder.
     */
    public function getBlocoICMSXml(NCM $ncm, SituacaoTributariaInterface $situacao, int $origemMercadoria, UF $origem, UF $destino, float $baseCalculo): array;
    public function getBlocoIPIXml(NCM $ncm, SituacaoTributariaInterface $situacao, float $baseCalculo): array;
    public function getBlocoPISXml(SituacaoTributariaInterface $situacao, float $baseCalculo): array;
    public function getBlocoCOFINSXml(SituacaoTributariaInterface $situacao, float $baseCalculo): array;
}
