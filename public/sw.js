const CACHE_NAME = 'gestao-materiais-v3';

// Instalação - apenas registra, sem cachear nada
self.addEventListener('install', event => {
    console.log('Service Worker instalado');
    self.skipWaiting();
});

// Ativação - limpa caches antigos
self.addEventListener('activate', event => {
    console.log('Service Worker ativado');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
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

// Fetch - NÃO intercepta nada, apenas passa direto
self.addEventListener('fetch', event => {
    // Simplesmente passa a requisição adiante sem cache
    event.respondWith(fetch(event.request));
});
