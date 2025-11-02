<?php
namespace App\Enums;

enum LotStatus:string {
    case SCHEDULED='SCHEDULED';
    case ACTIVE='ACTIVE';
    case ENDED='ENDED';
    case AWARDED='AWARDED';
    case UNSOLD='UNSOLD';
    case CANCELLED='CANCELLED';
}
