<?php

namespace Imposto\Fiscal\Helpers;

class Moeda
{
	public static function formatar(float $valor): string
	{
		return "R$ " . number_format($valor, 2, ',', '.');
	}

	public static function formatFromCents(int $valor): string
	{
		$valorFloat = $valor / 100.0;
		return self::formatar($valorFloat);
	}
}
