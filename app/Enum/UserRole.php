<?php

namespace App\Enum;

enum UserRole: string {
    case SUPER_ADMIN = "super-admin";
    case ADMIN = "admin";
    case USER = "user";
}