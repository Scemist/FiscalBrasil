<?php

namespace Imposto\Catalogo\IndicadorIE;

enum IndicadorIE: int
{
    case ContribuinteICMS  = 1;
    case ContribuinteIsento = 2;
    case NaoContribuinte    = 9;
}
