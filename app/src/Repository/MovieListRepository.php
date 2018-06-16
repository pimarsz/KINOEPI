<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-05-31
 * Time: 12:26
 */

/**
 * Movies repository.
 */
namespace Repository;
use Doctrine\DBAL\Connection;
use Form\ScreeningType;

/**
 * Class MovieListRepository.
 */
class MovieListRepository
{
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * MovieListRepository constructor.
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
        $queryBuilder->select('movie_id', 'movie_title', 'movie_description', 'movie_duration_min')
            ->from('movie');

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
        $queryBuilder->select('movie_id', 'movie_title', 'movie_description', 'movie_duration_min')
            ->from('movie')
            ->where('movie_id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return !$result ? [] : $result;
    }

    /**
     * @param $movie
     * @return int
     */
    public function save($movie)
    {
        if (isset($movie['movie_id']) && ctype_digit((string) $movie['movie_id'])) {
            // update record
            $id = $movie['movie_id'];
            unset($movie['movie_id']);

            return $this->db->update('movie', $movie, ['movie_id' => $id]);
        } else {
            // add new record
            return $this->db->insert('movie', $movie);
        }
    }

    /**
     * Query all records.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select('m.movie_id', 'm.title')
            ->from('movie', 'm');
    }

    public function delete($movie)
    {
        return $this->db->delete('movie', ['movie_id' => $movie['movie_id']]);
    }
}