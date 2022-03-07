<?php

/*
 * This file is part of the Ecentria group, inc. software.
 *
 * (c) 2021, Ecentria group, inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Version20200101000000
 *
 * @author Sergey Chernecov <sergey.chernecov@ecentria.com>
 */
final class Version20200101000000 extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !==
            'mysql', 'Migration can only be executed safely on \'mysql\'.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !==
            'mysql', 'Migration can only be executed safely on \'mysql\'.'
        );
    }
}
