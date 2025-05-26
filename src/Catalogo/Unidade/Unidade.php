<?php

namespace Imposto\Catalogo\Unidade;

enum Unidade: string
{
	case GRAMA = 'G';
	case HORA_H = 'H';
	case LITRO = 'L';
	case MEGAWATT_H = 'MWH';
	case METRO_CUBICO = 'M3';
	case METRO_LINEAR = 'M';
	case METRO_QUADRADO = 'M2';
	case MIL_LITROS = 'MILL';
	case MIL_METROS = 'MILM';
	case MIL_METROS_CUBICOS = 'M3MIL';
	case MILHAO = 'MILH';
	case MILHEIRO = 'MIL';
	case PAR = 'PAR';
	case QUILATE = 'QT';
	case QUILOGRAMA = 'KG';
	case TONELADA = 'T';
	case UNIDADE = 'UN';

	public function getDescricao(): string
	{
		return match ($this) {
			self::GRAMA => 'grama',
			self::HORA_H => 'homem / hora',
			self::LITRO => 'litro',
			self::MEGAWATT_H => 'megawatt-hora',
			self::METRO_CUBICO => 'metro cúbico',
			self::METRO_LINEAR => 'metro linear',
			self::METRO_QUADRADO => 'metro quadrado',
			self::MIL_LITROS => 'mil litros',
			self::MIL_METROS => 'mil metros',
			self::MIL_METROS_CUBICOS => 'mil metros cúbicos',
			self::MILHAO => 'milhão',
			self::MILHEIRO => 'milheiro',
			self::PAR => 'par',
			self::QUILATE => 'quilate',
			self::QUILOGRAMA => 'quilograma',
			self::TONELADA => 'tonelada',
			self::UNIDADE => 'unidade',
		};
	}
}
