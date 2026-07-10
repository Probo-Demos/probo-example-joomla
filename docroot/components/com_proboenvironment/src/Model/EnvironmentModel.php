<?php

/**
 * @package     Probo.Examples
 * @subpackage  com_proboenvironment
 *
 * @copyright   (C) 2026 Probo CI
 * @license     GNU General Public License version 2 or later
 */

namespace Probo\Component\Proboenvironment\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseModel;

/**
 * Reads the container's process environment for the Probo demo page.
 *
 * This is the Joomla port of the single-file PHP demo (probo-example-php): it
 * reads the environment, filters it the same way Probo's
 * probo-ubuntu/<image>/files/envvars-swap.sh does at container start, and
 * partitions the result into two sets — documented build variables and
 * everything else Probo injected (organization/project secrets and custom
 * variables).
 */
class EnvironmentModel extends BaseModel
{
    /**
     * Documented Probo build variables and their descriptions.
     *
     * Any of these present in the visible environment are shown (with live
     * values) in the first table; the rest are still listed as "not set" so
     * the page documents the full catalog. Description strings contain
     * trusted, static markup and are rendered unescaped in the template.
     *
     * @var array
     */
    protected const KNOWN_DESCRIPTIONS = [
        'PROBO_ENVIRONMENT' => 'Set to <code>TRUE</code> when running inside a Probo build container.',
        'BUILD_ID'          => 'Unique identifier for the current build.',
        'BUILD_DOMAIN'      => "Domain where this build's site is accessible.",
        'BRANCH_NAME'       => 'Name of the git branch being built.',
        'BRANCH_LINK'       => 'URL to the branch on the VCS provider (GitHub, Bitbucket, etc.).',
        'COMMIT_REF'        => 'Full commit hash/reference being built.',
        'COMMIT_LINK'       => 'URL to the commit on the VCS provider.',
        'PULL_REQUEST_NAME' => 'Title of the pull request that triggered this build.',
        'PULL_REQUEST_LINK' => 'URL to the pull request on the VCS provider.',
        'SRC_DIR'           => 'Path to the source code directory inside the container.',
        'ASSET_DIR'         => 'Path to the assets directory inside the container.',
    ];

    /**
     * The filtered environment, less the documented build variables.
     *
     * @var array|null
     */
    private $remainingEnv;

    /**
     * Returns the documented build variables, in catalog order.
     *
     * @return array  Rows of name, value, present, description.
     */
    public function getBuildVars(): array
    {
        $visible = $this->visibleEnvironment();

        $buildVars = [];

        foreach (self::KNOWN_DESCRIPTIONS as $name => $description) {
            $present = \array_key_exists($name, $visible);

            $buildVars[] = [
                'name'        => $name,
                'value'       => $present ? $visible[$name] : '',
                'present'     => $present,
                'description' => $description,
            ];

            unset($visible[$name]);
        }

        $this->remainingEnv = $visible;

        return $buildVars;
    }

    /**
     * Returns everything else Probo injected — secrets and custom variables.
     *
     * Discovered dynamically; there is no fixed list.
     *
     * @return array  Rows of name, value.
     */
    public function getOtherVars(): array
    {
        if ($this->remainingEnv === null) {
            $this->getBuildVars();
        }

        $otherVars = [];

        foreach ($this->remainingEnv as $name => $value) {
            $otherVars[] = [
                'name'  => $name,
                'value' => $value,
            ];
        }

        return $otherVars;
    }

    /**
     * Returns the environment variables Probo exposes, filtered and sorted.
     *
     * Mirrors the filter applied by envvars-swap.sh at container start:
     *
     *   compgen -e | grep -Ev '^APACHE_|^(HOME|LANG|PWD|PATH|OLDPWD|SHLVL|_)$'
     *
     * @return array  Variable name => value, sorted by name.
     */
    protected function visibleEnvironment(): array
    {
        $env = getenv();

        if (!\is_array($env)) {
            $env = $_ENV ?: [];
        }

        $exactExclude = ['HOME', 'LANG', 'PWD', 'PATH', 'OLDPWD', 'SHLVL', '_'];

        $visible = [];

        foreach ($env as $name => $value) {
            if (str_starts_with($name, 'APACHE_')) {
                continue;
            }

            if (\in_array($name, $exactExclude, true)) {
                continue;
            }

            $visible[$name] = $value;
        }

        ksort($visible);

        return $visible;
    }
}
