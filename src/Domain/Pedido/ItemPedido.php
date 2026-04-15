<?php

namespace Imposto\Domain\Pedido;

use Imposto\Catalogo\Unidade\Unidade;
use Imposto\Domain\NotaFiscal\NotaFiscalInterface;
use Imposto\Domain\ProdutoFiscal\NCM;
use Imposto\Fiscal\CFOP\CFOP;
use Imposto\Fiscal\CST\SituacaoTributariaInterface;

class ItemPedido
{
    private ?NotaFiscalInterface $notaFiscal = null;

    public function __construct(
        private string $nome,
        private float $preco,
        private float $quantidade,
        private Unidade $unidade,
        private NCM $ncm,
        private SituacaoTributariaInterface $situacaoTributaria,
        private CFOP $cfop,
        private int $origemMercadoria = 0,
        private float $desconto = 0.0,
        private string $codigoInterno = '',
        private string $codigoBarras = 'SEM GTIN',
        private string $codigoBeneficio = '',
        private string $numeroPedido = '',
        private int $numeroItemPedido = 0,
        private string $cstIBSCBS = '410',
        private string $classeTributariaIBSCBS = '',
    ) {}

    public function getNome(): string { return $this->nome; }
    public function getPreco(): float { return $this->preco; }
    public function getQuantidade(): float { return $this->quantidade; }
    public function getUnidade(): Unidade { return $this->unidade; }
    public function getNCM(): NCM { return $this->ncm; }
    public function getSituacaoTributaria(): SituacaoTributariaInterface { return $this->situacaoTributaria; }
    public function getCFOP(): CFOP { return $this->cfop; }
    public function getOrigemMercadoria(): int { return $this->origemMercadoria; }
    public function getDesconto(): float { return $this->desconto; }
    public function getCodigoInterno(): string { return $this->codigoInterno; }
    public function getCodigoBarras(): string { return $this->codigoBarras; }
    public function getCodigoBeneficio(): string { return $this->codigoBeneficio; }
    public function getNumeroPedido(): string { return $this->numeroPedido; }
    public function getNumeroItemPedido(): int { return $this->numeroItemPedido; }
    public function getCstIBSCBS(): string { return $this->cstIBSCBS; }
    public function getClasseTributariaIBSCBS(): string { return $this->classeTributariaIBSCBS; }

    public function getSubtotal(): float
    {
        return $this->preco * $this->quantidade;
    }

    public function getValorLiquido(): float
    {
        return $this->getSubtotal() - $this->desconto;
    }

    public function getICMS(): float
    {
        $this->ensureNotaFiscal();

        $aliquota = $this->notaFiscal->getRegimeTributario()->getAliquotaICMS(
            $this->ncm,
            $this->notaFiscal->getOrigem(),
            $this->notaFiscal->getDestino(),
            $this->situacaoTributaria,
        );

        return $aliquota * $this->getValorLiquido();
    }

    public function getIPI(): float
    {
        $this->ensureNotaFiscal();

        $aliquota = $this->notaFiscal->getRegimeTributario()->getAliquotaIPI(
            $this->ncm,
            $this->situacaoTributaria,
        );

        return $aliquota * $this->getValorLiquido();
    }

    public function getPIS(): float
    {
        $this->ensureNotaFiscal();
        return 0.0;
    }

    public function getCOFINS(): float
    {
        $this->ensureNotaFiscal();
        return 0.0;
    }

    public function setNotaFiscal(NotaFiscalInterface $notaFiscal): void
    {
        $this->notaFiscal = $notaFiscal;
    }

    private function ensureNotaFiscal(): void
    {
        if ($this->notaFiscal === null)
            throw new \RuntimeException('A Nota Fiscal precisa ser definida antes de calcular impostos.');
    }
}
