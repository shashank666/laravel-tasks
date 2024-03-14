'use strict';

var _registration = null;

function registerServiceWorker() {
    let now=new Date().getTime();
    return navigator.serviceWorker.register('/js/service-worker.js?'+now)
    .then(function(registration) {
      //console.log('Service worker successfully registered.');
      _registration = registration;
      return registration;
    })
    .catch(function(err) {
      //console.error('Unable to register service worker.', err);
    });
  }


  function askPermission() {
    return new Promise(function(resolve, reject) {
      const permissionResult = Notification.requestPermission(function(result) {
        resolve(result);
      });
      if (permissionResult) {
        permissionResult.then(resolve, reject);
      }
    })
    .then(function(permissionResult) {
      if (permissionResult !== 'granted') {
        alert('Please allow notification permission to www.weopined.com for display notifications');
        //throw new Error('We weren\'t granted permission.');
      }
      else{
        subscribeUserToPush();
      }
    });
  }

  function getSWRegistration(){
    var promise = new Promise(function(resolve, reject) {
    // do a thing, possibly async, then…
    if (_registration != null) {
      resolve(_registration);
    }
    else {
      reject(Error("It broke"));
    }
    });
    return promise;
  }


/**
 * Subscribe the user to push
 */
function subscribeUserToPush() {
    getSWRegistration()
    .then(function(registration) {
      const subscribeOptions = {
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array("BN1KEbip0e6GjDMwaDRtRc5X4bNuUEeLlNQmB6KaZOv8KuZyStyqVZ93NueCW66yy/EG+d8Ow4fz0n5WfgGHBfU=")
      };
      return registration.pushManager.subscribe(subscribeOptions);
    })
    .then(function(pushSubscription) {
      //console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
      sendSubscriptionToBackEnd(pushSubscription);
      return pushSubscription;
    });
  }


function unsubscribeUser() {
    _registration.pushManager.getSubscription()
    .then(function(subscription) {
      if (subscription) {
        const token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
        fetch('/notification/unsubscribe',{
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': token
        },
        body:JSON.stringify(subscription)
        }).then(function(response) {
            subscription.unsubscribe();
        }).catch(function(err){});
      }
    })
    .catch(function(error) {
    })
  }

/**
 * send PushSubscription to server with AJAX.
 * @param {object} pushSubscription
 */

function sendSubscriptionToBackEnd(subscription) {
    const token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    return fetch('/notification/subscribe', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': token
      },
      body: JSON.stringify(subscription)
    })
    .then(function(response) {
      if (!response.ok) {
        //throw new Error('Bad status code from server.');
      }
      return response;
    })
    .then(function(responseData) {
        //console.log('responseData',responseData);
      if (!(responseData.data && responseData.data.success)) {
       // throw new Error('Bad response from server.');
      }
    });
  }


/**
 * urlBase64ToUint8Array
 *
 * @param {string} base64String a public vapid key
 */
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
      .replace(/\-/g, '+')
      .replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

registerServiceWorker();


$(document).on('click','#allow-notification',function(){
    $('#notificationsModal').modal('hide');
    askPermission();
});
