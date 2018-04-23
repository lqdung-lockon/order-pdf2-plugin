<?php
/*
 * This file is part of the Order Pdf plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\OrderPdf2\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Plugin\OrderPdf2\Service\OrderPdf2Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class OrderPdfController.
 */
class OrderPdf2Controller extends AbstractController
{
    /**
     * 作成ボタンクリック時の処理
     * 帳票のPDFをダウンロードする.
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return Response
     *
     * @throws BadRequestHttpException
     */
    public function download(Application $app, Request $request)
    {

        $ids = $this->getIds($request);

        if (count($ids) == 0) {
            $app->addError('admin.plugin.order_pdf.parameter.notfound', 'admin');
            log_info('The Order cannot found!');

            return $app->redirect($app->url('admin_order'));
        }

        // サービスの取得
        /* @var OrderPdf2Service $service */
        $service = $app['orderpdf.service.order_pdf2'];

        // 購入情報からPDFを作成する
        $status = $service->makePdf($ids);

        // 異常終了した場合の処理
        if (!$status) {
            $app->addError('admin.plugin.order_pdf.download.failure', 'admin');
            log_info('Unable to create pdf files! Process have problems!');

            return $app->redirect($app->url('admin_order', array('resume' => 1)));
        }

        // ダウンロードする
        $response = new Response(
            $service->outputPdf(),
            200,
            array('content-type' => 'application/pdf')
        );

        // レスポンスヘッダーにContent-Dispositionをセットし、ファイル名をreceipt.pdfに指定
        $response->headers->set('Content-Disposition', 'attachment; filename="test.pdf"');

        return $response;
    }

    /**
     * requestから注文番号のID一覧を取得する.
     *
     * @param Request $request
     *
     * @return array $isList
     */
    protected function getIds(Request $request)
    {
        $isList = array();

        // その他メニューのバージョン
        $queryString = $request->getQueryString();

        if (empty($queryString)) {
            return $isList;
        }

        // クエリーをparseする
        // idsX以外はない想定
        parse_str($queryString, $ary);

        foreach ($ary as $key => $val) {
            // キーが一致
            if (preg_match('/^ids\d+$/', $key)) {
                if (!empty($val) && $val == 'on') {
                    $isList[] = intval(str_replace('ids', '', $key));
                }
            }
        }

        // id順にソートする
        sort($isList);

        return $isList;
    }
}
