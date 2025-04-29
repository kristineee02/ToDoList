<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include 'database.php';
include '../class/Task.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$task = new Task($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $taskData = $task->getTaskById($_GET['id']);
            if ($taskData) {
                echo json_encode(["status" => "success", "task" => $taskData]);
            } else {
                echo json_encode(["status" => "error", "message" => "Task not found"]);
            }
        } else {
            $tasks = $task->getAllTask();
            echo json_encode(["status" => "success", "task" => $tasks]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
            exit;
        }

        if (isset($data['task_name'], $data['start_date'], $data['end_date'], $data['status'], $data['description'])) {
            $result = $task->addTask(
                $data['task_name'],
                $data['start_date'],
                $data['end_date'],
                $data['status'],
                $data['description']
            );

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Task added successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to add task"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid input - missing fields"]);
        }
        break;

        case 'PUT':
            $data = json_decode(file_get_contents("php://input"), true);
        
            if (!$data) {
                echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
                exit;
            }
        
            if (isset($data['id'])) {
                $result = $task->updateTask(
                    $data['id'],
                    $data['task_name'] ?? null,
                    $data['start_date'] ?? null,
                    $data['end_date'] ?? null,
                    $data['status'] ?? null,
                    $data['description'] ?? null
                );
        
                if (is_array($result)) {
                    echo json_encode(["status" => $result['success'] ? "success" : "error", "message" => $result['message']]);
                } else {
                    echo json_encode([
                        "status" => $result ? "success" : "error",
                        "message" => $result ? "Task updated successfully" : "Failed to update task: You can only update pending tasks!"
                    ]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid input - missing ID"]);
            }
            break;


    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $task->deleteTask($id);
            if ($result) {
                echo json_encode(["status" => "success", "message" => "Task deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "You can only delete pending tasks!"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid ID"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
        break;
}
?>