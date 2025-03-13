<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Jwt\Refresh;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;

/** @api */
#[ORM\Entity]
#[ORM\Table(name: 'auth_jwt_refresh_tokens')]
class JwtRefreshToken extends RefreshToken
{
}
