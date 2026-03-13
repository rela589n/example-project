<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Search;

enum SearchType: string
{
    case TEXT = 'text';
    case VECTOR = 'vector';
}
