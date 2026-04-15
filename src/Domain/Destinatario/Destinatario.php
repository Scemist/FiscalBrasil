<?php

namespace Imposto\Domain\Destinatario;

use Imposto\Catalogo\TipoPessoa\TipoPessoa;
use Imposto\Domain\Endereco\Endereco;

class Destinatario
{
    private string $documento;

    public function __construct(
        private TipoPessoa $tipoPessoa,
        string $documento,
        private string $nome,
        private Endereco $endereco,
        private int $indicadorIE,
        private string $inscricaoEstadual = '',
    ) {
        $this->documento = preg_replace('/\D/', '', $documento);
    }

    public function getTipoPessoa(): TipoPessoa { return $this->tipoPessoa; }
    public function getDocumento(): string { return $this->documento; }
    public function getNome(): string { return $this->nome; }
    public function getEndereco(): Endereco { return $this->endereco; }

    /**
     * 1 = Contribuinte ICMS
     * 2 = Contribuinte isento de Inscrição
     * 9 = Não Contribuinte
     */
    public function getIndicadorIE(): int { return $this->indicadorIE; }
    public function getInscricaoEstadual(): string { return $this->inscricaoEstadual; }

    public function isContribuinteICMS(): bool { return $this->indicadorIE === 1; }
    public function isPessoaFisica(): bool { return $this->tipoPessoa === TipoPessoa::PF; }
}
