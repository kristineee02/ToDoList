<?php
include '../api/database.php';
include '../class/DbTest.php';

$database = new Database();
$conn = $database->getConnection();

$test = new DbTest($conn);
$connectionStatus = $test->checkConnection();

// Fetch tasks from the database
$query = "SELECT * FROM tasks"; 
$stmt = $conn->prepare($query);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Information System</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
        </div>
    </nav>
    
    <div class="container">
        <div class="header">
            <div class="logo">To Do List Management System</div>
            <p class="tagline">Create and manage your tasks efficiently</p>
        </div>
        
        <div class="page-header">
            <h1 class="page-title">My Tasks</h1>
            <button class="add-btn" onclick="openAddTaskModal()">
                <i class="fas fa-plus"></i> Add New Task
            </button>
        </div>
        
    <?php if (!empty($tasks)): ?>
    <?php foreach ($tasks as $task): 
        $startDate = htmlspecialchars($task['start_date']);
        $endDate = htmlspecialchars($task['end_date']);
        $statusClass = strtolower($task['status']) === 'completed' ? 'completed' : 
                       (strtolower($task['status']) === 'in-progress' ? 'in-progress' : 'pending');
        $taskData = htmlspecialchars(json_encode($task), ENT_QUOTES, 'UTF-8');
    ?>
        <div class="task-card">
            <div class="task-header">
                <div class="task-date">
                    <i class="far fa-calendar-alt"></i> <?= $startDate ?> - <?= $endDate ?>
                </div>
                <h3 class="task-title"><?= htmlspecialchars($task['task_name']) ?></h3>
                <span class="status-badge <?= $statusClass ?>">
                    <?= htmlspecialchars($task['status']) ?>
                </span>
            </div>
            <div class="task-body">
                <p class="task-description"><?= htmlspecialchars($task['description']) ?></p>
            </div>
            <div class="task-actions">
                <button class="btn btn-done" onclick="markAsDone(<?= $task['id'] ?>)">
                    <i class="fas fa-check"></i> Mark as Done
                </button>
                <button class="btn btn-edit" onclick='openEditModal(<?= $taskData ?>)'>
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-delete" onclick="deleteTask(<?= $task['id'] ?>)">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </div>
        </div>
    <?php endforeach; ?>
    <?php else: ?>
        <div class="no-tasks" style="text-align: center; margin-top: 20px;">
            <p>No tasks available. Add a new task to get started!</p>
        </div>
    <?php endif; ?>

    <!-- Add Task Modal -->
    <div id="addTaskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Create New Task</h2>
                <span class="close" onclick="closeAddTaskModal()">×</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="task_name" class="form-label">Task Title</label>
                    <input type="text" id="task_name" class="form-control" placeholder="Enter task title">
                </div>
                <div class="form-group">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" class="form-control">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="form-group">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" class="form-control">
                        <option value="pending">Pending</option>
                        <option value="in-progress">In Progress</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea id="description" class="form-control" placeholder="Enter task description"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="closeAddTaskModal()">Cancel</button>
                <button class="btn btn-save" onclick="addTask()">
                    <i class="fas fa-save"></i> Save Task
                </button>
            </div>
        </div>
    </div>
    
    <!-- Edit Task Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Task</h2>
                <span class="close" onclick="closeEditModal()">×</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" id="editId">
                    <label for="edit_task_name" class="form-label">Task Title</label>
                    <input type="text" id="edit_task_name" class="form-control" placeholder="Enter task title">
                </div>
                <div class="form-group">
                    <label for="edit_start_date" class="form-label">Start Date</label>
                    <input type="date" id="edit_start_date" class="form-control">
                    <label for="edit_end_date" class="form-label">End Date</label>
                    <input type="date" id="edit_end_date" class="form-control">
                </div>
                <div class="form-group">
                    <label for="edit_task_status" class="form-label">Status</label>
                    <select id="edit_task_status" class="form-control">
                        <option value="pending">Pending</option>
                        <option value="in-progress">In Progress</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_description" class="form-label">Description</label>
                    <textarea id="edit_description" class="form-control" placeholder="Enter task description"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button class="btn btn-save" onclick="updateTask()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
    
    <script src="../js/task.js"></script>
</body>
</html>