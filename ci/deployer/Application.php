<?php

/*
 * This file is part of the ecentria software.
 *
 * (c) 2018, ecentria, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

/**
 * Application
 *
 * @author Sergey Chernecov <sergey.chernecov@ecentria.com>
 */
final class Application
{
    public const KEY_REPOSITORY         = 'repository';
    public const KEY_SOURCE_CACHE_PATH  = 'source_cache_path';
    public const KEY_RELEASE_PATH       = 'release_path';
    public const KEY_WRITABLE_DIRS      = 'writable_dirs';
    public const KEY_DEPLOY_PATH        = 'deploy_path';
    public const KEY_EXECUTE_MIGRATIONS = 'execute_migrations';
    public const KEY_BRANCH             = 'branch';

    /**
     * Build
     *
     * @return void
     */
    public static function build(): void
    {
        self::buildInventory();
        self::registerTasks();
    }

    /**
     * Load variables
     *
     * @return void
     */
    public static function loadVariables(): void
    {
        set(self::KEY_SOURCE_CACHE_PATH, '{{' . self::KEY_DEPLOY_PATH . '}}/cached-copy');
    }

    /**
     * Build inventory
     *
     * @return void
     */
    private static function buildInventory(): void
    {
        inventory(__DIR__ . '/Inventory/hosts.yml');
    }

    /**
     * Register shared tasks
     *
     * @return void
     */
    private static function registerTasks(): void
    {
        set('shared_files', []); // We do not want obsolete env files symlinked from default symfony recipes

        /**
         * Custom made
         */
        task('custom:setup', TaskBuilder::buildSetupCallback())->setPrivate();
        task('custom:cached_copy_update', TaskBuilder::buildCachedCopyUpdateCallback())->setPrivate();
        task('custom:clear_opcache', TaskBuilder::buildClearOpCacheCallback())->setPrivate();
        task('custom:phpfpm:reload', TaskBuilder::buildPhpFpmRestartCallback())->setPrivate();
        task('custom:migrations', TaskBuilder::buildDatabaseMigrationCallback())->once()->setPrivate();
        task('custom:shared', TaskBuilder::buildSharedCallback())->setPrivate();

        /**
         * Overrides
         */
        task('deploy:update_code', TaskBuilder::buildUpdateCodeCallback())->setPrivate();

        /**
         * Temporary
         */
        after('custom:setup', 'deploy:unlock');

        /**
         * Sequence
         */
        before('deploy', 'custom:setup');

        after('deploy:shared', 'custom:shared');
        after('deploy:failed', 'deploy:unlock');
        after('deploy:vendors', 'custom:cached_copy_update');
        after('deploy:vendors', 'custom:migrations');

        before('cleanup', 'custom:clear_opcache');
        before('cleanup', 'custom:phpfpm:reload');
    }
}
