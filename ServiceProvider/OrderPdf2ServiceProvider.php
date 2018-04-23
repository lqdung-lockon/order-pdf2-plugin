<?php
/*
 * This file is part of the Order Pdf plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\OrderPdf2\ServiceProvider;

use Eccube\Common\Constant;
use Plugin\OrderPdf2\Service\OrderPdf2Service;
use Plugin\OrderPdf2\Util\Version;
use Plugin\OrderPdf2\Event\OrderPdf2;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

// include log functions (for 3.0.0 - 3.0.11)
require_once __DIR__.'/../log.php';

/**
 * Class OrderPdfServiceProvider.
 */
class OrderPdf2ServiceProvider implements ServiceProviderInterface
{
    /**
     * Register service function.
     *
     * @param BaseApplication $app
     */
    public function register(BaseApplication $app)
    {
        // Order pdf event
        $app['orderpdf.event.order_pdf2'] = $app->share(function () use ($app) {
            return new OrderPdf2($app);
        });

        // ============================================================
        // コントローラの登録
        // ============================================================
        // 管理画面定義
        $admin = $app['controllers_factory'];
        // 強制SSL
        if ($app['config']['force_ssl'] == Constant::ENABLED) {
            $admin->requireHttps();
        }

        // PDFファイルダウンロード
        $admin->get('/plugin/order-pdf2/download', '\\Plugin\\OrderPdf2\\Controller\\OrderPdf2Controller::download')
            ->bind('plugin_admin_order_pdf2_download');

        $app->mount('/'.trim($app['config']['admin_route'], '/').'/', $admin);


        // -----------------------------
        // サービスの登録
        // -----------------------------
        // 帳票作成
        $app['orderpdf.service.order_pdf2'] = $app->share(function () use ($app) {
            return new OrderPdf2Service($app);
        });

        // ============================================================
        // メッセージ登録
        // ============================================================
        $file = __DIR__.'/../Resource/locale/message.'.$app['locale'].'.yml';
        if (file_exists($file)) {
            $app['translator']->addResource('yaml', $file, $app['locale']);
        }

        // initialize logger (for 3.0.0 - 3.0.8)
        if (!Version::isSupportMethod()) {
            eccube_log_init($app);
        }
    }

    /**
     * Boot function.
     *
     * @param BaseApplication $app
     */
    public function boot(BaseApplication $app)
    {
    }
}
