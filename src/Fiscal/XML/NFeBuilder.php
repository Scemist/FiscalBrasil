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
        $serie            = $config['serie']            ?? 1;
        $numero           = $config['numero']           ?? 1;
        $ambiente         = $config['ambiente']         ?? 2;
        $naturezaOperacao = $config['naturezaOperacao'] ?? 'Venda';
        $tpEmis           = $config['tpEmis']           ?? 1;
        $indIntermed      = $config['indIntermed']      ?? 0;

        $emitente     = $nota->getEmitente();
        $destinatario = $nota->getDestinatario();
        $regime       = $nota->getRegimeTributario();
        $origem       = $nota->getOrigem();
        $destino      = $nota->getDestino();

        $idDest   = $origem === $destino ? 1 : 2;
        $indFinal = $nota->isConsumidorFinal() ? 1 : 0;
        $indPres  = $nota->isPresencial() ? 1 : 2;

        $cNF     = $config['cNF'] ?? str_pad((string) random_int(1, 99999999), 8, '0', STR_PAD_LEFT);
        $chave43 = $this->buildChave43($emitente, $serie, $numero, $tpEmis, $cNF);
        $cDV     = $this->calcularCDV($chave43);
        $chave   = $chave43 . $cDV;

        $xml  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $xml .= '<NFe xmlns="http://www.portalfiscal.inf.br/nfe">' . "\n";
        $xml .= "  <infNFe versao=\"4.00\" Id=\"NFe{$chave}\">\n";

        $xml .= $this->renderIde($emitente, $serie, $numero, $cNF, $cDV, $ambiente, $naturezaOperacao, $idDest, $indFinal, $indPres, $indIntermed, $tpEmis);
        $xml .= $this->renderEmit($emitente);
        $xml .= $this->renderDest($destinatario);

        foreach ($nota->getItens() as $index => $item) {
            $xml .= $this->renderDet($item, $index + 1, $regime, $origem, $destino);
        }

        $xml .= $this->renderTotal($nota);
        $xml .= $this->renderTransp($config);
        $xml .= $this->renderPag($nota->getTotalComImpostos(), $config);

        if (isset($config['infIntermed']))
            $xml .= $this->renderInfoIntermed($config['infIntermed']);

        if (!empty($config['infCpl']))
            $xml .= $this->renderInfoAdic($config['infCpl']);

        if (isset($config['infRespTec']))
            $xml .= $this->renderInfoRespTec($config['infRespTec']);

        $xml .= '  </infNFe>' . "\n";
        $xml .= '</NFe>';

        return $xml;
    }

    private function renderIde(
        Emitente $emitente,
        int $serie,
        int $numero,
        string $cNF,
        int $cDV,
        int $ambiente,
        string $naturezaOperacao,
        int $idDest,
        int $indFinal,
        int $indPres,
        int $indIntermed,
        int $tpEmis,
    ): string {
        $cuf  = $emitente->getEndereco()->getEstado()->getCodigoIBGE();
        $agora = (new \DateTimeImmutable())->format('Y-m-d\TH:i:sP');

        return "    <ide>\n"
            . "      <cUF>{$cuf}</cUF>\n"
            . "      <cNF>{$cNF}</cNF>\n"
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
            . "      <tpEmis>{$tpEmis}</tpEmis>\n"
            . "      <cDV>{$cDV}</cDV>\n"
            . "      <tpAmb>{$ambiente}</tpAmb>\n"
            . "      <finNFe>1</finNFe>\n"
            . "      <indFinal>{$indFinal}</indFinal>\n"
            . "      <indPres>{$indPres}</indPres>\n"
            . "      <indIntermed>{$indIntermed}</indIntermed>\n"
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
            . "      <indIEDest>" . $destinatario->getIndicadorIE()->value . "</indIEDest>\n";

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
        $cProd       = $item->getCodigoInterno() !== '' ? $item->getCodigoInterno() : (string) $numero;
        $cEAN        = $item->getCodigoBarras();
        $unidade     = $item->getUnidade()->value;
        $quantidade  = number_format($item->getQuantidade(), 4, '.', '');
        $vUnCom      = number_format($item->getPreco(), 10, '.', '');

        $xml = "    <det nItem=\"{$numero}\">\n"
            . "      <prod>\n"
            . "        <cProd>" . $this->esc($cProd) . "</cProd>\n"
            . "        <cEAN>{$cEAN}</cEAN>\n"
            . "        <xProd>" . $this->esc($item->getNome()) . "</xProd>\n"
            . "        <NCM>" . $ncm->getCodigo() . "</NCM>\n";

        if ($item->getCodigoBeneficio() !== '')
            $xml .= "        <cBenef>" . $item->getCodigoBeneficio() . "</cBenef>\n";

        $xml .= "        <CFOP>" . $item->getCFOP()->getCodigo() . "</CFOP>\n"
            . "        <uCom>{$unidade}</uCom>\n"
            . "        <qCom>{$quantidade}</qCom>\n"
            . "        <vUnCom>{$vUnCom}</vUnCom>\n"
            . "        <vProd>" . number_format($item->getSubtotal(), 2, '.', '') . "</vProd>\n"
            . "        <cEANTrib>{$cEAN}</cEANTrib>\n"
            . "        <uTrib>{$unidade}</uTrib>\n"
            . "        <qTrib>{$quantidade}</qTrib>\n"
            . "        <vUnTrib>{$vUnCom}</vUnTrib>\n";

        if ($item->getDesconto() > 0)
            $xml .= "        <vDesc>" . number_format($item->getDesconto(), 2, '.', '') . "</vDesc>\n";

        $xml .= "        <indTot>1</indTot>\n";

        if ($item->getNumeroPedido() !== '')
            $xml .= "        <xPed>" . $this->esc($item->getNumeroPedido()) . "</xPed>\n";

        if ($item->getNumeroItemPedido() > 0)
            $xml .= "        <nItemPed>" . $item->getNumeroItemPedido() . "</nItemPed>\n";

        $xml .= "      </prod>\n"
            . "      <imposto>\n"
            . $this->renderBlocoImposto('ICMS', $regime->getBlocoICMSXml($ncm, $situacao, $item->getOrigemMercadoria(), $origem, $destino, $baseCalculo))
            . $this->renderBlocoImposto('IPI', $regime->getBlocoIPIXml($ncm, $situacao, $baseCalculo))
            . $this->renderBlocoImposto('PIS', $regime->getBlocoPISXml($situacao, $baseCalculo))
            . $this->renderBlocoImposto('COFINS', $regime->getBlocoCOFINSXml($situacao, $baseCalculo))
            . $this->renderBlocoIBSCBS($item)
            . "      </imposto>\n"
            . "    </det>\n";

        return $xml;
    }

    private function renderBlocoImposto(string $grupoXml, array $bloco): string
    {
        $tag = $bloco['tag'];
        $ind = '        ';

        $xml = "{$ind}<{$grupoXml}>\n";

        if (isset($bloco['pre_campos'])) {
            foreach ($bloco['pre_campos'] as $campo => $valor) {
                $xml .= "{$ind}  <{$campo}>{$valor}</{$campo}>\n";
            }
        }

        $xml .= "{$ind}  <{$tag}>\n";
        foreach ($bloco['campos'] as $campo => $valor) {
            $xml .= "{$ind}    <{$campo}>{$valor}</{$campo}>\n";
        }
        $xml .= "{$ind}  </{$tag}>\n";
        $xml .= "{$ind}</{$grupoXml}>\n";

        return $xml;
    }

    private function renderBlocoIBSCBS(ItemPedido $item): string
    {
        $xml = "        <IBSCBS>\n"
            . "          <CST>" . $item->getCstIBSCBS() . "</CST>\n";

        if ($item->getClasseTributariaIBSCBS() !== '')
            $xml .= "          <cClassTrib>" . $item->getClasseTributariaIBSCBS() . "</cClassTrib>\n";

        $xml .= "        </IBSCBS>\n";

        return $xml;
    }

    private function renderTotal(NotaFiscalDeProduto $nota): string
    {
        return "    <total>\n"
            . "      <ICMSTot>\n"
            . "        <vBC>0.00</vBC>\n"
            . "        <vICMS>" . number_format($nota->getICMS(), 2, '.', '') . "</vICMS>\n"
            . "        <vICMSDeson>0.00</vICMSDeson>\n"
            . "        <vFCPUFDest>0.00</vFCPUFDest>\n"
            . "        <vICMSUFDest>0.00</vICMSUFDest>\n"
            . "        <vICMSUFRemet>0.00</vICMSUFRemet>\n"
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
            . "        <vTotTrib>0.00</vTotTrib>\n"
            . "      </ICMSTot>\n"
            . "      <IBSCBSTot>\n"
            . "        <vBCIBSCBS>0.00</vBCIBSCBS>\n"
            . "        <gIBS>\n"
            . "          <gIBSUF>\n"
            . "            <vDif>0.00</vDif>\n"
            . "            <vDevTrib>0.00</vDevTrib>\n"
            . "            <vIBSUF>0.00</vIBSUF>\n"
            . "          </gIBSUF>\n"
            . "          <gIBSMun>\n"
            . "            <vDif>0.00</vDif>\n"
            . "            <vDevTrib>0.00</vDevTrib>\n"
            . "            <vIBSMun>0.00</vIBSMun>\n"
            . "          </gIBSMun>\n"
            . "          <vIBS>0.00</vIBS>\n"
            . "          <vCredPres>0.00</vCredPres>\n"
            . "          <vCredPresCondSus>0.00</vCredPresCondSus>\n"
            . "        </gIBS>\n"
            . "        <gCBS>\n"
            . "          <vDif>0.00</vDif>\n"
            . "          <vDevTrib>0.00</vDevTrib>\n"
            . "          <vCBS>0.00</vCBS>\n"
            . "          <vCredPres>0.00</vCredPres>\n"
            . "          <vCredPresCondSus>0.00</vCredPresCondSus>\n"
            . "        </gCBS>\n"
            . "      </IBSCBSTot>\n"
            . "    </total>\n";
    }

    private function renderTransp(array $config = []): string
    {
        $modFrete = $config['modFrete'] ?? 9;

        $xml = "    <transp>\n"
            . "      <modFrete>{$modFrete}</modFrete>\n";

        if (isset($config['transporta'])) {
            $t = $config['transporta'];
            $xml .= "      <transporta>\n";
            if (!empty($t['cnpj']))   $xml .= "        <CNPJ>" . $t['cnpj'] . "</CNPJ>\n";
            if (!empty($t['cpf']))    $xml .= "        <CPF>" . $t['cpf'] . "</CPF>\n";
            if (!empty($t['xNome']))  $xml .= "        <xNome>" . $this->esc($t['xNome']) . "</xNome>\n";
            if (!empty($t['ie']))     $xml .= "        <IE>" . $t['ie'] . "</IE>\n";
            if (!empty($t['xEnder'])) $xml .= "        <xEnder>" . $this->esc($t['xEnder']) . "</xEnder>\n";
            if (!empty($t['xMun']))   $xml .= "        <xMun>" . $this->esc($t['xMun']) . "</xMun>\n";
            if (!empty($t['uf']))     $xml .= "        <UF>" . $t['uf'] . "</UF>\n";
            $xml .= "      </transporta>\n";
        }

        if (isset($config['vol'])) {
            $v = $config['vol'];
            $xml .= "      <vol>\n";
            if (isset($v['qVol']))  $xml .= "        <qVol>" . $v['qVol'] . "</qVol>\n";
            if (isset($v['nVol']))  $xml .= "        <nVol>" . $v['nVol'] . "</nVol>\n";
            if (isset($v['pesoL'])) $xml .= "        <pesoL>" . number_format((float) $v['pesoL'], 3, '.', '') . "</pesoL>\n";
            if (isset($v['pesoB'])) $xml .= "        <pesoB>" . number_format((float) $v['pesoB'], 3, '.', '') . "</pesoB>\n";
            $xml .= "      </vol>\n";
        }

        $xml .= "    </transp>\n";

        return $xml;
    }

    private function renderPag(float $valorTotal, array $config = []): string
    {
        $indPag = $config['indPag'] ?? 0;
        $tPag   = $config['tPag']   ?? '01';
        $vPag   = $config['vPag']   ?? $valorTotal;
        $vTroco = $config['vTroco'] ?? 0.0;

        return "    <pag>\n"
            . "      <detPag>\n"
            . "        <indPag>{$indPag}</indPag>\n"
            . "        <tPag>{$tPag}</tPag>\n"
            . "        <vPag>" . number_format((float) $vPag, 2, '.', '') . "</vPag>\n"
            . "      </detPag>\n"
            . "      <vTroco>" . number_format((float) $vTroco, 2, '.', '') . "</vTroco>\n"
            . "    </pag>\n";
    }

    private function renderInfoIntermed(array $intermed): string
    {
        return "    <infIntermed>\n"
            . "      <CNPJ>" . $intermed['cnpj'] . "</CNPJ>\n"
            . "      <idCadIntTran>" . $this->esc($intermed['idCadIntTran']) . "</idCadIntTran>\n"
            . "    </infIntermed>\n";
    }

    private function renderInfoAdic(string $infCpl): string
    {
        return "    <infAdic>\n"
            . "      <infCpl>" . $this->esc($infCpl) . "</infCpl>\n"
            . "    </infAdic>\n";
    }

    private function renderInfoRespTec(array $resp): string
    {
        return "    <infRespTec>\n"
            . "      <CNPJ>" . $resp['cnpj'] . "</CNPJ>\n"
            . "      <xContato>" . $this->esc($resp['xContato']) . "</xContato>\n"
            . "      <email>" . $this->esc($resp['email']) . "</email>\n"
            . "      <fone>" . $resp['fone'] . "</fone>\n"
            . "    </infRespTec>\n";
    }

    private function buildChave43(Emitente $emitente, int $serie, int $numero, int $tpEmis, string $cNF): string
    {
        $cuf      = str_pad((string) $emitente->getEndereco()->getEstado()->getCodigoIBGE(), 2, '0', STR_PAD_LEFT);
        $aamm     = (new \DateTimeImmutable())->format('ym');
        $cnpj     = $emitente->getCNPJ();
        $serieStr = str_pad((string) $serie, 3, '0', STR_PAD_LEFT);
        $nNF      = str_pad((string) $numero, 9, '0', STR_PAD_LEFT);

        return $cuf . $aamm . $cnpj . '55' . $serieStr . $nNF . $tpEmis . $cNF;
    }

    private function calcularCDV(string $chave43): int
    {
        $peso = 2;
        $soma = 0;
        for ($i = strlen($chave43) - 1; $i >= 0; $i--) {
            $soma += (int) $chave43[$i] * $peso;
            $peso  = $peso === 9 ? 2 : $peso + 1;
        }
        $resto = $soma % 11;
        return $resto < 2 ? 0 : 11 - $resto;
    }

    private function esc(string $valor): string
    {
        return htmlspecialchars($valor, ENT_XML1, 'UTF-8');
    }
}
