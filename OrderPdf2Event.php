<?php
/*
 * This file is part of the Order Pdf plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\OrderPdf2;

use Eccube\Application;
use Eccube\Event\TemplateEvent;
use Plugin\OrderPdf\Event\OrderPdf;
use Plugin\OrderPdf\Event\OrderPdfLegacy;
use Plugin\OrderPdf\Util\Version;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class OrderPdf Event.
 */
class OrderPdf2Event
{
    /**
     * @var Application
     */
    private $app;

    /**
     * OrderPdfEvent constructor.
     *
     * @param \Silex\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Event for new hook point.
     *
     * @param TemplateEvent $event
     */
    public function onAdminOrderIndexRender(TemplateEvent $event)
    {
        /* @var OrderPdf $orderPdfEvent */
        $orderPdfEvent = $this->app['orderpdf.event.order_pdf2'];
        $orderPdfEvent->onAdminOrderIndexRender($event);
    }
}
