<?php

namespace Imposto\Fiscal\GNRE;

class GNRE
{
	public function __construct(
		private string $ufDestino,
		private float $valorPrincipal,
		private \DateTime $dataVencimento,
		private string $codigoReceita,
		private string $cpfCnpjContribuinte,
		private string $inscricaoEstadual,
		private string $numeroDocumentoFiscal,
		private string $descricaoTributo,
		private ?string $numeroGNRE = null,
		private ?string $informacoesAdicionais = null,
	) {}

	public function validar(): bool
	{
		if (empty($this->ufDestino) || $this->valorPrincipal <= 0)
			return false;

		return true;
	}

	public function getGuiaXML(): string
	{
		return "<GNRE></GNRE>"; // Implementar geração XML conforme especificação GNRE
	}

	public function getResumo(): string
	{
		return "GNRE para {$this->ufDestino}, valor R$ {$this->valorPrincipal}, vencimento em {$this->dataVencimento->format('d/m/Y')}";
	}
}
