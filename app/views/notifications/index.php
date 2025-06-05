<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm">
<div class="card-header bg-info text-white d-flex justify-content-between align-items-center rounded-top-4">
                    <h4 class="mb-0">Notifications</h4>
                    <?php if (!empty($notifications)): ?>
                        <a href="/projet_java/app/notifications/mark-all-read" class="btn btn-sm btn-light">
                            Mark all as read
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($notifications)): ?>
                        <div class="alert alert-info text-center">
                            You have no unread notifications.
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($notifications as $notification): ?>
                                <a href="/projet_java/app/notifications/view/<?php echo $notification->id; ?>" 
                                   class="list-group-item list-group-item-action d-flex flex-column <?php echo $notification->is_read ? '' : 'bg-light fw-bold'; ?>"
                                   data-notification-id="<?php echo $notification->id; ?>"
                                   data-entity-type="<?php echo $notification->related_entity_type ?? ''; ?>"
                                   data-entity-id="<?php echo $notification->related_entity_id ?? ''; ?>"
                                   style="border-left: 4px solid <?php echo $notification->is_read ? '#0d6efd00' : '#0d6efd'; ?>;">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h5 class="mb-1">
                                            <i class="fas fa-bell text-primary me-2"></i>
                                            <?php echo htmlspecialchars($notification->message); ?>
                                        </h5>
                                        <small class="text-muted"><?php echo date('M j, g:i a', strtotime($notification->created_at)); ?></small>
                                    </div>
                                    <?php if ($notification->related_entity_type && $notification->related_entity_id): ?>
                                        <small class="text-muted">
                                            Related to: <?php echo ucfirst($notification->related_entity_type); ?> #<?php echo $notification->related_entity_id; ?>
                                        </small>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

