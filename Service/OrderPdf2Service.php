<?php
/*
 * This file is part of the Order Pdf plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\OrderPdf2\Service;

use Eccube\Application;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Order;
use Eccube\Repository\OrderRepository;

/**
 * Class OrderPdfService.
 * Do export pdf function.
 */
class OrderPdf2Service extends AbstractFPDIService
{
    /** @var Application|object */
    private $app;

    /** @var BaseInfo */
    private $BaseInfo;

    /** @var string */
    private $fileName = 'test.pdf';

    const FONT_SJIS = 'kozminproregular';

    /** @var \TCPDF */
    private $pdf;

    /**
     * OrderPdf2Service constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->BaseInfo = $app['eccube.repository.base_info']->get();
        $this->pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf->SetFont(self::FONT_SJIS);

        $this->pdf->setFontSubsetting(true);
        // PDFの余白(上左右)を設定
        $this->pdf->SetMargins(15, 20);

        // ヘッダーの出力を無効化
        $this->pdf->setPrintHeader(false);

        // フッターの出力を無効化
        $this->pdf->setPrintFooter(true);
        $this->pdf->setFooterMargin();
        $this->pdf->setFooterFont(array(self::FONT_SJIS, '', 8));
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function makePdf(array $ids)
    {
        /** @var OrderRepository $orderRepo */
        $orderRepo = $this->app['eccube.repository.order'];

        foreach ($ids as $id) {
            $Order = $orderRepo->find($id);
            if ($Order) {
                $template = $this->getTemplate($Order);

                $this->pdf->AddPage();
                $this->pdf->writeHTML($template, true, false, true, false, '');
                $this->pdf->lastPage();
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function outputPdf()
    {
        return $this->pdf->Output($this->fileName, 'S');
    }

    protected function getTemplate(Order $Order)
    {
        $template = $this->app->renderView('OrderPdf2/Resource/template/admin/template.twig',
            array(
                'Order'=> $Order,
                'BaseInfo' => $this->BaseInfo,
            )
        );
        return $template;
    }
}
