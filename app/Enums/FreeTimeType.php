<?php

namespace App\Enums;

enum FreeTimeType: string
{
    case Online = 'online';
    case FaceToFace = 'face-to-face';
    case All = 'all';
}
