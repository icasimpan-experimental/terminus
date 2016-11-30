<?php

namespace Pantheon\Terminus\Commands\Backup\Schedule;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class CancelCommand
 * @package Pantheon\Terminus\Commands\Backup\Schedule
 */
class CancelCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Cancel a regular backup schedule
     *
     * @authorize
     *
     * @command backup:schedule:cancel
     *
     * @param string $site_env Site & environment to cancel the schedule of, in the format `site-name.env`.
     *
     * @usage terminus backup:schedule:cancel <site>.<env>
     *     Cancels this environment's regular backup schedule for the <env> environment of <site>
     */
    public function cancelSchedule($site_env)
    {
        list(, $env) = $this->getSiteEnv($site_env);
        $env->getBackups()->cancelBackupSchedule();
        $this->log()->notice('Backup schedule successfully canceled.');
    }
}