<?php

namespace Imposto\Domain\Emitente;

use Imposto\Domain\Endereco\Endereco;

class Emitente
{
    public function __construct(
        private string $cnpj,
        private string $razaoSocial,
        private string $nomeFantasia,
        private Endereco $endereco,
        private string $inscricaoEstadual,
        private int $codigoRegimeTributario,
        private string $inscricaoMunicipal = '',
    ) {
        $this->cnpj = preg_replace('/\D/', '', $cnpj);
    }

    public function getCNPJ(): string { return $this->cnpj; }
    public function getRazaoSocial(): string { return $this->razaoSocial; }
    public function getNomeFantasia(): string { return $this->nomeFantasia; }
    public function getEndereco(): Endereco { return $this->endereco; }
    public function getInscricaoEstadual(): string { return $this->inscricaoEstadual; }
    public function getInscricaoMunicipal(): string { return $this->inscricaoMunicipal; }
    public function getCodigoRegimeTributario(): int { return $this->codigoRegimeTributario; }
}
