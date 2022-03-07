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

use Closure;

/**
 * TaskBuilder
 *
 * @author Sergey Chernecov <sergey.chernecov@ecentria.com>
 */
final class TaskBuilder
{
    /**
     * Get cached copy update callback
     *
     * @return Closure
     */
    public static function buildCachedCopyUpdateCallback(): Closure
    {
        return static function () {
            $exclusion   = '';
            $cache       = Application::KEY_SOURCE_CACHE_PATH;
            $releasePath = Application::KEY_RELEASE_PATH;

            foreach (get(Application::KEY_WRITABLE_DIRS) as $value) {
                $exclusion .= " --exclude={$value}";
            }
            run(
                "rsync -a --omit-dir-times --cvs-exclude --no-l {$exclusion} --delete {{{$releasePath}}}/ {{{$cache}}}/"
            );
        };
    }

    /**
     * Get clear op cache callback
     *
     * @return Closure
     */
    public static function buildClearOpCacheCallback(): Closure
    {
        return static function () {
            // clear APC and Zend OpCache, when apc.stat or opcache.revalidate_timestamps is 0
            run('curl http://localhost/clear_apc_cache.php || exit 0');
        };
    }

    /**
     * Get database migration callback
     *
     * @return Closure
     */
    public static function buildDatabaseMigrationCallback(): Closure
    {
        return static function () {
            if (!(has(Application::KEY_EXECUTE_MIGRATIONS) && get(Application::KEY_EXECUTE_MIGRATIONS))) {
                return;
            }

            run(
                '{{bin/console}} doctrine:migrations:migrate --no-debug --allow-no-migration',
                ['timeout' => 3600]
            );
        };
    }

    /**
     * Get php fpm restart callback
     *
     * @return Closure
     */
    public static function buildPhpFpmRestartCallback(): Closure
    {
        return static function () {
            run('sudo /sbin/service php-fpm reload || exit 0');
        };
    }

    /**
     * Get setup callback
     *
     * @return Closure
     */
    public static function buildSetupCallback(): Closure
    {
        return static function () {
            /**
             * Load variables and other required things
             * that are accessible only when context exists
             */
            Application::loadVariables();
        };
    }

    /**
     * Build shared callback
     *
     * @return Closure
     */
    public static function buildSharedCallback(): Closure
    {
        return static function () {
            $file       = '.env.local';
            $sharedPath = "{{deploy_path}}/shared";
            if (test("[ -f $sharedPath/$file ]") && !test("[ -f {{release_path}}/$file ]")) {
                run("{{bin/symlink}} $sharedPath/$file {{release_path}}/$file");
            }
        };
    }

    /**
     * Get update code callback
     *
     * @return Closure
     */
    public static function buildUpdateCodeCallback(): Closure
    {
        return static function () {

            $cachePath   = Application::KEY_SOURCE_CACHE_PATH;
            $repository  = Application::KEY_REPOSITORY;
            $branch      = Application::KEY_BRANCH;
            $releasePath = Application::KEY_RELEASE_PATH;

            $command =
                "if [ -d {{{$cachePath}}} ]; then
                    cd {{{$cachePath}}} &&
                    git fetch origin &&
                    git reset --hard {{{$branch}}};
                else
                    mkdir -p {{{$cachePath}}} && 
                    git clone -q {{{$repository}}} {{{$cachePath}}} && 
                    cd {{{$cachePath}}} && 
                    git fetch origin &&
                    git reset --hard {{{$branch}}};
                fi";

            $command = self::clearMultiLineCommand($command);

            run($command);

            run("rsync -a --delete {{{$cachePath}}}/ {{{$releasePath}}}");
        };
    }

    /**
     * Clear multi line command
     *
     * @param string $command
     *
     * @return string
     */
    private static function clearMultiLineCommand(string $command): string
    {
        return trim(
            preg_replace(
                '/(\s+|\R+)/',
                ' ',
                $command
            )
        );
    }
}
