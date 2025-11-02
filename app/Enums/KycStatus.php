<?php
namespace App\Enums;

enum KycStatus:string {
    case UNVERIFIED='UNVERIFIED';
    case PENDING='PENDING';
    case VERIFIED='VERIFIED';
    case REJECTED='REJECTED';
}
