<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Lead\App;

use ksfraser\FrontAccounting\Common\App\AbstractFrontController;

class LeadFrontController extends AbstractFrontController
{
    public function __construct(
        LeadAppShell $appShell,
        string $fallbackSecurityArea,
        string $path_to_root
    ) {
        parent::__construct($appShell, $fallbackSecurityArea, $path_to_root);
    }
}
