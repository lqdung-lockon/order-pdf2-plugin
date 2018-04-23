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

$includePath = get_include_path().';'.__DIR__.'/../vendor/tcpdf';
set_include_path($includePath);

require_once __DIR__.'/../vendor/tcpdf/tcpdf.php';
/**
 * FPDIのラッパークラス.
 */
abstract class AbstractFPDIService
{
}
