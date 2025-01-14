<?php

namespace App\Enums;

enum RolesEnum: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case SECRETARY = 'secretary';
    case EXTERNAL = 'external';
}
