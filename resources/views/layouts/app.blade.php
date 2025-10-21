<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Esmalteria Vida Maria') }} - @yield('title', 'Sistema')</title>
        <meta name="description" content="Sistema completo de agendamentos e gestão financeira para Esmalteria Vida Maria">

        <!-- Favicons e Ícones -->
        <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192" href="/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/manifest.json">
        <meta name="msapplication-TileColor" content="#0A1647">
        <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
        <meta name="theme-color" content="#D4AF37">
        
        <!-- PWA Meta Tags -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Vida Maria">
        <meta name="mobile-web-app-capable" content="yes">
        
        <!-- Icons -->
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="apple-touch-icon" href="/apple-icon-180x180.png">
        <link rel="icon" sizes="192x192" href="/android-icon-192x192.png">
        <link rel="icon" sizes="512x512" href="/icon-512.png">
        
        <!-- Splash Screens iOS -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="apple-touch-startup-image" href="/icon-512.png">
        
        <!-- Splash Screens para diferentes dispositivos iOS -->
        <link rel="apple-touch-startup-image" href="/android-icon-192x192.png" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)">
        <link rel="apple-touch-startup-image" href="/android-icon-192x192.png" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3)">
        <link rel="apple-touch-startup-image" href="/android-icon-192x192.png" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)">
        <link rel="apple-touch-startup-image" href="/android-icon-192x192.png" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)">
        <link rel="apple-touch-startup-image" href="/android-icon-192x192.png" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)">
        <link rel="apple-touch-startup-image" href="/android-icon-192x192.png" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)">
        <link rel="apple-touch-startup-image" href="/android-icon-192x192.png" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)">
        <link rel="apple-touch-startup-image" href="/android-icon-192x192.png" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts (Build de Produção) -->
        @php
            $manifestPath = public_path('build/manifest.json');
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
                $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
            }
        @endphp

        @if (!empty($cssFile))
            <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
        @endif
        @if (!empty($jsFile))
            <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
        @endif
        
        <!-- Sistema de Sincronização Offline -->
        <script>
            // Sistema de Sincronização Offline - Vida Maria Esmalteria
            class OfflineSync {
                constructor() {
                    this.dbName = 'VidaMariaOffline';
                    this.dbVersion = 1;
                    this.db = null;
                    this.syncQueue = [];
                    this.isOnline = navigator.onLine;
                    this.syncInProgress = false;
                    this.justCameOnline = false;
                    
                    this.init();
                }

                async init() {
                    await this.openDatabase();
                    this.setupEventListeners();
                    this.createStatusIndicator();
                    this.loadPendingSyncs();
                    
                    if (this.isOnline) {
                        this.syncPendingData();
                    }
                }

                async openDatabase() {
                    return new Promise((resolve, reject) => {
                        const request = indexedDB.open(this.dbName, this.dbVersion);
                        
                        request.onerror = () => reject(request.error);
                        request.onsuccess = () => {
                            this.db = request.result;
                            resolve();
                        };
                        
                        request.onupgradeneeded = (event) => {
                            const db = event.target.result;
                            
                            // Store para agendamentos offline
                            if (!db.objectStoreNames.contains('agendamentos')) {
                                const agendamentosStore = db.createObjectStore('agendamentos', { keyPath: 'id', autoIncrement: true });
                                agendamentosStore.createIndex('timestamp', 'timestamp', { unique: false });
                                agendamentosStore.createIndex('status', 'status', { unique: false });
                            }
                            
                            // Store para clientes offline
                            if (!db.objectStoreNames.contains('clientes')) {
                                const clientesStore = db.createObjectStore('clientes', { keyPath: 'id', autoIncrement: true });
                                clientesStore.createIndex('timestamp', 'timestamp', { unique: false });
                            }
                            
                            // Store para profissionais offline
                            if (!db.objectStoreNames.contains('profissionais')) {
                                const profissionaisStore = db.createObjectStore('profissionais', { keyPath: 'id', autoIncrement: true });
                                profissionaisStore.createIndex('timestamp', 'timestamp', { unique: false });
                            }
                            
                            // Store para fila de sincronização
                            if (!db.objectStoreNames.contains('syncQueue')) {
                                const syncStore = db.createObjectStore('syncQueue', { keyPath: 'id', autoIncrement: true });
                                syncStore.createIndex('timestamp', 'timestamp', { unique: false });
                                syncStore.createIndex('type', 'type', { unique: false });
                            }
                        };
                    });
                }

                setupEventListeners() {
                    // Detectar mudanças de conexão
                    window.addEventListener('online', () => {
                        this.isOnline = true;
                        this.justCameOnline = true;
                        this.updateStatusIndicator();
                        this.syncPendingData();
                        this.showNotification('✅ Conexão restaurada! Sincronizando dados...', 'success');
                    });

                    window.addEventListener('offline', () => {
                        this.isOnline = false;
                        this.updateStatusIndicator();
                        this.showNotification('⚠️ Você está offline. Dados serão salvos localmente.', 'warning');
                    });

                    // Interceptar requisições para armazenar offline
                    this.interceptRequests();
                }

                createStatusIndicator() {
                    // Criar indicador minimalista no canto inferior direito
                    const indicator = document.createElement('div');
                    indicator.id = 'minimal-status';
                    indicator.className = 'minimal-status';
                    indicator.innerHTML = `
                        <div class="minimal-icon">🌐</div>
                        <div class="minimal-text">Conectado</div>
                        <div class="minimal-tooltip">Conectado</div>
                    `;
                    document.body.appendChild(indicator);
                    console.log('Sistema de sincronização offline ativado');
                }

                updateStatusIndicator() {
                    // Atualizar indicador minimalista
                    this.updateMinimalIndicator();
                    // Atualizar banner principal
                    this.updateBanner();
                }
                
                updateMinimalIndicator() {
                    const indicator = document.getElementById('minimal-status');
                    if (!indicator) return;
                    
                    const icon = indicator.querySelector('.minimal-icon');
                    const text = indicator.querySelector('.minimal-text');
                    const tooltip = indicator.querySelector('.minimal-tooltip');
                    
                    if (!this.isOnline) {
                        indicator.className = 'minimal-status offline';
                        icon.innerHTML = '📡';
                        text.textContent = 'Offline';
                        tooltip.textContent = 'Sem conexão';
                    } else if (this.syncInProgress) {
                        indicator.className = 'minimal-status syncing';
                        icon.innerHTML = '⚡';
                        text.textContent = 'Sincronizando...';
                        tooltip.textContent = 'Sincronizando...';
                    } else if (this.syncQueue.length > 0) {
                        indicator.className = 'minimal-status pending';
                        icon.innerHTML = '📶';
                        text.textContent = `${this.syncQueue.length} pendente(s)`;
                        tooltip.textContent = `${this.syncQueue.length} pendente(s)`;
                    } else {
                        indicator.className = 'minimal-status online';
                        icon.innerHTML = '🌐';
                        text.textContent = 'Conectado';
                        tooltip.textContent = 'Conectado';
                    }
                }
                
                updateBanner() {
                    const banner = document.getElementById('connection-banner');
                    const statusText = banner.querySelector('.status-text');
                    const syncInfo = banner.querySelector('.sync-info');
                    const icon = banner.querySelector('.icon');
                    
                    if (!this.isOnline) {
                        banner.className = 'connection-banner offline';
                        banner.style.display = 'block';
                        statusText.textContent = '⚠️ Você está OFFLINE';
                        syncInfo.textContent = 'Dados serão salvos localmente e sincronizados quando voltar online';
                        icon.textContent = '⚠️';
                        document.body.classList.add('banner-visible');
                    } else if (this.syncInProgress) {
                        banner.className = 'connection-banner syncing';
                        banner.style.display = 'block';
                        statusText.textContent = '🔄 Sincronizando dados...';
                        syncInfo.textContent = 'Aguarde enquanto sincronizamos seus dados offline';
                        icon.textContent = '🔄';
                        document.body.classList.add('banner-visible');
                    } else if (this.syncQueue.length > 0) {
                        banner.className = 'connection-banner';
                        banner.style.display = 'block';
                        statusText.textContent = '📡 Online - Dados pendentes';
                        syncInfo.textContent = `${this.syncQueue.length} itens aguardando sincronização`;
                        icon.textContent = '📡';
                        document.body.classList.add('banner-visible');
                    } else {
                        // Quando online e sincronizado, mostrar banner compacto de sucesso
                        if (this.justCameOnline) {
                            banner.className = 'connection-banner success-banner';
                            banner.style.display = 'block';
                            statusText.textContent = '✅ Sincronizado';
                            syncInfo.textContent = 'Dados atualizados com sucesso';
                            icon.textContent = '✅';
                            document.body.classList.add('banner-visible', 'success-banner');
                            
                            // Esconder após 2 segundos
                            setTimeout(() => {
                                banner.style.display = 'none';
                                document.body.classList.remove('banner-visible', 'success-banner');
                                this.justCameOnline = false;
                            }, 2000);
                        } else {
                            banner.style.display = 'none';
                            document.body.classList.remove('banner-visible', 'success-banner');
                        }
                    }
                }

                interceptRequests() {
                    // Interceptar formulários
                    document.addEventListener('submit', (e) => {
                        if (!this.isOnline) {
                            e.preventDefault();
                            this.handleOfflineForm(e.target);
                        }
                    });

                    // Interceptar cliques em botões de ação
                    document.addEventListener('click', (e) => {
                        if (!this.isOnline && e.target.matches('[data-action]')) {
                            e.preventDefault();
                            this.handleOfflineAction(e.target);
                        }
                    });
                }

                async handleOfflineForm(form) {
                    const formData = new FormData(form);
                    const data = Object.fromEntries(formData.entries());
                    
                    // Determinar tipo de ação baseado na URL
                    const action = form.action;
                    let type = 'unknown';
                    
                    if (action.includes('/agendamentos')) {
                        type = 'agendamento';
                    } else if (action.includes('/clientes')) {
                        type = 'cliente';
                    } else if (action.includes('/profissionais')) {
                        type = 'profissional';
                    }
                    
                    // Adicionar timestamp
                    data.timestamp = new Date().toISOString();
                    data.status = 'pending';
                    data.type = type;
                    
                    // Salvar no IndexedDB
                    await this.saveToOfflineDB(type, data);
                    
                    // Adicionar à fila de sincronização
                    await this.addToSyncQueue(type, data, form.method);
                    
                    this.showNotification(`✅ ${type} salvo offline! Será sincronizado quando voltar online.`, 'success');
                    
                    // Simular sucesso para o usuário
                    this.simulateFormSuccess(form);
                }

                async handleOfflineAction(button) {
                    const action = button.dataset.action;
                    const data = JSON.parse(button.dataset.data || '{}');
                    
                    data.timestamp = new Date().toISOString();
                    data.status = 'pending';
                    data.type = action;
                    
                    await this.saveToOfflineDB(action, data);
                    await this.addToSyncQueue(action, data, 'POST');
                    
                    this.showNotification(`✅ Ação "${action}" salva offline!`, 'success');
                }

                async saveToOfflineDB(type, data) {
                    const transaction = this.db.transaction([type], 'readwrite');
                    const store = transaction.objectStore(type);
                    return store.add(data);
                }

                async addToSyncQueue(type, data, method = 'POST') {
                    const syncItem = {
                        type: type,
                        data: data,
                        method: method,
                        timestamp: new Date().toISOString(),
                        retries: 0
                    };
                    
                    const transaction = this.db.transaction(['syncQueue'], 'readwrite');
                    const store = transaction.objectStore('syncQueue');
                    return store.add(syncItem);
                }

                async loadPendingSyncs() {
                    const transaction = this.db.transaction(['syncQueue'], 'readonly');
                    const store = transaction.objectStore('syncQueue');
                    const request = store.getAll();
                    
                    return new Promise((resolve) => {
                        request.onsuccess = () => {
                            this.syncQueue = request.result || [];
                            this.updateStatusIndicator();
                            resolve();
                        };
                    });
                }

                async syncPendingData() {
                    if (!this.isOnline || this.syncInProgress || this.syncQueue.length === 0) {
                        return;
                    }
                    
                    this.syncInProgress = true;
                    this.updateStatusIndicator();
                    
                    const itemsToSync = [...this.syncQueue];
                    let successCount = 0;
                    let errorCount = 0;
                    
                    for (const item of itemsToSync) {
                        try {
                            await this.syncItem(item);
                            await this.removeFromSyncQueue(item.id);
                            successCount++;
                        } catch (error) {
                            console.error('Erro ao sincronizar item:', error);
                            item.retries++;
                            
                            if (item.retries < 3) {
                                // Reagendar para tentar novamente
                                await this.updateSyncQueueItem(item);
                            } else {
                                // Remover após 3 tentativas
                                await this.removeFromSyncQueue(item.id);
                                errorCount++;
                            }
                        }
                    }
                    
                    this.syncInProgress = false;
                    this.updateStatusIndicator();
                    
                    // Disparar evento customizado de sincronização
                    const syncEvent = new CustomEvent('sync-complete', {
                        detail: {
                            success: errorCount === 0,
                            count: successCount,
                            errors: errorCount,
                            total: itemsToSync.length
                        }
                    });
                    window.dispatchEvent(syncEvent);
                    
                    if (successCount > 0) {
                        this.showNotification(`✅ ${successCount} itens sincronizados com sucesso!`, 'success');
                    }
                    
                    if (errorCount > 0) {
                        this.showNotification(`⚠️ ${errorCount} itens falharam na sincronização.`, 'error');
                    }
                }

                async syncItem(item) {
                    const url = this.getSyncUrl(item.type);
                    const options = {
                        method: item.method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(item.data)
                    };
                    
                    const response = await fetch(url, options);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    return response.json();
                }

                getSyncUrl(type) {
                    const baseUrl = window.location.origin;
                    
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

                async removeFromSyncQueue(id) {
                    const transaction = this.db.transaction(['syncQueue'], 'readwrite');
                    const store = transaction.objectStore('syncQueue');
                    return store.delete(id);
                }

                async updateSyncQueueItem(item) {
                    const transaction = this.db.transaction(['syncQueue'], 'readwrite');
                    const store = transaction.objectStore('syncQueue');
                    return store.put(item);
                }

                simulateFormSuccess(form) {
                    // Mostrar mensagem de sucesso
                    const successMsg = document.createElement('div');
                    successMsg.className = 'alert alert-success';
                    successMsg.innerHTML = `
                        <div style="background: #10B981; color: white; padding: 12px; border-radius: 8px; margin: 10px 0;">
                            ✅ Dados salvos offline! Serão sincronizados quando voltar online.
                        </div>
                    `;
                    
                    form.parentNode.insertBefore(successMsg, form.nextSibling);
                    
                    // Limpar formulário
                    form.reset();
                    
                    // Remover mensagem após 5 segundos
                    setTimeout(() => {
                        successMsg.remove();
                    }, 5000);
                }

                showNotification(message, type = 'info') {
                    const notification = document.createElement('div');
                    notification.className = `notification notification-${type}`;
                    notification.innerHTML = `
                        <div style="
                            position: fixed;
                            top: 80px;
                            right: 20px;
                            background: ${type === 'success' ? '#10B981' : type === 'error' ? '#EF4444' : type === 'warning' ? '#F59E0B' : '#3B82F6'};
                            color: white;
                            padding: 16px 20px;
                            border-radius: 10px;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                            z-index: 1001;
                            max-width: 300px;
                            font-size: 14px;
                            font-weight: 500;
                            animation: slideIn 0.3s ease;
                        ">
                            ${message}
                        </div>
                    `;
                    
                    // Adicionar animação
                    const style = document.createElement('style');
                    style.textContent = `
                        @keyframes slideIn {
                            from { transform: translateX(100%); opacity: 0; }
                            to { transform: translateX(0); opacity: 1; }
                        }
                    `;
                    document.head.appendChild(style);
                    
                    document.body.appendChild(notification);
                    
                    // Remover após 5 segundos
                    setTimeout(() => {
                        notification.remove();
                    }, 5000);
                }
            }

            // Inicializar quando a página carregar
            document.addEventListener('DOMContentLoaded', () => {
                window.offlineSync = new OfflineSync();
            });

            // Exportar para uso global
            window.OfflineSync = OfflineSync;
        </script>
        
        <!-- Estilos para Banner de Status -->
        <style>
            .connection-banner {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                background: linear-gradient(135deg, #0A1647, #142C7C);
                color: white;
                padding: 8px 20px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                animation: slideDown 0.3s ease;
                height: 45px;
            }
            
            .connection-banner.offline {
                background: linear-gradient(135deg, #F59E0B, #D97706);
            }
            
            .connection-banner.syncing {
                background: linear-gradient(135deg, #3B82F6, #1D4ED8);
            }
            
            .connection-banner.error {
                background: linear-gradient(135deg, #EF4444, #DC2626);
            }
            
            .connection-banner.success-banner {
                background: linear-gradient(135deg, #10B981, #059669);
                height: 35px;
                padding: 6px 20px;
            }
            
            .success-banner .banner-text {
                margin: 0 10px;
            }
            
            .success-banner .status-text {
                font-size: 13px;
            }
            
            .success-banner .sync-info {
                font-size: 11px;
            }
            
            .banner-content {
                display: flex;
                align-items: center;
                justify-content: space-between;
                max-width: 1200px;
                margin: 0 auto;
                height: 100%;
            }
            
            .banner-icon {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .banner-icon .icon {
                font-size: 16px;
                animation: pulse 2s infinite;
            }
            
            .banner-text {
                flex: 1;
                margin: 0 15px;
            }
            
            .status-text {
                font-weight: 600;
                font-size: 14px;
                display: block;
                line-height: 1.2;
            }
            
            .sync-info {
                font-size: 12px;
                opacity: 0.9;
                display: block;
                margin-top: 1px;
                line-height: 1.2;
            }
            
            .banner-close {
                background: rgba(255,255,255,0.2);
                border: none;
                color: white;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                transition: background 0.3s ease;
                flex-shrink: 0;
            }
            
            .banner-close:hover {
                background: rgba(255,255,255,0.3);
            }
            
            @keyframes slideDown {
                from { transform: translateY(-100%); }
                to { transform: translateY(0); }
            }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
            
            /* Ajustar conteúdo quando banner estiver visível */
            body.banner-visible .min-h-screen {
                margin-top: 45px;
            }
            
            /* Ajustar para banner de sucesso (menor) */
            body.banner-visible.success-banner .min-h-screen {
                margin-top: 35px;
            }
            
            /* Ajustar posição do avatar no desktop quando banner estiver visível */
            @media (min-width: 768px) {
                /* Ajustar navegação para não sobrepor */
                body.banner-visible nav {
                    margin-top: 45px;
                }
                
                /* Ajustar dropdown do usuário */
                body.banner-visible .hidden.sm\\:flex.sm\\:items-center.sm\\:ml-6 {
                    position: relative;
                    z-index: 1000;
                }
                
                /* Garantir que o avatar fique visível */
                body.banner-visible .inline-flex.items-center.gap-2 {
                    position: relative;
                    z-index: 1001;
                }
            }
            
            /* Ajustar z-index do banner para não sobrepor elementos importantes */
            .connection-banner {
                z-index: 999;
            }
            
            /* Indicador minimalista */
            .minimal-status {
                position: fixed;
                bottom: 70px;
                left: 50%;
                transform: translateX(-50%);
                min-width: 35px;
                height: 35px;
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                z-index: 1000;
                font-size: 18px;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                padding: 0 8px;
                gap: 6px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            }
            
            .minimal-status:hover {
                transform: translateX(-50%) scale(1.1);
            }
            
            .minimal-status.offline {
                background: rgba(239, 68, 68, 0.2);
                border-color: rgba(239, 68, 68, 0.4);
                animation: pulse 2s infinite;
            }
            
            .minimal-status.syncing {
                background: rgba(59, 130, 246, 0.2);
                border-color: rgba(59, 130, 246, 0.4);
                animation: spin 1s linear infinite;
            }
            
            .minimal-status.pending {
                background: rgba(245, 158, 11, 0.2);
                border-color: rgba(245, 158, 11, 0.4);
                animation: pulse 1.5s infinite;
            }
            
            .minimal-status.online {
                background: rgba(16, 185, 129, 0.2);
                border-color: rgba(16, 185, 129, 0.4);
            }
            
            /* Estilos específicos para ícones modernos */
            .minimal-status.offline .minimal-icon {
                opacity: 0.6;
                filter: grayscale(80%);
                transform: rotate(45deg);
            }
            
            .minimal-status.syncing .minimal-icon {
                animation: flash 0.8s infinite;
            }
            
            .minimal-status.pending .minimal-icon {
                animation: pulse 1.5s infinite;
            }
            
            .minimal-status.online .minimal-icon {
                animation: float 3s ease-in-out infinite;
            }
            
            /* Estilos para o texto */
            .minimal-text {
                font-size: 12px;
                font-weight: 600;
                color: white;
                white-space: nowrap;
                text-shadow: 0 1px 2px rgba(0,0,0,0.3);
            }
            
            .minimal-status.offline .minimal-text {
                color: #FCA5A5;
            }
            
            .minimal-status.syncing .minimal-text {
                color: #93C5FD;
            }
            
            .minimal-status.pending .minimal-text {
                color: #FCD34D;
            }
            
            .minimal-status.online .minimal-text {
                color: #6EE7B7;
            }
            
            @keyframes flash {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.7; transform: scale(1.1); }
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-2px); }
            }
            
            .minimal-tooltip {
                position: absolute;
                bottom: 40px;
                right: 0;
                background: rgba(0,0,0,0.9);
                color: white;
                padding: 6px 10px;
                border-radius: 4px;
                font-size: 11px;
                white-space: nowrap;
                opacity: 0;
                transform: translateY(5px);
                transition: all 0.3s ease;
                pointer-events: none;
                z-index: 1001;
                font-weight: 500;
            }
            
            .minimal-tooltip::after {
                content: '';
                position: absolute;
                top: 100%;
                right: 8px;
                border: 3px solid transparent;
                border-top-color: rgba(0,0,0,0.9);
            }
            
            .minimal-status:hover .minimal-tooltip {
                opacity: 1;
                transform: translateY(0);
            }
            
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        </style>
        
        <!-- PWA Install Prompt -->
        <script>
            let deferredPrompt;
            let isInstalled = false;
            
            // Verificar se já está instalado
            if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
                isInstalled = true;
            }
            
            // Prompt de instalação automático
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                
                // Mostrar prompt após 3 segundos se não estiver instalado
                if (!isInstalled) {
                    setTimeout(() => {
                        showInstallPrompt();
                    }, 3000);
                }
            });
            
            function showInstallPrompt() {
                if (deferredPrompt && !isInstalled) {
                    // Criar modal de instalação
                    const installModal = document.createElement('div');
                    installModal.innerHTML = `
                        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                            <div style="background: white; padding: 30px; border-radius: 15px; max-width: 400px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                                <div style="font-size: 48px; margin-bottom: 20px;">💅</div>
                                <h3 style="color: #0A1647; margin-bottom: 15px; font-size: 24px;">Instalar App Vida Maria</h3>
                                <p style="color: #666; margin-bottom: 25px; line-height: 1.5;">Instale o app para acesso rápido e funcionalidade offline!</p>
                                <div style="display: flex; gap: 10px; justify-content: center;">
                                    <button id="install-app-btn" style="background: #D4AF37; color: #0A1647; border: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px;">
                                        📱 Instalar Agora
                                    </button>
                                    <button id="install-later-btn" style="background: #f5f5f5; color: #666; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-size: 16px;">
                                        Depois
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.body.appendChild(installModal);
                    
                    // Event listeners
                    document.getElementById('install-app-btn').addEventListener('click', async () => {
                        if (deferredPrompt) {
                            deferredPrompt.prompt();
                            const { outcome } = await deferredPrompt.userChoice;
                            console.log('Resultado da instalação:', outcome);
                            deferredPrompt = null;
                        }
                        document.body.removeChild(installModal);
                    });
                    
                    document.getElementById('install-later-btn').addEventListener('click', () => {
                        document.body.removeChild(installModal);
                    });
                }
            }
            
            // Registrar Service Worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/service-worker.js')
                        .then((registration) => {
                            console.log('Service Worker registrado:', registration);
                            
                            // Verificar atualizações
                            registration.addEventListener('updatefound', () => {
                                const newWorker = registration.installing;
                                newWorker.addEventListener('statechange', () => {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        // Nova versão disponível
                                        if (confirm('Nova versão disponível! Deseja atualizar?')) {
                                            window.location.reload();
                                        }
                                    }
                                });
                            });
                        })
                        .catch((error) => {
                            console.log('Erro ao registrar Service Worker:', error);
                        });
                });
            }
            
            // Detectar instalação
            window.addEventListener('appinstalled', () => {
                isInstalled = true;
                console.log('App instalado com sucesso!');
            });
            
            // Sistema de notificações de sincronização
            window.addEventListener('sync-complete', (event) => {
                const { success, count, errors } = event.detail;
                if (success) {
                    showSyncNotification(`✅ ${count} itens sincronizados com sucesso!`, 'success');
                } else {
                    showSyncNotification(`⚠️ ${count} itens sincronizados, ${errors} erros`, 'warning');
                }
            });
            
            function showSyncNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `sync-notification sync-${type}`;
                notification.innerHTML = `
                    <div style="
                        position: fixed;
                        top: 80px;
                        right: 20px;
                        background: ${type === 'success' ? '#10B981' : '#F59E0B'};
                        color: white;
                        padding: 16px 20px;
                        border-radius: 10px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                        z-index: 1001;
                        max-width: 350px;
                        font-size: 14px;
                        font-weight: 500;
                        animation: slideInRight 0.3s ease;
                        display: flex;
                        align-items: center;
                        gap: 10px;
                    ">
                        <span style="font-size: 20px;">${type === 'success' ? '✅' : '⚠️'}</span>
                        <span>${message}</span>
                    </div>
                `;
                
                // Adicionar animação se não existir
                if (!document.querySelector('#sync-animations')) {
                    const style = document.createElement('style');
                    style.id = 'sync-animations';
                    style.textContent = `
                        @keyframes slideInRight {
                            from { transform: translateX(100%); opacity: 0; }
                            to { transform: translateX(0); opacity: 1; }
                        }
                        @keyframes slideOutRight {
                            from { transform: translateX(0); opacity: 1; }
                            to { transform: translateX(100%); opacity: 0; }
                        }
                    `;
                    document.head.appendChild(style);
                }
                
                document.body.appendChild(notification);
                
                // Remover após 5 segundos com animação
                setTimeout(() => {
                    notification.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 300);
                }, 5000);
            }
        </script>
    </head>
    <body class="font-sans antialiased">
        <!-- Banner de Status de Conexão -->
        <div id="connection-banner" class="connection-banner" style="display: none;">
            <div class="banner-content">
                <div class="banner-icon">
                    <span class="icon">📡</span>
                </div>
                <div class="banner-text">
                    <span class="status-text">Status da Conexão</span>
                    <span class="sync-info"></span>
                </div>
                <button class="banner-close" onclick="this.parentElement.parentElement.style.display='none'">
                    ✕
                </button>
            </div>
        </div>
        
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main class="py-6 pb-20 sm:pb-6">
                @if (session('success'))
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </body>
</html>
