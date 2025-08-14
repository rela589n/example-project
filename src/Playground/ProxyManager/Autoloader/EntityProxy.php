<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Autoloader;

class_alias(EntityProxy::class, AnEntity::class);

if (false) {
    class_alias(AnEntity::class, AnEntityOriginal::class);
}

class EntityProxy extends AnEntityOriginal
{
    public string $foo {
        set => $value.'_bar';
    }
}

