<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-16
 * Time: 14:18
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
/**
 * Class RoleRepository.
 */

class RoleRepository
{

    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * AuditoriumRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Fetch all records.
     *
     * @return array Result
     */
    public function findAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('id', 'name')
            ->from('si_roles');


        return $queryBuilder->execute()->fetchAll();
    }
}