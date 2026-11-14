<?php

declare(strict_types=1);

/**
 * KSF FrontAccounting Module Hooks — ksf_FA_Lead
 *
 * Lead is the owning module for lead/pipeline data spun off from CRM Leads tab.
 * It answers RELATED_TO_FIELDS for the 'lead' type and provides LeadResolvedDto
 * instances built from 0_ksf_crm_leads data.
 *
 * @package KsfFA_ksf_FA_Lead
 * @version 1.0.0
 */

require_once __DIR__ . '/ComposerDependencies.php';

\ksfraser\FrontAccounting\Lead\Utils\ComposerDependencies::ensure(__DIR__);

$autoload_path = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($autoload_path)) {
    require_once $autoload_path;
}

require_once __DIR__ . '/includes/common.inc';
require_once __DIR__ . '/includes/events.inc';

use ksfraser\RelatedTo\DTO\RelatedQueryDto;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;

define('SS_ksf_FA_Lead', 154 << 8);

if (!defined('SA_LEAD_VIEW')) {
    define('SA_LEAD_VIEW', SS_ksf_FA_Lead | 1);
    define('SA_LEAD_MANAGE', SS_ksf_FA_Lead | 2);
}

class hooks_ksf_FA_Lead extends hooks {
    const RESPONDERS = array('RELATED_TO_FIELDS', 'LEAD_RESOLVE', 'LEAD_FIELDS');

    var $module_name = 'ksf_FA_Lead';
    var $version = '1.0.0';

    function install_tabs($app) {
    }

    function install_options($app) {
    }

    function install_access() {
        $security_sections[SS_ksf_FA_Lead] = _("Lead Management");
        $security_areas['SA_LEAD_VIEW'] = array(SA_LEAD_VIEW, _("View leads"));
        $security_areas['SA_LEAD_MANAGE'] = array(SA_LEAD_MANAGE, _("Manage leads"));

        return array($security_areas, $security_sections);
    }

    function activate_extension($company, $check_only = true) {
        if ($check_only) {
            return true;
        }

        \ksfraser\FrontAccounting\Lead\Utils\ComposerDependencies::ensure(__DIR__);

        $this->update_databases($company, array(
            '0_ksf_crm_leads.sql' => array('ksf_crm_leads'),
        ), false);

        return true;
    }

    private function ensure_composer_dependencies() {
        \ksfraser\FrontAccounting\Lead\Utils\ComposerDependencies::ensure(__DIR__);
    }

    public function getModuleConstants(&$data, $opts = null) {
        $data['constants'] = array(
            'SS_ksf_FA_Lead' => SS_ksf_FA_Lead,
            'SA_LEAD_VIEW' => SA_LEAD_VIEW,
            'SA_LEAD_MANAGE' => SA_LEAD_MANAGE,
        );
        return true;
    }

    public function getModuleCapabilities(&$data, $opts = null) {
        $data['capabilities'] = self::RESPONDERS;
        return true;
    }

    public function hasCapability(&$data, $opts = null) {
        $capability = is_array($opts) && isset($opts['capability']) ? $opts['capability'] : null;
        if ($capability === null && isset($data['capability'])) {
            $capability = $data['capability'];
        }
        $data['has_capability'] = in_array($capability, self::RESPONDERS, true);
        return $data['has_capability'];
    }

    public function respondToCapabilityRequest(&$data, $opts = null) {
        $request = is_array($opts) && isset($opts['request'])
            ? $opts['request']
            : (isset($data['request']) ? $data['request'] : 'capabilities');

        $data['request'] = $request;
        $data['module'] = $this->module_name;

        if (strpos($request, 'has:') === 0) {
            return $this->hasCapability($data, array('capability' => substr($request, 4)));
        }

        switch ($request) {
            case 'capabilities':
                return $this->getModuleCapabilities($data, $opts);
            case 'constants':
                return $this->getModuleConstants($data, $opts);
            default:
                $data['error'] = 'Unknown request type: ' . $request;
                return null;
        }
    }

    /**
     * RELATED_TO_FIELDS — the owning module answers with the display fields
     * it wants shown for the 'lead' type.
     *
     * @param array $data Modified by reference
     * @param array|null $opts
     * @return null
     * @since 1.0.0
     */
    public function RELATED_TO_FIELDS(&$data, $opts = null) {
        if (!isset($data['module']) || !is_string($data['module'])) {
            $data['error'] = 'RELATED_TO_FIELDS requires data[module]';
            return null;
        }
        if (!isset($data['type']) || !is_string($data['type'])) {
            $data['error'] = 'RELATED_TO_FIELDS requires data[type]';
            return null;
        }
        if ($data['type'] !== 'lead') {
            return null;
        }
        if (!isset($data['fields']) || !is_array($data['fields'])) {
            $data['fields'] = array();
        }
        $data['fields'] = array_merge($data['fields'], array(
            'leadSource' => _('Lead Source'),
            'leadStatus' => _('Lead Status'),
            'rating'     => _('Rating'),
            'company'    => _('Company'),
            'contact'    => _('Contact'),
        ));
        return null;
    }

    /**
     * LEAD_RESOLVE — resolve one lead by id, returning a LeadResolvedDto.
     *
     * @param array $data Modified by reference
     * @param array|null $opts
     * @return null
     * @since 1.0.0
     */
    public function LEAD_RESOLVE(&$data, $opts = null) {
        if (!isset($data['lead_id']) || !is_numeric($data['lead_id'])) {
            $data['error'] = 'LEAD_RESOLVE requires data[lead_id]';
            return null;
        }
        if (!isset($data['dto']) || !$data['dto'] instanceof RelatedRecordDto) {
            $data['error'] = 'LEAD_RESOLVE requires data[dto] as RelatedRecordDto';
            return null;
        }

        $leadId = (int) $data['lead_id'];
        $dto = $data['dto'];

        $service = new \Ksfraser\FrontAccounting\Lead\Service\LeadService();
        $resolved = $service->resolveById($leadId, $dto);
        $data['result'] = $resolved;

        return null;
    }

    /**
     * LEAD_FIELDS — return the field map for the lead type.
     *
     * @param array $data Modified by reference
     * @param array|null $opts
     * @return null
     * @since 1.0.0
     */
    public function LEAD_FIELDS(&$data, $opts = null) {
        $data['fields'] = array(
            'leadSource' => _('Lead Source'),
            'leadStatus' => _('Lead Status'),
            'rating'     => _('Rating'),
            'company'    => _('Company'),
            'contact'    => _('Contact'),
        );
        return null;
    }
}
