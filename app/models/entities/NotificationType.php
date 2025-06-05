<?php

namespace App\Models\Entities;

class NotificationType {
    const TASK_ASSIGNED = 'task_assigned';
 
    const STATUS_CHANGE = 'status_change';
    const TASK_COMMENT = 'task_comment';
    const PROJECT_UPDATE = 'project_update';
    const TASK_CREATED = 'task_created';
    const TASK_UPDATED = 'task_updated';
    const TASK_DELETED = 'task_deleted';
    const PROJECT_DELETED = 'project_deleted';
}
