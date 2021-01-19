<?php

namespace SkyRaptor\Chatter\Tests\Models;

use Illuminate\Foundation\Auth\User as AuthUser;
use SkyRaptor\Chatter\Interfaces\ChatterUser;
use SkyRaptor\Chatter\Traits\ChatterUserTrait;

class User extends AuthUser implements ChatterUser {
    use ChatterUserTrait;
}
