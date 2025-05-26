<?php

namespace Imposto\Fiscal\Repositorio;

use Imposto\Catalogo\UFs\UF;

class AliquotaInternaICMSRepository
{
	private array $aliquotas = [
		UF::AC => 0.19,  // Acre
		UF::AL => 0.20,  // Alagoas (19% + 1% FECOEP)
		UF::AP => 0.18,  // Amapá
		UF::AM => 0.20,  // Amazonas
		UF::BA => 0.205, // Bahia
		UF::CE => 0.20,  // Ceará
		UF::DF => 0.20,  // Distrito Federal
		UF::ES => 0.17,  // Espírito Santo
		UF::GO => 0.19,  // Goiás
		UF::MA => 0.22,  // Maranhão
		UF::MT => 0.17,  // Mato Grosso
		UF::MS => 0.17,  // Mato Grosso do Sul
		UF::MG => 0.18,  // Minas Gerais
		UF::PA => 0.19,  // Pará
		UF::PB => 0.20,  // Paraíba
		UF::PR => 0.195, // Paraná
		UF::PE => 0.205, // Pernambuco
		UF::PI => 0.21,  // Piauí
		UF::RJ => 0.22,  // Rio de Janeiro (20% + 2% FECP)
		UF::RN => 0.18,  // Rio Grande do Norte
		UF::RS => 0.17,  // Rio Grande do Sul
		UF::RO => 0.195, // Rondônia
		UF::RR => 0.20,  // Roraima
		UF::SC => 0.17,  // Santa Catarina
		UF::SP => 0.18,  // São Paulo
		UF::SE => 0.20,  // Sergipe (19% + 1% FECOEP)
		UF::TO => 0.20,  // Tocantins
	];

	public function getAliquota(UF $uf): float
	{
		if (!array_key_exists($uf->value, $this->aliquotas))
			throw new \InvalidArgumentException("Aliquota não encontrada para a UF: " . $uf->value);

		return $this->aliquotas[$uf->value];
	}

	public function getTodas(): array
	{
		return $this->aliquotas;
	}
}
