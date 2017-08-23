<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Tests\DependencyInjection\Compiler;

use Contao\CoreBundle\DependencyInjection\Compiler\AddSessionBagsPass;
use Contao\CoreBundle\Session\Attribute\ArrayAttributeBag;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Tests the AddSessionBagsPass class.
 *
 * @author Leo Feyer <https:/github.com/leofeyer>
 */
class AddSessionBagsPassTest extends TestCase
{
    /**
     * Tests the object instantiation.
     */
    public function testCanBeInstantiated()
    {
        $pass = new AddSessionBagsPass();

        $this->assertInstanceOf('Contao\CoreBundle\DependencyInjection\Compiler\AddSessionBagsPass', $pass);
    }

    /**
     * Tests adding the session bags.
     */
    public function testAddsTheSessionBags()
    {
        $container = new ContainerBuilder();
        $container->setDefinition('session', new Definition(Session::class));

        $container->setDefinition(
            'contao.session.contao_backend',
            new Definition(ArrayAttributeBag::class)
        );

        $container->setDefinition(
            'contao.session.contao_frontend',
            new Definition(ArrayAttributeBag::class)
        );

        $pass = new AddSessionBagsPass();
        $pass->process($container);

        $methodCalls = $container->findDefinition('session')->getMethodCalls();

        $this->assertCount(2, $methodCalls);
        $this->assertSame('registerBag', $methodCalls[0][0]);
        $this->assertSame('registerBag', $methodCalls[1][0]);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $methodCalls[0][1][0]);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $methodCalls[1][1][0]);
        $this->assertSame('contao.session.contao_backend', (string) $methodCalls[0][1][0]);
        $this->assertSame('contao.session.contao_frontend', (string) $methodCalls[1][1][0]);
    }

    /**
     * Tests processing the pass without a session.
     */
    public function testDoesNotAddsTheSessionBagsIfThereIsNoSession()
    {
        $container = new ContainerBuilder();

        $pass = new AddSessionBagsPass();
        $pass->process($container);

        $this->assertFalse($container->hasDefinition('session'));
    }
}
