(() => {
    'use strict'

    const ServiceDomain = 'https://pushmytraff.com/api/v1'
    // const ServiceDomain = 'http://localhost/api/v1'
    const WebPush = {
        init () {
            self.addEventListener('push', this.notificationPush.bind(this))
            self.addEventListener('notificationclick', this.notificationClick.bind(this))
            self.addEventListener('notificationclose', this.notificationClose.bind(this))
        },

        /**
         * Handle notification push event.
         *
         * https://developer.mozilla.org/en-US/docs/Web/Events/push
         *
         * @param {NotificationEvent} event
         */
        notificationPush (event) {
            if (!(self.Notification && self.Notification.permission === 'granted')) {
                return
            }

            // https://developer.mozilla.org/en-US/docs/Web/API/PushMessageData
            if (event.data) {
                event.waitUntil(
                    this.sendNotification(event.data.json())
                )
            }
        },

        /**
         * Handle notification click event.
         *
         * https://developer.mozilla.org/en-US/docs/Web/Events/notificationclick
         *
         * @param {NotificationEvent} event
         */
        notificationClick (event) {
            fetch( ServiceDomain + '/push/statistic', {
                method: 'POST',
                body: JSON.stringify({'action':'click', 'id' : event.notification.data.id}),
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            }).then(()=>{
                console.log( event.notification.data);
            }).catch((err) => {
                console.log(err)
            });
            let url = event.notification.data.url;
            event.notification.close(); // Android needs explicit close.

            event.waitUntil(
                clients.matchAll({type: 'window'}).then( windowClients => {
                    // Check if there is already a window/tab open with the target URL
                    for (var i = 0; i < windowClients.length; i++) {
                        var client = windowClients[i];
                        // If so, just focus it.
                        if (client.url === url && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    // If not, then open the target URL in a new window/tab.
                    if (clients.openWindow) {
                        return clients.openWindow(url);
                    }
                })
            );
        },

        /**
         * Handle notification close event (Chrome 50+, Firefox 55+).
         *
         * https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerGlobalScope/onnotificationclose
         *
         * @param {NotificationEvent} event
         */
        notificationClose (event) {
            self.registration.pushManager.getSubscription().then(subscription => {
                if (subscription) {
                    this.dismissNotification(event, subscription)
                }
            })
        },

        /**
         * Send notification to the user.
         *
         * https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification
         *
         * @param {PushMessageData|Object} data
         */
        sendNotification (data) {
            fetch(ServiceDomain + '/push/statistic', {
                method: 'POST',
                body: JSON.stringify({'action':'show', 'id':data.data.id}),
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    //'X-CSRF-Token': token
                }
            }).then((res) => {
                return self.registration.showNotification(data.title, data)
            }).catch((err) => {
                console.log(err)
            });
        },

        /**
         * Send request to server to dismiss a notification.
         *
         * @param  {NotificationEvent} event
         * @param  {String} subscription.endpoint
         * @return {Response}
         */
        dismissNotification ({ notification }, { endpoint }) {
            if (!notification.data || !notification.data.id) {
                return
            }

            //const data = new FormData()
            //data.append('endpoint', endpoint)

            // Send a request to the server to mark the notification as closed.
            fetch( ServiceDomain + '/push/statistic', {
                method: 'POST',
                body: JSON.stringify({'action':'close', 'id':notification.data.id}),
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            }).catch((err) => {
                console.log(err)
            });
        }
    }

    WebPush.init()
})()