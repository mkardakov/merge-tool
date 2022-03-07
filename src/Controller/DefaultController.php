<?php
/*
 * This file is part of the Ecentria group, inc. software.
 *
 * (c) 2021, Ecentria group, inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller
 *
 * @author Sergey Chernecov <sergey.chernecov@ecentria.com>
 */
class DefaultController extends AbstractController
{
    /**
     * Index action
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('base.html.twig');
    }
}
