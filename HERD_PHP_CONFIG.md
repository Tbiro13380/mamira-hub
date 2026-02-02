# Configuração do PHP para Upload de Vídeos

Para permitir uploads de vídeos maiores (até 25MB), você precisa ajustar as configurações do PHP no Herd.

## Opção 1: Via Interface do Herd (Recomendado)

1. Abra o Herd
2. Vá em **Settings** → **PHP**
3. Clique em **Edit php.ini**
4. Procure por `upload_max_filesize` e `post_max_size`
5. Altere para:
   ```
   upload_max_filesize = 25M
   post_max_size = 25M
   max_execution_time = 300
   max_input_time = 300
   ```
6. Salve o arquivo
7. Reinicie o Herd ou o PHP-FPM

## Opção 2: Via Terminal

1. Encontre o arquivo php.ini do Herd (geralmente em `C:\Program Files\Herd\config\php\`)
2. Edite o arquivo `php.ini` correspondente à versão do PHP que você está usando
3. Altere os valores conforme acima
4. Reinicie o Herd

## Verificar Configuração

Execute no terminal:
```bash
php -i | findstr "upload_max_filesize post_max_size"
```

Deve mostrar:
```
upload_max_filesize => 25M => 25M
post_max_size => 25M => 25M
```

## Nota

Após fazer essas alterações, **reinicie o Herd** para que as mudanças tenham efeito.

