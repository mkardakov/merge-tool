# Ecentria API Skeleton

Overview
---

This is the skeleton project for an API.
It uses:
- [API Platform]
- [Doctrine]
- [Logs Aggregation] (Ecentria Logging)
- [PHPUnit] for testing
- [Deployer] for deploying
- [Psalm] for static analysis
- [Platform JSON API Support]

It has pre-configured:
- Kubernetes testing environment
- Deployer phar configurations
- Bamboo build plan tasks and stages

How to convert this skeleton to an application
---

### Clone & Configure

```
git clone ssh://git@bitbucket.ecentria.tools/svc/ecentria-api-skeleton.git ./<name>
rm -rf ./.git
```

1. **./composer.json** <br>
Change composer.json properties that are unique to the project being created.
The name, description, homepage, author, etc.
2. **./config/packages/ecentria.yaml** <br>
Set _application\_name_, it will define an index name for your logs in Kibana.
3. **./ci/deployer/Inventory/hosts.yml** <br>
When the production server is ready, **hosts.yml** should be configured
to point to it. \<hostname>, \<user>, \<repository> should be replaced with real values.
4. **./ci/settings.sh** <br>
Configure the environment variables.
_PROJECT\_NAMESPACE_ is used to differentiate between projects in Kubernetes cluster
where the tests will be run in. In order to create the project kubernetes namespace
visit https://rancher.ecentria.tools/. Contact **Chuck Norris** team for any questions
related to Kubernetes.
    
#### Setup bamboo plan and specs
For bamboo specs to work you first need to have an empty build plan in bamboo and then update bamboo specs file accordingly. These are the steps:
1. Create a new build plan in bamboo
2. Navigate to the newly created bamboo build and note the project and build keys from the url, these will be needed for bamboo specs file, for example, for api skeleton project itself build plan can be found here https://bamboo.ecentria.tools/browse/SER-EAS, in this case, `SER` is the project key (this build belongs to 'Services' project) and `EAS` is the build key
3. Update **./bamboo-specs/bamboo.yml** with keys noted in step 2.
    -  _project-key_  is the project key
    - _key_ is the build key
    - _name_ is the build name, make sure this matches the build name in bamboo
4. Update **./ci/settings.sh** with keys noted in step 2.
    - _PLAN_BUILD_KEY_ is the build key
5. After you have pushed the updated bamboo specs to the repository you need to enable bamboo specs, see https://confluence.atlassian.com/bamboo/enabling-repository-stored-bamboo-specs-938641941.html for more information on how to do that


More details
---

### How to install development dependencies
This project uses [composer-bin-plugin] that allows installing dev tool dependencies in separate folder under vendor-bin. Each folder is assigned a namespace:
- ci-tools: tools written in PHP used in the project build
- test-tools: tools written in PHP used to test or check PHP code

If a new development dependency is required and is referenced from within the project code, it needs to be installed in the original require-dev section of composer.json. Otherwise, this dependency can be installed with composer bin plugin.

### Environment variables

Environment variables on production servers are managed by puppet.
Deployer.phar is used to store and use .env.local file on production servers.
In order to have environment files in production, puppet should install .env.local file into:
```bash
/home/<application>/shared/.env.local
```
Deployer will guarantee that this file will be shared across all releases.

- use `.env.local` for generic overrides on machine (local, remote)
- use `.env.<environment>` (commit to repo) for specific environment vars 
- use `.env.<environment>.local` for specific environment overrides

### Testing

To run psalm checks use the following command:
`composer psalm`
If you are getting an error `Container xml file(s) not found` while running the command, make sure the symfony cache exists and it is up-to-date.

To run phpunit tests use the following command:
`composer phpunit`

### How to setup local Docker environment
If you would like to use Docker for development, this project has already prepared a docker-compose file. For setting up a project use dev/setup-dev-env.sh script. After executing this script you will have all required contains for development.

#### How to configure and setup Xdebug 3 in Docker
Xdebug 3 is already installed and configured in the PHP container. So to start debugging you need:
- setup local Docker environment, see the previous section
- open PHPStorm and verify that your PHPStorm listen 9003 xdebug port - Settings -> PHP -> Debug
- in PHPStorm open Settings -> PHP -> Servers and add a new server with the following configuration:
    - name: api_skeleton
    - host: localhost
    - port: 8080
    - click on the "Use path mappings" checkbox and define mapping to the `/var/www/app/project` server path

### Default Bamboo build plan ###
By default bamboo plan consists of 2 stages:
- _Build_ stage, this stage consists of single task that builds `./ci/docker/image/php-fpm/Dockerfile` image and pushes it to image registry. This image contains source code and all the dependencies needed
- _Run tests_, this stage consists of 2 tasks:
    - _Psalm_, pulls image built during the first stage and runs _composer psalm_
    - _PHPUnit_, pulls image built during the first stage and runs _composer phpunit_
    
Links
---
For more details see: 
- https://symfony.com/doc/current/configuration.html#configuring-environment-variables-in-env-files
- https://symfony.com/doc/current/configuration/dot-env-changes.html
  

[Psalm]: https://psalm.dev
[ADR for Static Analysis]: https://confluence.ecentria.tools/x/cSt6Bg
[API Platform]: https://api-platform.com/
[Doctrine]: https://www.doctrine-project.org/
[Logs Aggregation]: https://confluence.ecentria.tools/display/ARCH/Logs+Aggregation
[PHPUnit]: https://phpunit.de/
[composer-bin-plugin]: https://github.com/bamarni/composer-bin-plugin
[Deployer]: https://deployer.org/
[Platform JSON API Support]: https://bitbucket.ecentria.tools/projects/LIB/repos/api-platform-json-api-support/browse