<?php

namespace Imposto\Domain\Endereco;

use Imposto\Catalogo\UFs\UF;

class Endereco
{
    public function __construct(
        private string $logradouro,
        private string $numero,
        private string $bairro,
        private string $municipioCodigo,
        private string $municipioNome,
        private UF $estado,
        private string $cep,
        private string $complemento = '',
        private string $telefone = '',
        private int $paisCodigo = 1058,
        private string $paisNome = 'Brasil',
    ) {}

    public function getLogradouro(): string { return $this->logradouro; }
    public function getNumero(): string { return $this->numero; }
    public function getBairro(): string { return $this->bairro; }
    public function getMunicipioCodigo(): string { return $this->municipioCodigo; }
    public function getMunicipioNome(): string { return $this->municipioNome; }
    public function getEstado(): UF { return $this->estado; }
    public function getCEP(): string { return preg_replace('/\D/', '', $this->cep); }
    public function getComplemento(): string { return $this->complemento; }
    public function getTelefone(): string { return preg_replace('/\D/', '', $this->telefone); }
    public function getPaisCodigo(): int { return $this->paisCodigo; }
    public function getPaisNome(): string { return $this->paisNome; }
}
