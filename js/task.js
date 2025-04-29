document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("addTaskModal").style.display = "none";
    document.getElementById("editModal").style.display = "none";
});

// Load and display tasks
function loadTasks() {
    fetch("../api/task_api.php", { method: "GET" })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                displayTasks(data.task);
            } else {
                console.error("Error loading tasks:", data.message);
                alert("Failed to load tasks: " + data.message);
            }
        })
}

// Add Task
function addTask() {
    const taskName = document.getElementById("task_name").value.trim();
    const startDate = document.getElementById("start_date").value.trim();
    const endDate = document.getElementById("end_date").value.trim();
    const status = document.getElementById("status").value.trim();
    const description = document.getElementById("description").value.trim();

    if (!taskName || !startDate || !endDate || !status || !description) {
        alert("All fields are required!");
        return;
    }

    if (new Date(endDate) < new Date(startDate)) {
        alert("End date cannot be before start date!");
        return;
    }

    fetch("../api/task_api.php", {
        method: "POST",
        body: JSON.stringify({
            task_name: taskName,
            start_date: startDate,
            end_date: endDate,
            status: status,
            description: description
        }),
        headers: { "Content-Type": "application/json" }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                closeAddTaskModal();
                location.reload(); 
            }
        })
        .catch(error => {
            console.error("Error adding task:", error);
            alert("An error occurred while adding the task: " + error.message);
        });
}

// Update Task
function updateTask() {
    const id = document.getElementById("editId").value;
    const taskName = document.getElementById("edit_task_name").value.trim();
    const startDate = document.getElementById("edit_start_date").value.trim();
    const endDate = document.getElementById("edit_end_date").value.trim();
    const status = document.getElementById("edit_task_status").value.trim();
    const description = document.getElementById("edit_description").value.trim();

    if (!id || !taskName || !startDate || !endDate || !status || !description) {
        alert("All fields are required!");
        return;
    }

    if (new Date(endDate) < new Date(startDate)) {
        alert("End date cannot be before start date!");
        return;
    }

    fetch("../api/task_api.php", {
        method: "PUT",
        body: JSON.stringify({
            id: id,
            task_name: taskName,
            start_date: startDate,
            end_date: endDate,
            status: status,
            description: description
        }),
        headers: { "Content-Type": "application/json" }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                closeEditModal();
                location.reload(); 
            }
        })
        .catch(error => {
            console.error("Error updating task:", error);
            alert("An error occurred while updating the task: " + error.message);
        });
}

// Delete Task
function deleteTask(id) {
    if (confirm("Are you sure you want to delete this task?")) {
        fetch(`../api/task_api.php?id=${id}`, {
            method: "DELETE"
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    location.reload();
                }
            })
            .catch(error => {
                console.error("Error deleting task:", error);
                alert("An error occurred while deleting the task: " + error.message);
            });
    }
}

// Open Add Modal
function openAddTaskModal() {
    document.getElementById("task_name").value = "";
    document.getElementById("start_date").value = "";
    document.getElementById("end_date").value = "";
    document.getElementById("status").value = "pending";
    document.getElementById("description").value = "";
    document.getElementById("addTaskModal").style.display = "flex";
}

function closeAddTaskModal() {
    document.getElementById("addTaskModal").style.display = "none";
}

// Open Edit Modal
function openEditModal(task) {
    document.getElementById("editId").value = task.id;
    document.getElementById("edit_task_name").value = task.task_name;
    document.getElementById("edit_start_date").value = task.start_date;
    document.getElementById("edit_end_date").value = task.end_date;
    document.getElementById("edit_task_status").value = task.status.toLowerCase();
    document.getElementById("edit_description").value = task.description;
    document.getElementById("editModal").style.display = "flex";
}

function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}

// Mark task as done
function markAsDone(id) {
    if (confirm("Mark this task as completed?")) {
        fetch("../api/task_api.php", {
            method: "PUT",
            body: JSON.stringify({
                id: id,
                status: "completed"
            }),
            headers: { "Content-Type": "application/json" }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    loadTasks();
                }
            })
            .catch(error => {
                console.error("Error marking task as done:", error);
                alert("An error occurred while marking the task as done: " + error.message);
            });
    }
}