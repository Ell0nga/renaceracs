<?php

namespace App;

enum FinanzaCategoriaTipo: string
{
    case INGRESO = 'ingreso';
    case EGRESO = 'egreso';
    case INVERSION = 'inversion';
    case AHORRO = 'ahorro';
    case DEUDA = 'deuda';
}