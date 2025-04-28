<?php

namespace App\Domain\Enums;

enum HttpStatusCodeEnum: int
{
    case NOT_FOUND = 404;
    case INTERNAL_SERVER_ERROR = 500;
    case CREATED = 201;
    case FORBIDDEN = 403;
    case CUSTOM_ERROR = 1213;
    case UNPROCESSABLE_ENTITY = 422;
    case UNAUTHORIZED = 401;
}
