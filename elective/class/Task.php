<?php
    class Task {
        private $conn;
        private $table = "tasks";

        public function __construct($db) {
            $this->conn = $db;
        }

        public function getAllTask() {
            $query = "SELECT id, task_name, start_date, end_date, status, description  FROM ". $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getTaskById($id) {
            $query = "SELECT id, task_name, start_date, end_date, status, description  FROM ". $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        }

        public function addTask($task_name, $start_date, $end_date, $status, $description) {
            $query = "INSERT INTO " . $this->table . " (task_name, start_date, end_date, status, description) 
            VALUES (:task_name, :start_date, :end_date, :status, :description)";
        
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':task_name', $task_name);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':description', $description);
        
            return $stmt->execute();
        }

        public function updateTask($id, $task_name, $start_date, $end_date, $status, $description) {
            $query = "SELECT status FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($task && $task['status'] !== 'Pending') {
                return false; // Only pending tasks can be updated
            }
        
            $query = "UPDATE " . $this->table . " SET task_name = ?, start_date = ?, end_date = ?, status = ?, description = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$task_name, $start_date, $end_date, $status, $description, $id]);
        }
        
        public function deleteTask($id) {
            $query = "SELECT status FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($task && $task['status'] !== 'Pending') {
                return false; // Only pending tasks can be deleted
            }
        
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        }
        
    }
?>