<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-02
 * Time: 10:57
 */

/**
 * Screening repository.
 */
namespace Repository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

/**
 * Class ScreeningRepository.
 */

class ScreeningRepository
{
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * ScreeningRepository constructor.
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
        $queryBuilder->select('screening_id', 'screening_date', 'screening_time', 'auditorium_id_auditorium', 'movie_duration_min', 'movie_movie_id', 'm.movie_title')
            ->from('screening', 's')
            ->innerJoin('s', 'movie', 'm', 's.movie_movie_id= m.movie_id');

        return $queryBuilder->execute()->fetchAll();
    }
    /**
     * Fetch one by its id records.
     *
     * @return array Result
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('s.screening_id', 's.screening_date', 's.screening_time', 'a.name',
            's.movie_movie_id', 'm.movie_id', 'm.movie_title')
            ->from('screening', 's')
            ->innerJoin('s', 'movie', 'm', 's.movie_movie_id= m.movie_id')
            ->innerJoin('s', 'auditorium', 'a', 's.auditorium_id_auditorium= a.auditorium_id')
            ->where('m.movie_id=:id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();

        return $result;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOneScreeningById($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('s.screening_id', 's.screening_date', 's.screening_time', 's.auditorium_id_auditorium',  's.movie_movie_id')
            ->from('screening', 's')
//            ->innerJoin('s', 'movie', 'm', 's.movie_movie_id= m.movie_id')
//            ->innerJoin('s', 'auditorium', 'a', 's.auditorium_id_auditorium= a.auditorium_id')
            ->where('s.screening_id=:id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return $result;
    }



    /**
     * Save all records.
     *
     */
    public function save($screening)
    {
        if (isset($screening['screening_id']) && ctype_digit((string) $screening['screening_id'])) {
            // update record

            $id = $screening['screening_id'];
            unset($screening['screening_id']);
            unset($screening['auditorium_id']);

            return $this->db->update('screening', $screening, ['screening_id' => $id]);


        } else {
            // add new record
            return $this->db->insert('screening', $screening);
        }
    }

    public function delete($screening)
    {
        return $this->db->delete('screening', ['screening_id' => $screening['screening_id']]);
    }

   /* public function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select('s.screening_id', 's.screening_date', 's.screening_time', 's.movie_movie_id', 'm.movie_title', 's.auditorium_id_auditorium', 'a.name')
            ->from('screening', 's')
            ->innerJoin('s', 'movie', 'm', 's.movie_movie_id= m.movie_id')
            ->innerJoin('s', 'auditorium', 'a', 's.auditorium_id_auditorium= a.auditorium_id')
            ->where('m.movie_id=:id');
    }*/

    public function findAllScreenings()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('s.screening_id', 's.screening_date', 's.screening_time', 's.movie_movie_id', 'a.auditorium_id', 'm.movie_title', 's.auditorium_id_auditorium', 'a.name')
            ->from('screening', 's')
            ->innerJoin('s', 'movie', 'm', 's.movie_movie_id= m.movie_id')
            ->innerJoin('s', 'auditorium', 'a', 's.auditorium_id_auditorium= a.auditorium_id')
            -> orderBy('s.screening_date');

        return $queryBuilder->execute()->fetchAll();
    }

}







