<?php

namespace App\Enum\Meeting;

enum Status: string
{
    case OPEN_TO_REGISTRATION = 'open to registration';
    case FULL = 'full';
    case IN_SESSION = 'in session';
    case DONE = 'done';
}
