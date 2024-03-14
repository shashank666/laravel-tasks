let d = {}


self.addEventListener('push', function (e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        //notifications aren't supported or permission not granted!
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        d = msg;
        let random=Math.floor(100000 + Math.random() * 900000);

        e.waitUntil(self.registration.showNotification(msg.title, {
            body: msg.body,
            icon: msg.icon,
            image: msg.image,
            badge: msg.icon,
            vibrate:[500, 250, 500, 250],
            silent:false,
            tag:"opined-"+random,
            renotify: true,
            requireInteraction: true,
            timestamp:new Date().getTime()
        }));
    }
});

    self.addEventListener('notificationclick', function (event) {
        let urlToOpen = d.data.goto;
        event.notification.close();
        clients.openWindow(urlToOpen);
    });
