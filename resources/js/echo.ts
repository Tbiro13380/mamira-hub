import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

declare global {
    interface Window {
        Pusher: typeof Pusher;
        Echo: Echo | null;
    }
}

window.Pusher = Pusher;

// Inicializar Echo apenas se as variáveis de ambiente estiverem configuradas
if (import.meta.env.VITE_REVERB_APP_KEY && import.meta.env.VITE_REVERB_HOST) {
    console.log('Inicializando Laravel Echo com Reverb:', {
        host: import.meta.env.VITE_REVERB_HOST,
        port: import.meta.env.VITE_REVERB_PORT ?? 8080,
        scheme: import.meta.env.VITE_REVERB_SCHEME ?? 'http',
    });
    
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
    
    // Log de conexão
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ Conectado ao Reverb!');
    });
    
    window.Echo.connector.pusher.connection.bind('error', (err: any) => {
        console.error('❌ Erro na conexão com Reverb:', err);
    });
} else {
    console.warn('⚠️ Variáveis de ambiente do Reverb não configuradas:', {
        key: !!import.meta.env.VITE_REVERB_APP_KEY,
        host: !!import.meta.env.VITE_REVERB_HOST,
    });
    window.Echo = null;
}

