const CACHE_NAME = 'vida-maria-v2';
const urlsToCache = [
  '/',
  '/dashboard',
  '/agenda',
  '/clientes',
  '/profissionais',
  '/servicos',
  '/backup',
  '/financeiro',
  '/offline.html',
  '/manifest.json',
  '/logo.svg',
  '/favicon.ico',
  '/android-icon-192x192.png',
  '/icon-512.svg',
  '/build/assets/app.css',
  '/build/assets/app.js',
];

// Instalação do Service Worker
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Cache aberto');
        return cache.addAll(urlsToCache);
      })
  );
  self.skipWaiting();
});

// Ativação e limpeza de cache antigo
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log('Removendo cache antigo:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  return self.clients.claim();
});

// Estratégia: Network First, depois Cache
self.addEventListener('fetch', (event) => {
  // Apenas cache GET requests
  if (event.request.method !== 'GET') return;
  
  event.respondWith(
    fetch(event.request)
      .then((response) => {
        // Verifica se recebeu uma resposta válida
        if (!response || response.status !== 200 || response.type === 'error') {
          return response;
        }

        // Clone a resposta
        const responseToCache = response.clone();

        caches.open(CACHE_NAME)
          .then((cache) => {
            cache.put(event.request, responseToCache);
          });

        return response;
      })
      .catch(() => {
        // Se falhar, tenta buscar do cache
        return caches.match(event.request)
          .then((response) => {
            if (response) {
              return response;
            }
            
            // Se não tiver no cache, retorna página offline personalizada
            return caches.match('/offline.html');
          });
      })
  );
});

// Sincronização em background
self.addEventListener('sync', (event) => {
  console.log('Background sync triggered:', event.tag);
  
  if (event.tag === 'sync-data') {
    event.waitUntil(syncOfflineData());
  } else if (event.tag === 'sync-agendamentos') {
    event.waitUntil(syncAgendamentos());
  } else if (event.tag === 'sync-clientes') {
    event.waitUntil(syncClientes());
  } else if (event.tag === 'sync-profissionais') {
    event.waitUntil(syncProfissionais());
  }
});

async function syncOfflineData() {
  try {
    console.log('Iniciando sincronização de dados offline...');
    
    // Abrir IndexedDB
    const db = await openIndexedDB();
    const transaction = db.transaction(['syncQueue'], 'readonly');
    const store = transaction.objectStore('syncQueue');
    const items = await getAllFromStore(store);
    
    console.log(`${items.length} itens para sincronizar`);
    
    for (const item of items) {
      try {
        await syncItem(item);
        await removeFromSyncQueue(item.id);
        console.log(`Item ${item.id} sincronizado com sucesso`);
      } catch (error) {
        console.error(`Erro ao sincronizar item ${item.id}:`, error);
      }
    }
    
    console.log('Sincronização concluída');
  } catch (error) {
    console.error('Erro na sincronização:', error);
  }
}

async function syncAgendamentos() {
  console.log('Sincronizando agendamentos...');
  // Implementar sincronização específica de agendamentos
}

async function syncClientes() {
  console.log('Sincronizando clientes...');
  // Implementar sincronização específica de clientes
}

async function syncProfissionais() {
  console.log('Sincronizando profissionais...');
  // Implementar sincronização específica de profissionais
}

async function openIndexedDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('VidaMariaOffline', 1);
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error);
  });
}

async function getAllFromStore(store) {
  return new Promise((resolve, reject) => {
    const request = store.getAll();
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error);
  });
}

async function syncItem(item) {
  const url = getSyncUrl(item.type);
  const response = await fetch(url, {
    method: item.method,
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(item.data)
  });
  
  if (!response.ok) {
    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
  }
  
  return response.json();
}

function getSyncUrl(type) {
  const baseUrl = self.location.origin;
  
  switch (type) {
    case 'agendamento':
      return `${baseUrl}/agendamentos`;
    case 'cliente':
      return `${baseUrl}/clientes`;
    case 'profissional':
      return `${baseUrl}/profissionais`;
    default:
      return `${baseUrl}/api/sync`;
  }
}

async function removeFromSyncQueue(id) {
  const db = await openIndexedDB();
  const transaction = db.transaction(['syncQueue'], 'readwrite');
  const store = transaction.objectStore('syncQueue');
  return store.delete(id);
}

// Notificações Push
self.addEventListener('push', (event) => {
  const data = event.data ? event.data.json() : {};
  const title = data.title || 'Esmalteria Vida Maria';
  const options = {
    body: data.body || 'Nova notificação',
    icon: '/logo.svg',
    badge: '/logo.svg',
    vibrate: [200, 100, 200],
    data: data,
    actions: data.actions || []
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Clique em notificação
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  
  event.waitUntil(
    clients.openWindow(event.notification.data.url || '/')
  );
});

