<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-04
 * Time: 11:46
 */


/**
 * Orders repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;


/**
 * Class OrderRepository.
 */
class OrderRepository
{
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * OrderRepository constructor.
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
        $queryBuilder->select('order_id', 'screening_id_screening', /*'id_user',*/ 'seats_amount')
            ->from('order');

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Find one record.
     *
     * @param string $id Element id
     *
     * @return array|mixed Result
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('order_id', 'screening_id_screening', /*'id_user',*/ 'seats_amount')
            ->from('order')
            ->where('order_id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return !$result ? [] : $result;
    }

    /**
     * @param $order
     * @return int
     */
    public function save($order, $userId)
    {
        if (isset($order['id']) && ctype_digit((string) $order['id'])) {

            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->insert('ticket')
                ->values(
                    array(
                        'screening_screening_id' => ':screen_id',
                        'seats_amount' => ':seats',
                        'user_user_id' => ':user_id'
                    )
                )
                ->setParameter(':screen_id', $order['id'], \PDO::PARAM_INT)
                ->setParameter(':seats',  $order['seats_amount'], \PDO::PARAM_INT)
                ->setParameter(':user_id',  $userId, \PDO::PARAM_INT);
            $result = $queryBuilder->execute();

            return $result;
        } else {
            return 0;
        }
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function CountSeats($id)
    {
        $booked = $this->FindBookedSeats($id);
        $all = $this->FindAvailableSeats($id);
        $freeSeats = $all - $booked;

        return $freeSeats;
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function FindBookedSeats($id)
  {
      $queryBuilder = $this->db->createQueryBuilder();
      $queryBuilder->select('sum(ticket.seats_amount)')
          -> from ('ticket')
          -> where ('screening_screening_id = :id')
          ->setParameter(':id', $id, \PDO::PARAM_INT);
      $result = $queryBuilder->execute()->fetch();

      return $result['sum(ticket.seats_amount)'];
  }
    /**
     * @param $id
     * @return mixed
     */
    public function FindAvailableSeats ($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
//        $screening = $this->FindScreening($id);
//        dump($this->FindScreening($id));
//        dump($screening);
        $queryBuilder->select('a.seats_no')
            ->from('auditorium', 'a')
            ->innerJoin('a', 'screening', 's', 's.auditorium_id_auditorium = a.auditorium_id')
            -> where ('s.screening_id = :screening')
            ->setParameter(':screening', $id,\PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return $result['seats_no'];
    }

    /**
     * @param $id
     * @return mixed
     */
//    public function FindScreening ($id)
//        {
//            $queryBuilder = $this->db->createQueryBuilder();
////            dump($id);
//            $queryBuilder->select ('s.screening_id')
//            -> from ('screening', 's')
//            -> innerJoin ('s', 'ticket', 't', 't.screening_screening_id = s.screening_id')
//            -> where ('t.ticket_id= :id')
//                ->setParameter(':id', $id, \PDO::PARAM_INT);
//            $result = $queryBuilder->execute()->fetch();
//            dump($result);
//            return $result['screening_id'];
//
//        }


    /**
     * Find for appropriate.
     *
     * @param string          $name Element name
     * @param int|string|null $id   Element id
     *
     * @return array Result
     */
    public function findForAppropriate($name, $id = null)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('a.auditorium_id = :id')
            ->setParameter(':name', $name, \PDO::PARAM_STR);
        if ($id) {
            $queryBuilder->andWhere('t.id <> :id')
                ->setParameter(':id', $id, \PDO::PARAM_INT);
        }

        return $queryBuilder->execute()->fetchAll();
    }


}