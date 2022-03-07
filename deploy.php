<?php

/*
 * This file is part of the ecentria software.
 *
 * (c) 2020, ecentria, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

require 'recipe/symfony4.php';

require __DIR__ . '/ci/deployer/Application.php';
require __DIR__ . '/ci/deployer/TaskBuilder.php';

Application::build();
