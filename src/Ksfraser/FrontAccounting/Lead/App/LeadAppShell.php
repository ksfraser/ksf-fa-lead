<?php

declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Lead\App;

use ksfraser\FrontAccounting\Common\App\AbstractAppShell;
use ksfraser\FrontAccounting\Common\Menu\FAModuleMenu;
use ksfraser\FrontAccounting\Lead\Controller\LeadTabController;

class LeadAppShell extends AbstractAppShell
{
    public function __construct()
    {
        parent::__construct('lead', 'index.php', 'view', 'leads');

        $this->add_module(_("Lead"));

        $menu = new FAModuleMenu(
            'modules/ksf_FA_Lead/index.php',
            'view',
            ''
        );

        $menu->addItem('leads', _("Leads"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_LEAD_VIEW');

        $this->add_extensions();
    }
}
