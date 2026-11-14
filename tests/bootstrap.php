<?php
declare(strict_types=1);

/**
 * Unit test bootstrap for ksfraser/ksf-fa-lead.
 *
 * Loads Composer's autoloader. No FA bootstrap, no database — pure unit tests
 * against DTOs and repository (fake db_* functions).
 */

require_once __DIR__ . '/../vendor/autoload.php';
