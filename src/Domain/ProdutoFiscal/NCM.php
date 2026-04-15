<?php

namespace Imposto\Domain\ProdutoFiscal;

class NCM
{
    private string $codigo;

    public function __construct(string $codigo)
    {
        $normalizado = str_replace('.', '', $codigo);

        if (!preg_match('/^\d{8}$/', $normalizado))
            throw new \InvalidArgumentException("NCM [{$codigo}] inválido: deve conter 8 dígitos numéricos (ex: 49019900 ou 4901.99.00).");

        $this->codigo = $normalizado;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getCodigoFormatado(): string
    {
        return substr($this->codigo, 0, 4) . '.' . substr($this->codigo, 4, 2) . '.' . substr($this->codigo, 6, 2);
    }
}
