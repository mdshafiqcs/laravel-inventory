<?php 

namespace App\Enum;

enum Status : string {
    case SUCCESS = 'success';
    case ERROR = 'error';
    case FAILED = 'failed';
}

