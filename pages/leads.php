<?php

declare(strict_types=1);

use Ksfraser\FrontAccounting\Lead\Service\LeadService;

$path_to_root = '../../..';

include_once($path_to_root . '/includes/session.inc');
add_access_extensions();

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    display_error(_('Lead module dependencies are not installed'));
    exit;
}
require_once $autoload;

$service = new LeadService();

$action = isset($_GET['action']) ? (string) $_GET['action'] : 'list';

if ($action === 'list') {
    page(_('Leads'));

    $leads = $service->listAll();

    echo '<p>' . _('Leads') . '</p>';
    echo '<table class="selection">';
    echo '<thead><tr>'
        . '<th>' . _('Source') . '</th>'
        . '<th>' . _('Status') . '</th>'
        . '<th>' . _('Rating') . '</th>'
        . '<th>' . _('Industry') . '</th>'
        . '<th>' . _('Assigned To') . '</th>'
        . '<th>' . _('Actions') . '</th>'
        . '</tr></thead><tbody>';

    foreach ($leads as $lead) {
        echo '<tr>'
            . '<td>' . htmlspecialchars($lead['fields']['leadSource'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($lead['fields']['leadStatus'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($lead['fields']['rating'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($lead['fields']['company'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($lead['fields']['contact'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td><a href="' . htmlspecialchars($lead['url'], ENT_QUOTES, 'UTF-8') . '" target="_blank">'
            . _('Edit') . '</a></td>'
            . '</tr>';
    }

    echo '</tbody></table>';
    end_page();
    exit;
}

display_error(_('Unknown action'));
exit;
