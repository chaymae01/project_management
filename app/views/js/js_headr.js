


$(document).ready(function() {
    let userHasSeenNotifications = false; // Pour savoir si utilisateur a consulté ses notif

    // Clic sur notification individuelle
    $('.notification-item').click(function(e) {
        e.preventDefault();
        const notificationId = $(this).data('notification-id');
        const url = $(this).attr('href');

        $.post('/projet_java/app/notifications/mark-as-read', {id: notificationId})
         .done(function() {
             userHasSeenNotifications = true;
             window.location.href = url;
         })
         .fail(function() {
             // Même si ça échoue, on continue vers la page
             window.location.href = url;
         });
    });

    // Clic sur "Marquer tout comme lu"
    $('.mark-all-read').click(function(e) {
        e.preventDefault();
        const url = $(this).attr('href');

        $.post(url)
         .done(function() {
             userHasSeenNotifications = true;
             $('.notification-item').removeClass('unread');
             $('.notification-badge').hide();
             window.location.reload();
         })
         .fail(function() {
             // En cas d'erreur, on peut afficher une alerte ou ne rien faire
             alert('Impossible de marquer toutes les notifications comme lues.');
         });
    });

    // Mise à jour du badge selon le nombre de notifications non vues
    function updateBadge(count) {
        const badge = $('.notification-badge');
        if (count > 0) {
            badge.text(count).show();
        } else {
            if (userHasSeenNotifications) {
                badge.hide();
            } else {
                badge.text('●').show();
            }
        }
    }

    // Rafraîchissement périodique
    function refreshNotifications() {
        $.get('/projet_java/app/notifications/unviewed-count')
         .done(function(data) {
             updateBadge(data.count);
         })
         .fail(function() {
             // En cas d'erreur, ne rien faire ou afficher un message
             console.error('Erreur lors du chargement du compteur de notifications.');
         });
    }

    // Appel initial + intervalle
    refreshNotifications();
    setInterval(refreshNotifications, 30000);
});


