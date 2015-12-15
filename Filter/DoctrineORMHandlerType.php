<?php

/*
 * This file is part of the Da2e FiltrationDoctrineORMBundle package.
 *
 * (c) Dmitry Abrosimov <abrosimovs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da2e\FiltrationDoctrineORMBundle\Filter;

/**
 * Simple class with constants for Doctrine ORM handler type.
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class DoctrineORMHandlerType
{
    const TYPE = 'doctrine_orm';
    const CLASS_NAME = '\Doctrine\ORM\QueryBuilder';
}
