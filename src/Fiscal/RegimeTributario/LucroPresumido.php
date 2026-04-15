<?php

namespace Imposto\Fiscal\RegimeTributario;

use Imposto\Catalogo\UFs\UF;

class LucroPresumido
{
    public function calcularDIFAL(UF $ufOrigem, UF $ufDestino, float $baseCalculo): float
    {
        if ($ufOrigem === $ufDestino)
            return 0.0;

        $aliquotaInternaDestino  = 0.18;
        $aliquotaInterestadual   = 0.12;

        return max(0.0, ($aliquotaInternaDestino - $aliquotaInterestadual) * $baseCalculo);
    }
}
