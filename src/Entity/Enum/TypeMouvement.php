<?php

namespace App\Entity\Enum;

enum TypeMouvement: string
{
    case ENTREE = 'entree';
    case SORTIE = 'sortie';
    case AJUSTEMENT = 'ajustement';
}
