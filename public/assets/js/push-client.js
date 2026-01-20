const VAPID_PUBLIC_KEY = 'BNn2FYRLm1VvW3BFQ6JRkTnaXeVaqJP89_p-9TjnpYl9En0Dh5wVVx-OEYHqZVLok9WaX8GAJ1_XLa4Y1juogMA';

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

async function registerServiceWorker() {
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js');
            console.log('Service Worker registrado com sucesso:', registration);
            return registration;
        } catch (error) {
            console.error('Falha ao registrar Service Worker:', error);
            return null;
        }
    } else {
        console.warn('Push Notifications não suportadas neste navegador.');
        return null;
    }
}

async function subscribeUserToPush() {
    const registration = await navigator.serviceWorker.ready;
    if (!registration) return;

    try {
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
        });

        console.log('User is subscribed:', subscription);
        await sendSubscriptionToBackEnd(subscription);
        return subscription;
    } catch (err) {
        if (Notification.permission === 'denied') {
            console.warn('Permissão para notificações foi negada.');
        } else {
            console.error('Falha ao inscrever o usuário: ', err);
        }
        return null;
    }
}

async function sendSubscriptionToBackEnd(subscription) {
    try {
        const response = await fetch('/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(subscription)
        });

        if (!response.ok) {
            throw new Error('Falha ao salvar inscrição no backend');
        }
        console.log('Inscrição salva no servidor.');
    } catch (err) {
        console.error('Erro ao enviar inscrição:', err);
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    const registration = await registerServiceWorker();
    
    if (registration) {
        if (Notification.permission === 'default') {
             addPushToggleButton();
        } else if (Notification.permission === 'granted') {
             subscribeUserToPush();
        }
    }
});

function addPushToggleButton() {
    // Evita duplicar botão
    if (document.getElementById('btn-enable-push')) return;

    const btn = document.createElement('button');
    btn.id = 'btn-enable-push';
    btn.innerHTML = '<i class="fas fa-bell"></i> Ativar Notificações';
    btn.className = 'btn btn-primary btn-sm position-fixed bottom-0 end-0 m-3 shadow';
    btn.style.zIndex = 9999;
    btn.onclick = async () => {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            await subscribeUserToPush();
            btn.remove();
        }
    };
    
    document.body.appendChild(btn);
}
