/**
 * Sistema de Sincronização Offline - Vida Maria Esmalteria
 * Garante que todas as ações offline sejam sincronizadas quando voltar online
 */

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
        const indicator = document.createElement('div');
        indicator.id = 'connection-status';
        indicator.innerHTML = `
            <div class="status-indicator">
                <div class="status-icon">
                    <span class="icon">📡</span>
                </div>
                <div class="status-text">
                    <span class="status">${this.isOnline ? 'Online' : 'Offline'}</span>
                    <span class="sync-status"></span>
                </div>
            </div>
        `;
        
        // Adicionar estilos
        const style = document.createElement('style');
        style.textContent = `
            #connection-status {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                transition: all 0.3s ease;
            }
            
            .status-indicator {
                background: ${this.isOnline ? '#10B981' : '#F59E0B'};
                color: white;
                padding: 8px 16px;
                border-radius: 25px;
                display: flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                font-size: 14px;
                font-weight: 500;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255,255,255,0.2);
            }
            
            .status-icon .icon {
                font-size: 16px;
                animation: ${this.isOnline ? 'pulse' : 'blink'} 2s infinite;
            }
            
            .sync-status {
                font-size: 12px;
                opacity: 0.8;
            }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
            
            @keyframes blink {
                0%, 50% { opacity: 1; }
                51%, 100% { opacity: 0.3; }
            }
            
            .status-indicator.offline {
                background: #F59E0B;
            }
            
            .status-indicator.syncing {
                background: #3B82F6;
            }
            
            .status-indicator.error {
                background: #EF4444;
            }
        `;
        
        document.head.appendChild(style);
        document.body.appendChild(indicator);
    }

    updateStatusIndicator() {
        // Atualizar indicador pequeno
        const indicator = document.getElementById('connection-status');
        if (indicator) {
            const statusIcon = indicator.querySelector('.status-icon .icon');
            const statusText = indicator.querySelector('.status');
            const syncStatus = indicator.querySelector('.sync-status');
            
            if (this.isOnline) {
                indicator.className = 'status-indicator';
                statusIcon.textContent = '📡';
                statusText.textContent = 'Online';
                
                if (this.syncInProgress) {
                    indicator.classList.add('syncing');
                    statusIcon.textContent = '🔄';
                    syncStatus.textContent = 'Sincronizando...';
                } else if (this.syncQueue.length > 0) {
                    syncStatus.textContent = `${this.syncQueue.length} itens pendentes`;
                } else {
                    syncStatus.textContent = 'Sincronizado';
                }
            } else {
                indicator.className = 'status-indicator offline';
                statusIcon.textContent = '⚠️';
                statusText.textContent = 'Offline';
                syncStatus.textContent = 'Dados salvos localmente';
            }
        }
        
        // Atualizar banner principal
        this.updateBanner();
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
