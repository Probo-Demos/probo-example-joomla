<?php

/**
 * @package     Probo.Examples
 * @subpackage  com_proboenvironment
 *
 * @copyright   (C) 2026 Probo CI
 * @license     GNU General Public License version 2 or later
 */

namespace Probo\Component\Proboenvironment\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Default controller for the Probo Environment demo component.
 */
class DisplayController extends BaseController
{
    /**
     * The default view to display.
     *
     * @var string
     */
    protected $default_view = 'environment';
}
