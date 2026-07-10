<?php

/**
 * @package     Probo.Examples
 * @subpackage  com_proboenvironment
 *
 * @copyright   (C) 2026 Probo CI
 * @license     GNU General Public License version 2 or later
 */

namespace Probo\Component\Proboenvironment\Site\View\Environment;

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Version;

/**
 * Renders the Probo build-environment demo page.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * TRUE when PROBO_ENVIRONMENT === 'TRUE'.
     *
     * @var boolean
     */
    protected $inProbo = false;

    /**
     * The running Joomla core version string.
     *
     * @var string
     */
    protected $joomlaVersion = '';

    /**
     * The running PHP version string.
     *
     * @var string
     */
    protected $phpVersion = '';

    /**
     * Documented Probo build variables (name, value, present, description).
     *
     * @var array
     */
    protected $buildVars = [];

    /**
     * Dynamically discovered secrets/custom variables (name, value).
     *
     * @var array
     */
    protected $otherVars = [];

    /**
     * Prepares and displays the demo page.
     *
     * @param   string  $tpl  The name of the template file to parse.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $this->buildVars     = $this->get('BuildVars');
        $this->otherVars     = $this->get('OtherVars');
        $this->inProbo       = getenv('PROBO_ENVIRONMENT') === 'TRUE';
        $this->joomlaVersion = (new Version())->getShortVersion();
        $this->phpVersion    = PHP_VERSION;

        $this->getDocument()->setTitle('Probo Environment — Demo');

        HTMLHelper::_('stylesheet', 'com_proboenvironment/probo-environment.css', ['version' => 'auto', 'relative' => true]);

        parent::display($tpl);
    }
}
