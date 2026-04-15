<?php

namespace Imposto\Fiscal\XML;

use Imposto\Domain\Destinatario\Destinatario;
use Imposto\Domain\Emitente\Emitente;
use Imposto\Domain\NotaFiscal\NotaFiscalDeProduto;
use Imposto\Domain\Pedido\ItemPedido;
use Imposto\Fiscal\RegimeTributario\RegimeTributarioInterface;

/**
 * Gera o XML da NF-e 4.00 a partir dos objetos de domínio.
 * Responsável apenas pela serialização — cálculo de tributos fica no RegimeTributario.
 */
class NFeBuilder
{
    public function build(NotaFiscalDeProduto $nota, array $config = []): string
    {
        $serie      = $config['serie']              ?? 1;
        $numero     = $config['numero']             ?? 1;
        $ambiente   = $config['ambiente']           ?? 2;
        $naturezaOperacao = $config['naturezaOperacao'] ?? 'Venda';

        $emitente    = $nota->getEmitente();
        $destinatario = $nota->getDestinatario();
        $regime      = $nota->getRegimeTributario();
        $origem      = $nota->getOrigem();
        $destino     = $nota->getDestino();

        $idDest = $origem === $destino ? 1 : 2;
        $indFinal = $nota->isConsumidorFinal() ? 1 : 0;
        $indPres  = $nota->isPresencial() ? 1 : 2;

        $xml  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $xml .= '<NFe xmlns="http://www.portalfiscal.inf.br/nfe">' . "\n";
        $xml .= '  <infNFe versao="4.00">' . "\n";

        $xml .= $this->renderIde($emitente, $serie, $numero, $ambiente, $naturezaOperacao, $idDest, $indFinal, $indPres);
        $xml .= $this->renderEmit($emitente);
        $xml .= $this->renderDest($destinatario);

        foreach ($nota->getItens() as $index => $item) {
            $xml .= $this->renderDet($item, $index + 1, $regime, $origem, $destino);
        }

        $xml .= $this->renderTotal($nota);
        $xml .= $this->renderTransp();
        $xml .= $this->renderPag($nota->getTotalComImpostos());

        $xml .= '  </infNFe>' . "\n";
        $xml .= '</NFe>';

        return $xml;
    }

    private function renderIde(Emitente $emitente, int $serie, int $numero, int $ambiente, string $naturezaOperacao, int $idDest, int $indFinal, int $indPres): string
    {
        $cuf = $emitente->getEndereco()->getEstado()->getCodigoIBGE();
        $agora = (new \DateTimeImmutable())->format('Y-m-d\TH:i:sP');

        return "    <ide>\n"
            . "      <cUF>{$cuf}</cUF>\n"
            . "      <natOp>" . $this->esc($naturezaOperacao) . "</natOp>\n"
            . "      <mod>55</mod>\n"
            . "      <serie>{$serie}</serie>\n"
            . "      <nNF>{$numero}</nNF>\n"
            . "      <dhEmi>{$agora}</dhEmi>\n"
            . "      <dhSaiEnt>{$agora}</dhSaiEnt>\n"
            . "      <tpNF>1</tpNF>\n"
            . "      <idDest>{$idDest}</idDest>\n"
            . "      <cMunFG>" . $emitente->getEndereco()->getMunicipioCodigo() . "</cMunFG>\n"
            . "      <tpImp>1</tpImp>\n"
            . "      <tpEmis>1</tpEmis>\n"
            . "      <tpAmb>{$ambiente}</tpAmb>\n"
            . "      <finNFe>1</finNFe>\n"
            . "      <indFinal>{$indFinal}</indFinal>\n"
            . "      <indPres>{$indPres}</indPres>\n"
            . "      <procEmi>0</procEmi>\n"
            . "      <verProc>FiscalBrasil 1.0</verProc>\n"
            . "    </ide>\n";
    }

    private function renderEmit(Emitente $emitente): string
    {
        $end = $emitente->getEndereco();
        $xml = "    <emit>\n"
            . "      <CNPJ>" . $emitente->getCNPJ() . "</CNPJ>\n"
            . "      <xNome>" . $this->esc($emitente->getRazaoSocial()) . "</xNome>\n"
            . "      <xFant>" . $this->esc($emitente->getNomeFantasia()) . "</xFant>\n"
            . "      <enderEmit>\n"
            . "        <xLgr>" . $this->esc($end->getLogradouro()) . "</xLgr>\n"
            . "        <nro>" . $this->esc($end->getNumero()) . "</nro>\n";

        if ($end->getComplemento() !== '')
            $xml .= "        <xCpl>" . $this->esc($end->getComplemento()) . "</xCpl>\n";

        $xml .= "        <xBairro>" . $this->esc($end->getBairro()) . "</xBairro>\n"
            . "        <cMun>" . $end->getMunicipioCodigo() . "</cMun>\n"
            . "        <xMun>" . $this->esc($end->getMunicipioNome()) . "</xMun>\n"
            . "        <UF>" . $end->getEstado()->value . "</UF>\n"
            . "        <CEP>" . $end->getCEP() . "</CEP>\n"
            . "        <cPais>" . $end->getPaisCodigo() . "</cPais>\n"
            . "        <xPais>" . $this->esc($end->getPaisNome()) . "</xPais>\n";

        if ($end->getTelefone() !== '')
            $xml .= "        <fone>" . $end->getTelefone() . "</fone>\n";

        $xml .= "      </enderEmit>\n"
            . "      <IE>" . $emitente->getInscricaoEstadual() . "</IE>\n";

        if ($emitente->getInscricaoMunicipal() !== '')
            $xml .= "      <IM>" . $emitente->getInscricaoMunicipal() . "</IM>\n";

        $xml .= "      <CRT>" . $emitente->getCodigoRegimeTributario() . "</CRT>\n"
            . "    </emit>\n";

        return $xml;
    }

    private function renderDest(Destinatario $destinatario): string
    {
        $end = $destinatario->getEndereco();
        $tagDocumento = $destinatario->isPessoaFisica() ? 'CPF' : 'CNPJ';

        $xml = "    <dest>\n"
            . "      <{$tagDocumento}>" . $destinatario->getDocumento() . "</{$tagDocumento}>\n"
            . "      <xNome>" . $this->esc($destinatario->getNome()) . "</xNome>\n"
            . "      <enderDest>\n"
            . "        <xLgr>" . $this->esc($end->getLogradouro()) . "</xLgr>\n"
            . "        <nro>" . $this->esc($end->getNumero()) . "</nro>\n";

        if ($end->getComplemento() !== '')
            $xml .= "        <xCpl>" . $this->esc($end->getComplemento()) . "</xCpl>\n";

        $xml .= "        <xBairro>" . $this->esc($end->getBairro()) . "</xBairro>\n"
            . "        <cMun>" . $end->getMunicipioCodigo() . "</cMun>\n"
            . "        <xMun>" . $this->esc($end->getMunicipioNome()) . "</xMun>\n"
            . "        <UF>" . $end->getEstado()->value . "</UF>\n"
            . "        <CEP>" . $end->getCEP() . "</CEP>\n"
            . "        <cPais>" . $end->getPaisCodigo() . "</cPais>\n"
            . "        <xPais>" . $this->esc($end->getPaisNome()) . "</xPais>\n";

        if ($end->getTelefone() !== '')
            $xml .= "        <fone>" . $end->getTelefone() . "</fone>\n";

        $xml .= "      </enderDest>\n"
            . "      <indIEDest>" . $destinatario->getIndicadorIE() . "</indIEDest>\n";

        if ($destinatario->getInscricaoEstadual() !== '')
            $xml .= "      <IE>" . $destinatario->getInscricaoEstadual() . "</IE>\n";

        $xml .= "    </dest>\n";

        return $xml;
    }

    private function renderDet(ItemPedido $item, int $numero, RegimeTributarioInterface $regime, $origem, $destino): string
    {
        $ncm         = $item->getNCM();
        $situacao    = $item->getSituacaoTributaria();
        $baseCalculo = $item->getValorLiquido();

        $xml = "    <det nItem=\"{$numero}\">\n"
            . "      <prod>\n"
            . "        <cProd>" . $this->esc($item->getCFOP()->getCodigo()) . "</cProd>\n"
            . "        <xProd>" . $this->esc($item->getNome()) . "</xProd>\n"
            . "        <NCM>" . $ncm->getCodigo() . "</NCM>\n"
            . "        <CFOP>" . $item->getCFOP()->getCodigo() . "</CFOP>\n"
            . "        <uCom>" . $item->getUnidade()->value . "</uCom>\n"
            . "        <qCom>" . number_format($item->getQuantidade(), 4, '.', '') . "</qCom>\n"
            . "        <vUnCom>" . number_format($item->getPreco(), 10, '.', '') . "</vUnCom>\n"
            . "        <vProd>" . number_format($item->getSubtotal(), 2, '.', '') . "</vProd>\n";

        if ($item->getDesconto() > 0)
            $xml .= "        <vDesc>" . number_format($item->getDesconto(), 2, '.', '') . "</vDesc>\n";

        $xml .= "        <indTot>1</indTot>\n"
            . "      </prod>\n"
            . "      <imposto>\n"
            . $this->renderBlocoImposto('ICMS', $regime->getBlocoICMSXml($ncm, $situacao, $item->getOrigemMercadoria(), $origem, $destino, $baseCalculo))
            . $this->renderBlocoImposto('IPI', $regime->getBlocoIPIXml($ncm, $situacao, $baseCalculo))
            . $this->renderBlocoImposto('PIS', $regime->getBlocoPISXml($situacao, $baseCalculo))
            . $this->renderBlocoImposto('COFINS', $regime->getBlocoCOFINSXml($situacao, $baseCalculo))
            . "      </imposto>\n"
            . "    </det>\n";

        return $xml;
    }

    private function renderBlocoImposto(string $grupoXml, array $bloco): string
    {
        $tag = $bloco['tag'];
        $xml = "        <{$grupoXml}><{$tag}>\n";
        foreach ($bloco['campos'] as $campo => $valor) {
            $xml .= "          <{$campo}>{$valor}</{$campo}>\n";
        }
        $xml .= "        </{$tag}></{$grupoXml}>\n";
        return $xml;
    }

    private function renderTotal(NotaFiscalDeProduto $nota): string
    {
        return "    <total>\n"
            . "      <ICMSTot>\n"
            . "        <vBC>0.00</vBC>\n"
            . "        <vICMS>" . number_format($nota->getICMS(), 2, '.', '') . "</vICMS>\n"
            . "        <vICMSDeson>0.00</vICMSDeson>\n"
            . "        <vFCP>0.00</vFCP>\n"
            . "        <vBCST>0.00</vBCST>\n"
            . "        <vST>0.00</vST>\n"
            . "        <vFCPST>0.00</vFCPST>\n"
            . "        <vFCPSTRet>0.00</vFCPSTRet>\n"
            . "        <vProd>" . number_format($nota->getSubtotal(), 2, '.', '') . "</vProd>\n"
            . "        <vFrete>0.00</vFrete>\n"
            . "        <vSeg>0.00</vSeg>\n"
            . "        <vDesc>" . number_format($nota->getDesconto(), 2, '.', '') . "</vDesc>\n"
            . "        <vII>0.00</vII>\n"
            . "        <vIPI>" . number_format($nota->getIPI(), 2, '.', '') . "</vIPI>\n"
            . "        <vIPIDevol>0.00</vIPIDevol>\n"
            . "        <vPIS>0.00</vPIS>\n"
            . "        <vCOFINS>0.00</vCOFINS>\n"
            . "        <vOutro>0.00</vOutro>\n"
            . "        <vNF>" . number_format($nota->getTotalComImpostos(), 2, '.', '') . "</vNF>\n"
            . "      </ICMSTot>\n"
            . "    </total>\n";
    }

    private function renderTransp(): string
    {
        return "    <transp>\n"
            . "      <modFrete>9</modFrete>\n"
            . "    </transp>\n";
    }

    private function renderPag(float $valorTotal): string
    {
        return "    <pag>\n"
            . "      <detPag>\n"
            . "        <tPag>99</tPag>\n"
            . "        <vPag>" . number_format($valorTotal, 2, '.', '') . "</vPag>\n"
            . "      </detPag>\n"
            . "    </pag>\n";
    }

    private function esc(string $valor): string
    {
        return htmlspecialchars($valor, ENT_XML1, 'UTF-8');
    }
}
