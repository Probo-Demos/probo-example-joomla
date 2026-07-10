<?php

/**
 * @package     Probo.Examples
 * @subpackage  com_proboenvironment
 *
 * @copyright   (C) 2026 Probo CI
 * @license     GNU General Public License version 2 or later
 */

\defined('_JEXEC') or die;

/**
 * Probo build-environment demo page.
 *
 * Available view properties:
 * - $this->inProbo        TRUE when PROBO_ENVIRONMENT === 'TRUE'.
 * - $this->joomlaVersion  The running Joomla core version string.
 * - $this->phpVersion     The running PHP version string.
 * - $this->buildVars      Documented Probo build variables (name, value, present, description).
 * - $this->otherVars      Dynamically discovered secrets/custom variables (name, value).
 */
?>
<div class="probo-env">

    <section class="probo-env__hero">
        <div class="probo-env__badge">&#x1F4E6; Probo CI &mdash; Joomla <?php echo $this->escape($this->joomlaVersion); ?></div>
        <h1 class="probo-env__title">Build <span>environment</span> variables</h1>
        <p class="probo-env__subtitle">
            Live values injected by Probo CI into this build container.
            Variables are populated when a pull request triggers a build.
        </p>
    </section>

    <div class="probo-env__status-bar">
        <div class="probo-env__status-pill <?php echo $this->inProbo ? 'is-active' : 'is-inactive'; ?>">
            <span class="probo-env__dot"></span>
            <?php echo $this->inProbo ? 'Running inside a Probo build container' : 'Not running in a Probo environment'; ?>
        </div>
    </div>

    <div class="probo-env__version-card">
        <div class="probo-env__version-inner">
            <div class="probo-env__version-logo">&#x2733;&#xFE0F;</div>
            <div class="probo-env__version-text">
                <div class="probo-env__version-label">Joomla Version</div>
                <div class="probo-env__version-number"><?php echo $this->escape($this->joomlaVersion); ?></div>
                <div class="probo-env__version-full">PHP <?php echo $this->escape($this->phpVersion); ?></div>
            </div>
        </div>
    </div>

    <section class="probo-env__section">
        <h2 class="probo-env__section-title">Build variables</h2>
        <p class="probo-env__desc" style="margin-bottom:1.25rem;">
            Documented variables Probo injects for every build. Values shown are read
            live from the environment the site can see &mdash; the same set exposed by
            the container's <code>envvars-swap.sh</code>.
        </p>
        <table class="probo-env__table">
            <thead>
                <tr>
                    <th style="width:22%">Variable</th>
                    <th style="width:28%">Value</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->buildVars as $var) : ?>
                <tr>
                    <td><span class="probo-env__var-name">$<?php echo $this->escape($var['name']); ?></span></td>
                    <td>
                        <?php if ($var['present'] && $var['value'] !== '') : ?>
                            <span class="probo-env__var-value is-set"><?php echo $this->escape($var['value']); ?></span>
                        <?php else : ?>
                            <span class="probo-env__var-value is-unset">not set</span>
                        <?php endif; ?>
                    </td>
                    <td class="probo-env__desc"><?php echo $var['description']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="probo-env__section">
        <h2 class="probo-env__section-title">Secrets &amp; other injected variables</h2>
        <p class="probo-env__desc" style="margin-bottom:1.25rem;">
            Everything else Probo made visible to the site: organization and project
            secrets plus any custom variables. Discovered dynamically from the
            container environment &mdash; there is no fixed list.
        </p>
        <?php if (empty($this->otherVars)) : ?>
            <p class="probo-env__var-value is-unset">No additional variables are exposed in this environment.</p>
        <?php else : ?>
        <table class="probo-env__table">
            <thead>
                <tr>
                    <th style="width:35%">Variable</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->otherVars as $var) : ?>
                <tr>
                    <td><span class="probo-env__var-name">$<?php echo $this->escape($var['name']); ?></span></td>
                    <td>
                        <?php if ($var['value'] !== '') : ?>
                            <span class="probo-env__var-value is-set"><?php echo $this->escape($var['value']); ?></span>
                        <?php else : ?>
                            <span class="probo-env__var-value is-unset">empty</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>

</div>
