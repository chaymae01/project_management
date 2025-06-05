

document.addEventListener('DOMContentLoaded', function() {
    let draggedTask = null;
    let originalColumn = null;
    let originalPosition = null;

    function initDragEvents() {
        document.querySelectorAll('.kanban-task').forEach(task => {
            task.addEventListener('dragstart', handleDragStart);
            task.addEventListener('dragend', handleDragEnd);
        });

        document.querySelectorAll('.kanban-column').forEach(column => {
            column.addEventListener('dragover', handleDragOver);
            column.addEventListener('dragleave', handleDragLeave);
            column.addEventListener('drop', handleDrop);
        });
    }

    function handleDragStart(e) {
        draggedTask = this;
        originalColumn = this.closest('.kanban-column');
        originalPosition = {
            index: Array.from(originalColumn.querySelectorAll('.kanban-task')).indexOf(this),
            nextSibling: this.nextElementSibling
        };
        
        this.classList.add('dragging');
        e.dataTransfer.setData('text/plain', this.dataset.taskId);
        e.dataTransfer.effectAllowed = 'move';
    }

    function handleDragEnd() {
        this.classList.remove('dragging');
        document.querySelectorAll('.kanban-column').forEach(col => {
            col.classList.remove('drop-zone', 'drop-allowed', 'drop-denied');
        });
    }

    function handleDragOver(e) {
        e.preventDefault();
        if (!draggedTask) return;
        
        this.classList.add('drop-zone', 'drop-allowed');
        e.dataTransfer.dropEffect = 'move';
    }

    function handleDragLeave() {
        this.classList.remove('drop-zone', 'drop-allowed', 'drop-denied');
    }

    async function handleDrop(e) {
        e.preventDefault();
        this.classList.remove('drop-zone', 'drop-allowed', 'drop-denied');
        
        if (!draggedTask) return;
        
        const taskId = draggedTask.dataset.taskId;
        const newStatus = this.dataset.status;

        try {
            const response = await fetch(`/projet_java/app/task/move/${taskId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus })
            });

            if (!response.ok) {
                throw new Error(await response.text());
            }

            this.querySelector('.kanban-task-container').appendChild(draggedTask);
            draggedTask.dataset.status = newStatus;
            updateTaskVisualState(draggedTask, newStatus);
            updateColumnCounters();

        } catch (error) {
            console.error('Error:', error);
            revertTaskPosition();
            showAlert('danger', error.message);
        }
    }

    function revertTaskPosition() {
        if (!originalColumn || !draggedTask) return;
        
        const container = originalColumn.querySelector('.kanban-task-container');
        if (originalPosition.nextSibling) {
            container.insertBefore(draggedTask, originalPosition.nextSibling);
        } else {
            container.appendChild(draggedTask);
        }
    }

    function updateTaskVisualState(taskElement, newStatus) {
        const statusBadge = taskElement.querySelector('.task-status');
        if (statusBadge) {
            statusBadge.textContent = newStatus;
            statusBadge.className = `task-status status-${newStatus.toLowerCase().replace(' ', '-')}`;
        }
        
        taskElement.className = taskElement.className
            .replace(/status-\S+/g, '')
            .replace(/\s+/g, ' ')
            .trim();
        
        taskElement.classList.add(`status-${newStatus.toLowerCase().replace(' ', '-')}`);
    }
    
    function updateColumnCounters() {
        document.querySelectorAll('.kanban-column').forEach(column => {
            const count = column.querySelectorAll('.kanban-task:not(.dragging)').length;
            const counter = column.querySelector('.task-counter') || createCounterElement(column);
            counter.textContent = `${count} tâche${count !== 1 ? 's' : ''}`;
        });
    }

    function createCounterElement(column) {
        const counter = document.createElement('div');
        counter.className = 'task-counter';
        column.querySelector('h3').appendChild(counter);
        return counter;
    }
    
    function showAlert(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} fixed-top mx-auto mt-3`;
        alert.style.maxWidth = '500px';
        alert.style.zIndex = '1100';
        alert.textContent = message;
        
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    }

    initDragEvents();
    updateColumnCounters();
});
