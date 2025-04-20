#!/bin/bash
set -e  # Encerra o script em caso de erro

# —————————————————————————————————————————
# 1) Funções de logging
define_colors() {
  GREEN="\033[32m"
  YELLOW="\033[33m"
  RED="\033[31m"
  RESET="\033[0m"
}
log_info() {
  echo -e "${GREEN}$1${RESET}"
}
log_warning() {
  echo -e "${YELLOW}$1${RESET}"
}
log_error() {
  echo -e "${RED}$1${RESET}"
}

spinner() {
  local pid=$1
  local delay=0.1
  local spin='-\|/'
  echo -n "⏳ "
  while kill -0 $pid 2>/dev/null; do
    for i in $(seq 0 3); do
      echo -ne "\b${spin:$i:1}"
      sleep $delay
    done
  done
  echo -ne "\b"
}

define_colors

# —————————————————————————————————————————
# 2) Garantir .env disponível antes de carregar variáveis
echo "# Entrypoint iniciando em $(date)"
cd /var/www/html || { log_error "Falha ao acessar /var/www/html"; exit 1; }

if [ ! -f .env ]; then
  log_info "Copiando .env.example para .env..."
  cp .env.example .env
else
  log_info ".env já existe. Pulando cópia."
fi

# Gera a chave de aplicação do Laravel
log_info "Gerando chave de aplicação..."
php artisan key:generate --force
if [ $? -ne 0 ]; then
  log_error "Falha ao gerar chave de aplicação"
  exit 1
fi
log_info "Chave de aplicação gerada com sucesso!"

# Gera a chave JWT automaticamente
log_info "Gerando chave JWT..."
if ! php artisan jwt:secret --force --no-interaction; then
  log_error "Falha ao gerar JWT_SECRET."
  exit 1
fi
log_info "JWT_SECRET gerado com sucesso!"

# Carrega as variáveis do .env após gerar as chaves
export $(grep -v '^#' .env | xargs)

# Verifica vars de BD
if [ -z "$DB_HOST" ] || [ -z "$DB_PORT" ] || [ -z "$DB_DATABASE" ] \
   || [ -z "$DB_USERNAME" ] || [ -z "$DB_PASSWORD" ]; then
    log_error "Variáveis de conexão não definidas no .env (DB_HOST, DB_PORT, etc)."
    exit 1
fi

# —————————————————————————————————————————
# 3) Espera o MySQL ficar online
log_info "⏳ Esperando o MySQL em $DB_HOST:$DB_PORT..."
(
  until mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" \
        -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
    sleep 2
  done
) &
spinner $!
log_info "✅ MySQL está online!"

# —————————————————————————————————————————
# 4) Fluxo Laravel: migrations + seed condicional
log_info "Executando migrations..."
if ! php artisan migrate --force --quiet; then
  log_error "Falha ao executar migrações. Verifique a conexão com o banco de dados ou as migrações."
  exit 1
fi

# Só roda se o usuário não existir ainda
USER_EMAIL="professor@uefs.gov.br"
EXISTS=$(mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" \
         -D"$DB_DATABASE" -se "SELECT EXISTS(SELECT 1 FROM users WHERE email='$USER_EMAIL');")
if [ "$EXISTS" -eq 0 ]; then
  log_info "Seed inicial não encontrada. Executando seeder..."
  if ! php artisan db:seed --class=DatabaseSeeder --force --quiet; then
    log_error "Falha ao executar seed. Verifique o seeder."
    exit 1
  fi
else
  log_info "Seed inicial já aplicada. Pulando seeder."
fi

# —————————————————————————————————————————
# 5) Fluxo Node/Vite/Swagger adicional
if [ -f package.json ]; then
  log_info "Instalando dependências do Node.js..."
  if ! npm install --silent; then
    log_error "Falha no npm install."
    exit 1
  fi
  if ! npm run build --silent; then
    log_error "Falha no npm run build."
    exit 1
  fi
else
  log_warning "package.json não encontrado. Pulando Node/Vite."
fi

if php artisan list | grep -q "l5-swagger:generate"; then
  log_info "Gerando documentação do Swagger..."
  if ! php artisan l5-swagger:generate --quiet; then
    log_error "Falha ao gerar docs Swagger."
    exit 1
  fi
else
  log_warning "L5Swagger não instalado. Pulando geração de docs."
fi

# —————————————————————————————————————————
# 6) Links finais e start do FPM
log_info "Número de argumentos recebidos: $#"

# Exibe os links de acesso independentemente dos argumentos
echo ""
log_info "==================== Links de Acesso ===================="
log_info "Frontend: http://localhost:8000/"
log_info "API Doc:  http://localhost:8000/api/documentation"
log_info "========================================================"
log_info "User: professor@uefs.gov.br  |  Pass: admin123"
echo ""

# Executa o comando fornecido, se houver
if [ "$#" -ne 0 ]; then
  log_info "Executando comando fornecido: $@"
  exec "$@"
else
  # Caso contrário, inicia o PHP-FPM em primeiro plano
  log_info "Iniciando PHP-FPM..."
  exec php-fpm --nodaemonize
fi

log_info "Script concluído com sucesso."
exit 0