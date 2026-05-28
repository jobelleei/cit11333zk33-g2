<?php
// ============================================================
//  classes/BaseModel.php
//
//  Every model (User, Subject, Grade) extends this class.
//  It provides shared getAll(), find(), create(), update(),
//  and delete() so subclasses only need to define their own
//  specific queries.
// ============================================================

class BaseModel {
    protected $conn;    // raw PDO connection
    protected $db;      // QueryBuilder instance
    protected $table;   // set by each subclass

    public function __construct($conn) {
        $this->conn = $conn;
        $this->db   = new QueryBuilder($conn);
    }

    // Return every row in the table
    public function getAll() {
        return $this->db->table($this->table)->select()->get();
    }

    // Return every row that belongs to one user
    public function getAllByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY id ASC");
        $stmt->execute([
            ':user_id' => $user_id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find a single row by ID
    public function find($id) {
        return $this->db
                    ->table($this->table)
                    ->select()
                    ->where('id', $id)
                    ->first();
    }

    // Find a single row by ID and user ID
    public function findByUser($id, $user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id AND user_id = :user_id LIMIT 1");
        $stmt->execute([
            ':id' => $id,
            ':user_id' => $user_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a new row
    public function create($data) {
        $columns = array_keys($data);
        $placeholders = array_map(function($column) {
            return ':' . $column;
        }, $columns);

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ")
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $column => $value) {
            $stmt->bindValue(':' . $column, $value);
        }

        return $stmt->execute();
    }

    // Update a row by ID
    public function update($id, $data) {
        $setParts = [];

        foreach ($data as $column => $value) {
            $setParts[] = "$column = :$column";
        }

        $sql = "UPDATE {$this->table}
                SET " . implode(', ', $setParts) . "
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $column => $value) {
            $stmt->bindValue(':' . $column, $value);
        }

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Update a row by ID and user ID
    public function updateByUser($id, $user_id, $data) {
        $setParts = [];

        foreach ($data as $column => $value) {
            $setParts[] = "$column = :$column";
        }

        $sql = "UPDATE {$this->table}
                SET " . implode(', ', $setParts) . "
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $column => $value) {
            $stmt->bindValue(':' . $column, $value);
        }

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Delete a row by its primary key
    public function delete($id) {
        return $this->db->table($this->table)->delete($id);
    }

    // Delete a row by ID and user ID
    public function deleteByUser($id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $user_id
        ]);
    }
}