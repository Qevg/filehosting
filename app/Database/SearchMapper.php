<?php

namespace Filehosting\Database;

/**
 * Class SearchMapper
 * @package Filehosting\Database
 */
class SearchMapper
{
    /**
     * @var \PDO $db
     */
    private $db;

    /**
     * SearchMapper constructor.
     *
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Search query
     *
     * @param string $query
     *
     * @return array
     */
    public function searchQuery(string $query): array
    {
        $sql = "SELECT * FROM index_files, rt_files WHERE MATCH(:query_bind)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':query_bind', $query, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, 'Filehosting\Entity\File');
    }

    /**
     * Gets latest files
     *
     * @param int $number
     *
     * @return array
     */
    public function getLatestFiles(int $number): array
    {
        $sql = "SELECT * FROM index_files, rt_files ORDER BY id DESC LIMIT :number_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':number_bind', $number, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, 'Filehosting\Entity\File');
    }


    /**
     * Adds real time index
     *
     * @param int $id
     * @param string $originalName
     */
    public function addIndex(int $id, string $originalName): void
    {
        $sql = "INSERT INTO rt_files (id, original_name) VALUES (:id_bind, :original_name_bind)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_bind', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':original_name_bind', $originalName, \PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Deletes real time index
     *
     * @param int $id
     */
    public function deleteIndex(int $id): void
    {
        $sql = "DELETE FROM rt_files WHERE id=:id_bind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_bind', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }
}
