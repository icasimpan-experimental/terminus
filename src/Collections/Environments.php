<?php

namespace Pantheon\Terminus\Collections;

use Pantheon\Terminus\Models\Environment;

/**
 * Class Environments
 * @package Pantheon\Terminus\Collections
 */
class Environments extends SiteOwnedCollection
{
    public static $pretty_name = 'environments';
    /**
     * @var string
     */
    protected $collected_class = Environment::class;
    /**
     * @var string
     */
    protected $url = 'sites/{site_id}/environments';

    /**
     * Creates a multidev environment
     *
     * @param string $to_env_id Name of new the environment
     * @param Environment $from_env Environment to clone from
     * @return Workflow
     */
    public function create($to_env_id, Environment $from_env)
    {
        $workflow = $this->getWorkflows()->create(
            'create_cloud_development_environment',
            [
                'params' => [
                    'environment_id' => $to_env_id,
                    'deploy' => [
                        'clone_database' => ['from_environment' => $from_env->id,],
                        'clone_files' => ['from_environment' => $from_env->id,],
                        'annotation' => sprintf(
                            'Create the "%s" environment.',
                            $to_env_id
                        ),
                    ],
                ],
            ]
        );
        return $workflow;
    }

    /**
     * Filters the environment list to only include development environments
     *
     * @return Environment $this
     */
    public function filterForDevelopment()
    {
        return $this->filter(function ($env) {
            return $env->isDevelopment();
        });
    }

    /**
     * Filters the environment list to only include multidev environments
     *
     * @return Environment $this
     */
    public function filterForMultidev()
    {
        return $this->filter(function ($env) {
            return $env->isMultidev();
        });
    }

    /**
     * List Environment IDs, with Dev/Test/Live first
     *
     * @return string[] $ids
     */
    public function ids()
    {
        $ids = array_keys($this->getMembers());

        //Reorder environments to put dev/test/live first
        $default_ids = ['dev', 'test', 'live'];
        $multidev_ids = array_diff($ids, $default_ids);
        $ids = array_merge($default_ids, $multidev_ids);

        return $ids;
    }

    /**
     * Returns a list of all multidev environments on the collection-owning Site
     *
     * @return Environment[]
     */
    public function multidev()
    {
        return $this->filterForMultidev()->all();
    }

    /**
     * Retrieves all models serialized into arrays. If the site is frozen, it skips test and live.
     *
     * @return array
     */
    public function serialize()
    {
        $site_is_frozen = !is_null($this->getSite()->get('frozen'));
        $models = [];
        foreach ($this->getMembers() as $id => $model) {
            if (!$site_is_frozen || !in_array($id, ['test', 'live',])) {
                $models[$id] = $model->serialize();
            }
        }
        return $models;
    }
}
