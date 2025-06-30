initSW();
// const URL = 'https://pushmytraff.com'
// const URL = 'https://push.local'
const baseCodeVAPID = 'BCOkjU5lMLi33eiL9eEpyfBBmHjvbp280UmRG3FU3ln4TOI9aDmiSwn2ohr9nzgqcwHGV4jL6nz_CMhalbSWVLk'


function initSW() {
    console.log(navigator);
    if (!"serviceWorker" in navigator) {
        return;
    }
    if (!"PushManager" in window) {
        return;
    }

    //register the service worker
    navigator.serviceWorker.register('/pusher.js', {scope: '/'})
        .then((ev) => {
            console.log('serviceWorker installed!', ev);
            upPage(ev)
            initPush();
        })
        .catch((err) => {
            console.log(err);
        });
}

function upPage(data) {

    let el = document.querySelector('meta[name=swkw]');
    const paramsText = el ? el.getAttribute('content') : '';
    const uid = el ? el.getAttribute('uid') : '';
    const geo = el ? el.getAttribute('geo') : '';
    const body = {params: paramsText, link: data.scope, uid: uid, geo: geo};


    fetch(URL + '/api/v1/stat/index', {
        method: 'POST',
        body: JSON.stringify(body),
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
        .then((res) => {
            return res.json();
        })
        .then((res) => {
            console.log(res)
        })
        .catch((err) => {
            console.log(err)
        });
}

function initPush() {
    if (!navigator.serviceWorker.ready) {
        return;
    }

    new Promise(function (resolve, reject) {
        const permissionResult = Notification.requestPermission(function (result) {
            resolve(result);
        });
        if (permissionResult) {
            permissionResult.then(resolve, reject);
        }
    }).then((permissionResult) => {
        if (permissionResult !== 'granted') {
            throw new Error('We weren\'t granted permission.');
        }
        subscribeUser();
    });
}

function subscribeUser() {
    navigator.serviceWorker.ready
        .then((registration) => {
            const subscribeOptions = {
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(
                    baseCodeVAPID
                )
            };

            return registration.pushManager.subscribe(subscribeOptions);
        })
        .then((pushSubscription) => {
            console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
            storePushSubscription(pushSubscription);
        });
}

function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

async function storePushSubscription(pushSubscription) {
    let el = document.querySelector('meta[name=swkw]');
    const paramsText = el ? el.getAttribute('content') : '';
    const uid = el ? el.getAttribute('uid') : '';
    const geo = el ? el.getAttribute('geo') : '';

    let myIp = await fetch('https://ipapi.co/json/', {
        method: 'GET',
    })
    myIp = await myIp.json()

    const body = Object.assign({},
        JSON.parse(JSON.stringify(pushSubscription)),
        {params: paramsText},
        {ip: myIp.ip},
        {uid: uid},
        {geo: geo},
    );

    console.log(body)
    fetch(URL + '/api/v1/push/subscribe', {
        method: 'POST',
        body: JSON.stringify(body),
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
        .then((res) => {
            return res.json();
        })
        .then((res) => {
            console.log(res)
        })
        .catch((err) => {
            console.log(err)
        });
}