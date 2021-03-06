<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Tests\DependencyInjection\Compiler;

use Contao\CoreBundle\DependencyInjection\Compiler\AddImagineClassPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AddImagineClassPassTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $pass = new AddImagineClassPass();

        $this->assertInstanceOf('Contao\CoreBundle\DependencyInjection\Compiler\AddImagineClassPass', $pass);
    }

    public function testAddsTheImagineClass(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('contao.image.imagine', new Definition());

        $pass = new AddImagineClassPass();
        $pass->process($container);

        $this->assertContains(
            $container->getDefinition('contao.image.imagine')->getClass(),
            [
                'Imagine\Gd\Imagine',
                'Imagine\Gmagick\Imagine',
                'Imagine\Imagick\Imagine',
            ]
        );
    }
}
