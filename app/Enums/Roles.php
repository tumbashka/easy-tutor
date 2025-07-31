<?php

namespace App\Enums;

enum Roles: string
{
    case Admin = 'admin';
    case Teacher = 'teacher';
    case Student = 'student';
}
