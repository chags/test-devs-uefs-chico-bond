# **Projeto UEFS - NETRA**  
## **Engenheiro de Software no Projeto UEFS - NETRA**  
### **Candidato**: Francisco Cristiano Chagas  
### **Whatsapp**: (85)9 9959-3777 

---

## **Escopo do Teste Técnico**  

Você deverá desenvolver uma API RESTful com as seguintes funcionalidades:  

- **CRUD de Usuários**: Criar, ler, atualizar e deletar usuários.  
- **CRUD de Posts**: Criar, ler, atualizar e deletar postagens.  
- **CRUD de Tags**: Criar, ler, atualizar e deletar tags (palavras-chave).  

### **Regras de Relacionamento**  
- Um **Post** pode ter várias **Tags**.  
- Uma **Tag** pode estar associada a vários **Posts** (relacionamento N:N).  

---

## **Como Rodar o Projeto Localmente**  

### **Pré-requisitos**  

Certifique-se de ter instalado na sua máquina:  
- [Docker](https://www.docker.com/)  
- [Docker Compose](https://docs.docker.com/compose/)  
- [Git](https://git-scm.com/)  

### **Passo a Passo**  

1. **Clone o Repositório**  
   ```bash
   git clone https://github.com/chags/test-devs-uefs-chico-bond.git
   ```

2. **Entre na Pasta do Projeto**  
   ```bash
   cd test-devs-uefs-chico-bond
   ```

3. **Suba o Ambiente Docker**  
   Execute o comando abaixo para iniciar o ambiente Docker. Este comando já configura tudo automaticamente, incluindo a instalação das dependências, migrações do banco de dados e seeders:  
   ```bash
   docker-compose up -d
   ```

   > **Nota**: O arquivo `entrypoint.sh` foi configurado para automatizar todas as etapas necessárias, como instalar as dependências (`composer install`), rodar as migrações (`php artisan migrate`) e popular o banco de dados com dados iniciais (`php artisan db:seed`). Portanto, não é necessário executar esses comandos manualmente.

4. **Aguarde a Finalização da Configuração**  
   Após executar o comando acima, aguarde alguns instantes até que todos os serviços estejam prontos. Você pode verificar o status dos containers com:  
   ```bash
   docker ps
   ```

5. **Acesse a Aplicação**  
   Assim que o ambiente estiver pronto, você poderá acessar a aplicação usando os links abaixo:  

   ```bash
   echo ""
   log_info "==================== Links de Acesso ===================="
   log_info "Frontend: http://localhost:8000/"
   log_info "API Doc:  http://localhost:8000/api/documentation"
   log_info "========================================================"
   log_info "Usuário Pré-Criado:"
   log_info "Email: professor@uefs.gov.br  |  Senha: admin123"
   echo ""
   ```

   - **Frontend**: A interface principal da aplicação estará disponível em:  
     ```
     http://localhost:8000/
     ```

   - **Documentação da API (Swagger)**: A documentação interativa da API estará disponível em:  
     ```
     http://localhost:8000/api/documentation
     ```

   - **Credenciais Iniciais**:  
     - **Email**: `professor@uefs.gov.br`  
     - **Senha**: `admin123`  

6. **Faça Login e Obtenha o Token JWT**  
   Para acessar os endpoints protegidos, siga o fluxo abaixo:  
   - Acesse o endpoint `/api/login` no Swagger.  
   - Use as credenciais fornecidas acima.  
   - Faça a requisição POST e copie o token JWT retornado na resposta.  
   - Configure o token no Swagger clicando no botão **Authorize** e inserindo o token no formato `Bearer <token>`.  

---
## **Como Executar os Testes**

Os testes foram implementados usando [PHPUnit](https://phpunit.de/), uma ferramenta amplamente utilizada para testes unitários e de integração em projetos PHP. Os testes garantem que o código funcione conforme o esperado e ajudam a prevenir regressões durante o desenvolvimento.

### **Pré-requisitos**

Certifique-se de que o ambiente esteja configurado corretamente antes de executar os testes:

1. **Ambiente Dockerizado**:
   - Já esta tudo pronto para os tests o banco de dados e o arquivo .ENV.testing


### **Executando os Testes**

Para executar os testes, siga os passos abaixo:

1. **Entre no Container do Laravel**  
   Acesse o container onde o Laravel está rodando:
   ```bash
   docker exec -it laravel bash
   ```

2. **Execute os Testes**  
   Use o comando abaixo para executar todos os testes unitários e de integração:
   ```bash
   php artisan test
   ```

   - **Saída Esperada**:  
     O PHPUnit exibirá um relatório detalhado, mostrando quantos testes foram executados, quantos passaram e, se houver falhas, quais testes falharam e por quê.


### **Estrutura dos Testes**

Os testes estão organizados no diretório `tests/App/Http/Controllers`:
   - Porque copiar a estrutura App em testes, facilita muito para saber de onde estão vindo o teste. 
   - Para quem vai avaliar fica melhor ainda.

1. **Testes Unitarios e de integração**:
   - Localizado em  `tests/App/Http/Controllers`.
   - Os dois estão junto. para facilitar a avaliação ao executar o `php artisan test` estará fazendo os dois tipos
   ao mesmo tempo , isso garante que toda a aplicação ira funcionar num contexto geral.
---

### **Otimizações de Banco de Dados**

Para garantir um desempenho eficiente e escalável, aplicamos as seguintes otimizações no banco de dados:

- **Eager Loading**: Evitamos o problema N+1 ao carregar relacionamentos (como posts e tags) de forma eficiente em uma única consulta.
- **Índices**: Adicionamos índices em colunas frequentemente utilizadas, como chaves estrangeiras (`user_id`, `post_id`), para acelerar consultas.
- **Paginação**: Limitamos o número de registros retornados por requisição, melhorando a performance e a experiência do usuário.
- **Seleção de Colunas Específicas**: Reduzimos o volume de dados transferidos ao selecionar apenas as colunas necessárias em consultas.

Essas práticas garantem que o sistema seja rápido, sustentável e preparado para lidar com grandes volumes de dados.
   

## **Decisões Técnicas e Particularidades**  

1. **Automação via Entrypoint**:  
   - O arquivo `entrypoint.sh` foi criado para automatizar todas as etapas de configuração da aplicação, incluindo a instalação de dependências, execução de migrações e geração de dados iniciais. Isso garante que o ambiente seja configurado de forma consistente e sem intervenção manual.  

2. **JWT para Autenticação**:  
   - Implementei autenticação JWT para garantir segurança nas rotas protegidas. O usuário inicial criado pelo seeder permite que você faça login imediatamente após configurar o projeto e obtenha um token JWT para acessar os endpoints protegidos.  

3. **Relacionamento N:N**:  
   - Utilizei tabelas pivot para permitir que posts tenham várias tags e vice-versa.  

4. **Camada de Serviço (Service Layer)**:  
   - Implementei classes de serviço, como `PostService`,`StorePsotRequest` e `ValidateJwtToken`, para centralizar a lógica de negócio. Isso promove reutilização de código, separação de responsabilidades e facilidade de testes.  

5. **Swagger para Documentação**:  
   - A documentação da API foi gerada usando Swagger, facilitando a visualização e testes dos endpoints diretamente no navegador.  

6. **Docker**:  
   - O uso de Docker garante que o ambiente seja consistente e fácil de configurar em qualquer máquina, independentemente do sistema operacional.  

7. **Testes**: teste unitarios e de integração Juntos, esses testes garantem que o sistema seja robusto, confiável e fácil de manter.

8. **Otmizações de Banco de dados**: Essas otimizações resultaram em um sistema mais rápido, eficiente e preparado para lidar com grandes volumes de dados, garantindo uma experiência fluida tanto para os usuários quanto para os desenvolvedores..

9. **Usou IA?**: Claro que sim, eu não seria ótario de não contar com essa ajuda. Tarefas repetitivas nunca mais, kkk!

10. **REDIS ou ElastcSeach**: Ta maluco o projeto fica muito caro e o tempo não permite, 5 dias é muito bom, mas sabe? 
aqui tem uns comedor de rapadura que não pergunta de onde eu consigo dinheiro para comprar a cana. logo não tenho tempo.
talvez quando você me contratar eu corro com muitas outras coisas. kkkk!

11. **Atendimento as exigências**: Todos os pontos listado na vaga de Sênior foram atendidos cuidadosamente no total
 ou em parte dos códigos para demostrar a habilidade e o conhecimento robusto no Laravel e suas ferramentas.  


   ## **Comandos Úteis para o Docker**  

- **Parar e Remover Todos os Containers**:  
  ```bash
  docker-compose down -v
  ```

- **Limpar Todo o Docker**:  
  ```bash
  docker system prune -a --volumes
  ```

- **Recriar o Ambiente do Zero**:  
  ```bash
  docker-compose down -v && docker-compose up --build
  ```

  ###Atenciosamente: Cristiano chagas

