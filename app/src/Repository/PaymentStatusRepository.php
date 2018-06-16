<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-15
 * Time: 18:58
 */
/**
 * Payment Method repository.
 */
namespace Repository;
use Doctrine\DBAL\Connection;
use Form\TransactionType;

/**
 * Class PaymentStatusRepository.
 */

class PaymentStatusRepository
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
        $queryBuilder->select('payment_status_id', 'status_name')
            ->from('payment_status');


        return $queryBuilder->execute()->fetchAll();
    }
}