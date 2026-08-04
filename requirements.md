# 📋 Requisitos — Hall dos Conquistadores

> Documentação ágil de desenvolvimento do projeto **Hall dos Conquistadores**.
> Para a visão geral dos requisitos funcionais e não funcionais, consulte o [README.md](README.md).

> **Legenda de Status:** 🟢 Concluído | 🟡 Em desenvolvimento | 🔴 Pendente


## 🧩 RF017 — Painel Administrativo (Filament PHP) 🟡 Em desenvolvimento



| 🟡 | **RF017** | **Painel Administrativo (Filament PHP)**     | O sistema deve possuir uma área administrativa para controle completo de usuários, posts, campanhas, enquetes, permissões e configurações.           |

#### Arquivos Criados: 🟢 Concluído
- app/Filament/Widgets/StatsOverviewWidget.php - Cards de estatísticas (usuários, canais, postagens, campanhas)

#### Arquivo Modificado: 🟢 Concluído
- app/Providers/Filament/AdminPanelProvider.php - Substituídos os widgets padrão pelos novos widgets


##### Ajustes Enum Type 🟢 Concluído
O PanelTypeEnum é utilizado nas seguintes partes do sistema:
- ok app/Models/User.php:55	Cast do atributo panel
- ok app/Models/User.php:67-68	Validação de enum
- ok app/Policies/UserPolicy.php:17,35,43,48-49	Políticas de autorização (verifica se é ADMIN/SUPER_ADMIN)
- ok app/Http/Controllers/Auth/RegisteredUserController.php:56	Define panel como APP ao registrar usuário
- ok app/Http/Controllers/Auth/AuthenticatedSessionController.php:44	Redirecionamento após login (verifica se é ADMIN/SUPER_ADMIN)
- ok app/Filament/Resources/Users/UserResource.php:57-63	Scope de queries (filtra usuários baseado no panel)
- ok app/Filament/Resources/Users/Schemas/UserForm.php:149,165	Opções do select e visibilidade do campo
- ok app/Filament/Resources/Users/Tables/UsersTable.php:76	Opções na tabela
- ok app/Filament/Resources/Users/Pages/ListUsers.php:31-39	Filtros por panel
- ok app/Filament/Pages/Profile.php:125	Opções no form de perfil
- ok database/seeders/UserSeeder.php:28	Cria super admin
- ok database/factories/UserFactory.php:23	Default factory























---

## 🧩 RF002 — Cadastro de Seguidores 🟢 Concluído

| Código    | Nome                       | Descrição                                                                                               | Prioridade | Status                | Critérios de Aceitação                                              |
| :-------- | :------------------------- | :------------------------------------------------------------------------------------------------------ | :--------: | :-------------------: | :------------------------------------------------------------------ |
| **RF002** | **Cadastro de Seguidores** | O seguidor deve poder realizar um cadastro simples para seguir canais, comentar e receber notificações. | 🔺 Alta    | 🟡 Em desenvolvimento | O usuário consegue criar uma conta, validar e acessar a plataforma. |

### 🔹 Sub-Requisitos Funcionais

| Código      | Nome                                           | Descrição                                                                                                                            | Prioridade | Status                | Critérios de Aceitação                                                                            |
| :---------- | :--------------------------------------------- | :----------------------------------------------------------------------------------------------------------------------------------- | :--------: | :-------------------: | :------------------------------------------------------------------------------------------------ |
| **RF002.1** | **Formulário híbrido — login/cadastro**        | Deve existir um formulário com e-mail e senha e de cadastro de follower (seguidor). O e-mail deve ser único e a senha criptografada. | 🔺 Alta    | 🟢 Concluído          | Campos obrigatórios validados, erro amigável exibido e redirecionamento para tela de confirmação. |
| **RF002.2** | **Validação de E-mail (Token de Confirmação)** | Após o cadastro, o sistema envia um e-mail com token de confirmação para ativar a conta.                                             | 🔺 Alta    | 🟢 Concluído  | Token expira em 24h; link confirma conta; login bloqueado antes da confirmação.                   |
| **RF002.3** | **Seguir Canais | Notificações**                              | O seguidor autenticado pode seguir, like comentar e receber  notificações de novos posts/campanhas dos canais que seguem.                                                                        | 🟢 Média   | 🔴 Pendente           | Botão alterna "Seguir/Seguindo"; relação persistida (`followers`).                                |

---

### 📋 RF002.3 — Passo a Passo Detalhado

#### 1. Seguir Canal ✅ Concluído

| Item | Status | Detalhes |
|------|--------|----------|
| Migration `channel_follower` | ✅ | `user_id`, `channel_id`, unique |
| Model `User` → `following()` | ✅ | BelongsToMany |
| Model `Channel` → `followers()` | ✅ | BelongsToMany |
| Componente `FollowButton` | ✅ | Livewire com toggle |
| Contador seguidores em tempo real | ✅ | `$followersCount` atualizado |
| Impedir dono de seguir próprio canal | ✅ | Validação no `toggleFollow()` |

---

#### 2. Like em Posts 🔄 Pendente

| Item | Detalhes |
|------|----------|
| Migration `post_likes` | `id`, `user_id`, `post_id`, `timestamps`, unique `[user_id, post_id]` |
| Model `PostLike` | belongsTo User, belongsTo Post |
| Model `Post` | `likes()` → HasMany, `likes_count` via withCount |
| Componente `LikeButton` | Recebe `$postId`, `toggleLike()` com insert/delete |
| View | Botão coração (preenchido/outline) + contador |

---

#### 3. Comentar em Posts 🔄 Pendente

| Item | Detalhes |
|------|----------|
| Migration `comments` | `id`, `user_id`, `post_id`, `parent_id` (nullable), `content`, `timestamps` |
| Model `Comment` | belongsTo User, belongsTo Post, belongsTo Comment (parent) |
| Model `Post` | `comments()` → HasMany |
| Componente `CommentSection` | Lista + formulário, `$replyTo` para replies |
| Métodos | `addComment()`, `reply($id)`, `deleteComment($id)` |
| View | Formulário (textarea + botão), lista com avatar/nome/data, replies indentadas |

---

#### 4. Notificações (Sininho) 🔄 Pendente

| Item | Detalhes |
|------|----------|
| Migration `notifications` | `id`, `user_id`, `type`, `data` (json), `read_at` (nullable), `timestamps` |
| Model `Notification` | belongsTo User, cast data como array, cast read_at como datetime |
| Notification Class | `NewPostPublished` — salva na tabela com post_id, post_title, channel_name |
| Trigger | Observer no Post (evento `created`) → busca seguidores com 🔔 → cria notificação |
| Componente `NotificationCenter` | `$notifications`, `$unreadCount`, `markAsRead()`, `markAllAsRead()` |
| View (Navbar) | Sininho com badge, dropdown com últimas notificações, link para post |

**Fluxo:**
```
Usuário segue canal com 🔔 habilitado
    ↓
Canal publica post
    ↓
Post Observer dispara
    ↓
Busca seguidores com notificação ativa
    ↓
Cria registro na tabela notifications
    ↓
Badge aparece no sininho do navbar
```

---

#### 5. Navbar Atualizado 🔄 Pendente

| Item | Detalhes |
|------|----------|
| Sininho | Ícone com badge de notificações não lidas |
| Dropdown | Últimas notificações com avatar, texto, data, link |
| Botão | "Marcar todas como lida" |

---

#### Resumo Técnico

| Feature | Tecnologia | Tabela | Componente |
|---------|------------|--------|------------|
| Seguir | Livewire | `channel_follower` | `FollowButton` ✅ |
| Like | Livewire | `post_likes` | `LikeButton` |
| Comentar | Livewire | `comments` | `CommentSection` |
| Notificações | Livewire + Observer | `notifications` | `NotificationCenter` |
| Trigger | Post Observer | — | `PostObserver` |

---

## ⚙️ Estratégia Técnica de Implementação (RF002)

### RF002.1 — Formulário de Cadastro Inscrito

- **Ação:** Instalar o Laravel Breeze (versão Blade ou Livewire).
- **Técnica:** O Breeze já traz a validação de `unique:users,email` e o `Hash::make` para a senha.
- **Personalização:** Alterar a `RegisterUserController` para injetar o `type => 'follower'` automaticamente no momento da criação do usuário.
- **Login:**
  - **`APP`** → redireciona para **`home`**.
  - **`ADMIN/SUPER_ADMIN`** → redireciona para **`/admin`**.

#### Rotas de Autenticação (`routes/web.php`)

Divida o arquivo de rotas em grupos claros:

```php
// Grupo 1: Público (Qualquer um vê)
Route::get('/', [LandingController::class, 'index']);
Route::get('/canal/{slug}', [ChannelController::class, 'show']);

// Grupo 2: Autenticação (Breeze)
require __DIR__.'/auth.php';

// Grupo 3: Interação do Seguidor (Logado e Verificado)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/follow/{channel}', [ChannelController::class, 'toggleFollow'])->name('channel.follow');
    Route::post('/comment', [CommentController::class, 'store']);
});
```

---

### RF002.2 — Validação de E-mail

> 📖 [Documentação Laravel: Email Verification](https://laravel.com/docs/12.x/verification)

- **Ação:** Implementar a interface `MustVerifyEmail` no Model `User`.
- **Técnica:** Habilitar o middleware `['auth', 'verified']` nas rotas protegidas.
- **Configuração:** No `.env`, configurar o **Mailtrap** para capturar os e-mails de teste.
- O Laravel já traz toda a lógica de tokens e expiração pronta. Basta implementar a interface no Model `User` e proteger as rotas com o middleware `verified`.

#### ⚠️ Pontos a Analisar|desenvolvimento

- [x] Após cadastro e-mail é enviado com token e ao clicar usuário é validado;
- [x] Ajustar mensagens de erro no login e cadastro.
- [x] Após cadastro mesmo usuário não validando cadastro no e-mail, o sistema o deixa logado.
- [x] Após cadastro, se usuário não validar e-mail no tempo que expira o link de validação, como usuário solicita outro?

---

### RF002.3 — Login e Sessão de Seguidor

- **Ação:** Configurar o middleware de redirecionamento.
- **Técnica:** No `config/session.php`, ajustar o `lifetime` para `120` (2 horas).
- **Segurança:** Garantir que o método `authenticated()` no `LoginController` redirecione:
  - Seguidor → **Home**
  - Criador → **`/admin`**

---

### RF002.4 — Seguir Canais

- **Ação:** Criar migration para a tabela pivô `channel_follower` (`user_id`, `channel_id`).
- **Técnica:** Utilizar o método `toggle()` do Eloquent:
  ```php
  $user->following()->toggle($channelId);
  ```
- **Interface:** Criar componente **Livewire** para o botão "Seguir" (sem recarregar página).
- **Model User:** Campo `type` (string/enum) na tabela `users` (`admin`, `creator`, `follower`).
- **Relação:** Muitos-para-Muitos (`BelongsToMany`) via tabela pivô `channel_follower`.
- **Sugestão:** Usar constantes ou Enum no PHP (ex: `User::TYPE_CREATOR`, `User::TYPE_FOLLOWER`).

---

### RF002.5 — Notificações

- **Ação:** Criar classe de notificação:
  ```bash
  php artisan make:notification NewCampaignPublished
  ```
- **Técnica:** Implementar os canais `mail` e `database`.
- **Fila:** Adicionar a interface `ShouldQueue` para envio assíncrono.
- **Ferramenta:** Laravel Notifications — permite enviar alertas por e-mail, banco de dados (sininho) ou SMS/WhatsApp no futuro.

#### 📬 Gestão de E-mails e Filas (Jobs/Queues)

- **Dica para XAMPP/Windows:** Comece com `MAIL_MAILER=log` no `.env`. Os e-mails aparecem nos logs. Quando pronto, use o **Mailtrap**.
- **Queues:** Use o driver `database`. Rode `php artisan queue:work` para processar e-mails pendentes.

---

### Proteção de Rotas com Middlewares

| Tipo de Rota        | Proteção                     | Exemplo                              |
| :------------------ | :--------------------------- | :----------------------------------- |
| **Rotas Públicas**  | Nenhuma                      | Landing page, página do canal (slug) |
| **Rotas Seguidor**  | `auth` + `verified`          | Seguir canal, comentar               |
| **Rotas Admin**     | Middleware nativo do Filament | Painel `/admin`                      |

---

## 🗺️ Fases de Implementação

### Fase 1 — Preparação da Estrutura (Backend)

- [ ] Atualização do Model `User`:
  - Adicionar coluna `panel_type` (Enum: `super-admin`, `admin`, `app`).
  - Definir valor padrão como `app`.
- [ ] Criação da tabela de relacionamento:
  - Migration `create_channel_follower_table` (Pivô).
  - Implementar `following()` e `followers()` nos Models.

### Fase 2 — Autenticação e Verificação (Breeze)

- [x] Instalar o Breeze (Blade Stack):
  ```bash
  composer require laravel/breeze --dev
  php artisan breeze:install blade
  php artisan migrate
  npm install && npm run dev
  ```
- [x] Personalizar o `RegisterController` para `panel_type => app`.
- [ ] Ativar `MustVerifyEmail` no Model `User`.
- [ ] Configurar middleware de redirecionamento (impedir `app` de acessar `/admin`).

#### Recuperação de Rotas após Breeze

> O Breeze pode sobrescrever o `routes/web.php`. Use `git diff` e `git checkout` para recuperar e adicionar apenas `require __DIR__.'/auth.php';`.

### Fase 3 — Funcionalidades Sociais (Interação)

- [ ] Rota `POST /follow/{channel}` com `toggle()`.
- [ ] Notificação `NewCampaignPublished`.
- [ ] Configurar fila (`database`) no `.env`.

### Fase 4 — Frontend e Feedback

- [ ] Customizar telas do Breeze para combinar com o tema Retro/Dark.
- [ ] Dashboard do usuário: visão de canais seguidos.

---

## ⚙️ Requisitos Não Funcionais (RNF) — RF002

| Código       | Nome           | Descrição                                                                   | Categoria           | Status           |
| :----------- | :------------- | :-------------------------------------------------------------------------- | :-----------------: | :--------------: |
| **RNF002.1** | Segurança      | Senhas, tokens e e-mails devem ser criptografados e tratados com segurança. | 🔐 Segurança        | 🟢 Ativo         |
| **RNF002.2** | Performance    | O envio de e-mail de confirmação deve ocorrer em menos de 5 segundos.       | ⚡ Desempenho        | 🟡 Monitoramento |
| **RNF002.3** | Usabilidade    | Interface com feedback visual, mensagens claras e responsividade.           | 🎨 UX/UI            | 🟢 Ativo         |
| **RNF002.4** | Escalabilidade | Sistema deve suportar 10.000 seguidores simultâneos sem degradação.         | ☁️ Infraestrutura   | 🔴 Planejado     |

---

## ✅ Registro de Desenvolvimento (RF002)

> O que já foi implementado para o RF002:

- [x] Instalação do Laravel Breeze (Blade Stack) + ajuste de layout e rotas.
- [x] Configuração do cadastro de seguidor (`RegisteredUserController.php`).
- [x] Configuração de login com redirecionamento por tipo (`AuthenticatedSessionController.php`).
- [x] Ajuste nos Models:
  - `User` → `following(): BelongsToMany`
  - `Channel` → `followers(): BelongsToMany`
- [x] Organização de rotas no `web.php`.
- [x] Criação da tabela de seguidores: `php artisan make:migration create_channel_follower_table`.
- [x] Componente Livewire para botão "Seguir": `php artisan make:livewire FollowButton`.
- [x] Desativação do login do Filament (comentar `->login()` em `AdminPanelProvider.php`).

---

## 📌 Backlog — Próximos Passos

| Status | Tarefa                                                                                       |
| :----: | :------------------------------------------------------------------------------------------- |
|   🟡   | Criar seção "Canais que eu sigo" na Home do usuário                                          |
|   🟡   | Planejar RF003 (API)                                                                         |
|   🟢   | Estilizar páginas de Login/Registro do Breeze com CSS Dark/Retro                             |
|   🟡   | Ajustar `getTabs` da `ListUser`                                                              |
|   🟡   | Renomear níveis: `ADMIN` → Streamer (verde) \| `APP` → Seguidor (laranja)                   |
|   🟡   | Listagem de seguidores em separado                                                           |
|   🟡   | Seguidor: form simples, edição simples, deleção; somente `super-admin` visualiza lista       |
|   🔴   | Perfil master user logado ADMIN/FILAMENT                                                     |
|   🔴   | Perfil seguidor web                                                                          |
