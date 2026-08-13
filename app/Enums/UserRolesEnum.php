<?php

namespace App\Enums;

enum UserRolesEnum : string
{
    case CUSTOMER = 'customer';
    case ADMIN = 'admin';
    case SALESMAN = 'salesman';
}