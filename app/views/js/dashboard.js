document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('taskStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['À faire', 'En cours', 'Terminées'],
            datasets: [{
                data: [
                    window.tasksByStatus.todo,
                    window.tasksByStatus.inProgress,
                    window.tasksByStatus.done
                ],
                backgroundColor: [
                    '#9ca3af',
                    '#818cf8',
                    '#10b981'
                ],
                borderWidth: 0,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        font: {
                            family: 'Inter',
                            size: 12
                        },
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
});
