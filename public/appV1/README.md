# NexusFlow - Sistema SaaS Multi-Empresas

![NexusFlow Logo](assets/images/logo.png)

## ðŸ“‹ Sobre o Projeto

**NexusFlow** Ã© um sistema SaaS (Software as a Service) multi-empresas desenvolvido para gerenciar mÃºltiplas organizaÃ§Ãµes dentro de uma Ãºnica plataforma. O sistema oferece controle granular de usuÃ¡rios, filiais, licenciamento e funcionalidades especÃ­ficas para diferentes segmentos de mercado.

## ðŸš€ CaracterÃ­sticas Principais

### Multi-Tenant Architecture
- **Isolamento de dados** por empresa
- **AdministraÃ§Ã£o centralizada** para super admins
- **GestÃ£o independente** para cada empresa
- **Escalabilidade** para mÃºltiplas organizaÃ§Ãµes

### GestÃ£o de UsuÃ¡rios e PermissÃµes
- **PapÃ©is hierÃ¡rquicos**: Super Admin, Admin Empresa, Gerente, Operador, Visualizador
- **Controle granular** de permissÃµes por funcionalidade
- **GestÃ£o por filiais** com usuÃ¡rios distribuÃ­dos geograficamente
- **Sistema de convites** e ativaÃ§Ã£o por e-mail

### Licenciamento Inteligente
- **PerÃ­odo trial** de 3 meses gratuitos
- **Controle automÃ¡tico** de expiraÃ§Ã£o de licenÃ§as
- **Planos flexÃ­veis** adaptÃ¡veis ao tamanho da empresa
- **Faturamento integrado** com notificaÃ§Ãµes

### Multi-Segmentos
Suporte nativo para diversos segmentos:
- ðŸª **Varejo**
- ðŸ­ **IndÃºstria**
- ðŸ—ï¸ **ConstruÃ§Ã£o**
- ðŸ¦ **Financeiro**
- ðŸ“¢ **Marketing**
- ðŸ’» **Tecnologia**
- ðŸ¥ **SaÃºde**
- ðŸ“š **EducaÃ§Ã£o**

## ðŸ› ï¸ Tecnologias Utilizadas

### Frontend
- **HTML5** - Estrutura semÃ¢ntica moderna
- **CSS3** - Estilos customizados com variÃ¡veis CSS
- **JavaScript ES6+** - Funcionalidades interativas
- **Bootstrap 5.3** - Framework CSS responsivo
- **Bootstrap Icons** - Biblioteca de Ã­cones
- **Chart.js** - GrÃ¡ficos e visualizaÃ§Ãµes

### Fontes e Design
- **Google Fonts (Inter)** - Tipografia moderna
- **Design System** - Paleta de cores consistente
- **Responsive Design** - AdaptÃ¡vel a todos os dispositivos

## ðŸ“ Estrutura do Projeto

```
nexusflow-saas/
â”œâ”€â”€ assets/
â”‚   â”œâ”€â”€ css/
â”‚   â”‚   â””â”€â”€ nexusflow.css          # Estilos principais
â”‚   â”œâ”€â”€ js/
â”‚   â”‚   â””â”€â”€ nexusflow.js           # JavaScript principal
â”‚   â”œâ”€â”€ images/                    # Imagens e logos
â”‚   â””â”€â”€ fonts/                     # Fontes customizadas
â”œâ”€â”€ components/
â”‚   â””â”€â”€ base-layout.html           # Template base
â”œâ”€â”€ pages/
â”‚   â”œâ”€â”€ auth/
â”‚   â”‚   â”œâ”€â”€ login.html             # PÃ¡gina de login
â”‚   â”‚   â”œâ”€â”€ register.html          # Cadastro de empresa
â”‚   â”‚   â””â”€â”€ forgot-password.html   # RecuperaÃ§Ã£o de senha
â”‚   â”œâ”€â”€ admin/
â”‚   â”‚   â”œâ”€â”€ companies.html         # GestÃ£o de empresas
â”‚   â”‚   â”œâ”€â”€ system-users.html      # UsuÃ¡rios do sistema
â”‚   â”‚   â””â”€â”€ billing.html           # Faturamento
â”‚   â”œâ”€â”€ company/
â”‚   â”‚   â”œâ”€â”€ users.html             # GestÃ£o de usuÃ¡rios
â”‚   â”‚   â”œâ”€â”€ branches.html          # GestÃ£o de filiais
â”‚   â”‚   â””â”€â”€ settings.html          # ConfiguraÃ§Ãµes
â”‚   â”œâ”€â”€ user/
â”‚   â”‚   â””â”€â”€ profile.html           # Perfil do usuÃ¡rio
â”‚   â”œâ”€â”€ dashboard.html             # Dashboard principal
â”‚   â”œâ”€â”€ reports.html               # RelatÃ³rios
â”‚   â””â”€â”€ analytics.html             # Analytics
â”œâ”€â”€ docs/                          # DocumentaÃ§Ã£o
â””â”€â”€ README.md                      # Este arquivo
```

## ðŸŽ¨ Identidade Visual

### Paleta de Cores
- **Azul Escuro (#2C3E50)**: Elementos primÃ¡rios, navegaÃ§Ã£o
- **Azul Ciano (#3498DB)**: BotÃµes, links, destaques
- **Cinza Claro (#ECF0F1)**: Fundos, Ã¡reas de conteÃºdo
- **Verde Sucesso (#2ECC71)**: Mensagens positivas
- **Vermelho Alerta (#E74C3C)**: Mensagens de erro
- **Laranja Aviso (#F39C12)**: Mensagens de atenÃ§Ã£o

### Logo e Branding
- **Nome**: NexusFlow
- **Conceito**: ConexÃ£o e fluxo contÃ­nuo de informaÃ§Ãµes
- **Ãcone**: Diagrama de conexÃµes estilizado
- **Tipografia**: Inter (Google Fonts)

## ðŸ” NÃ­veis de Acesso

### Super Admin (Administrador Geral)
- Visualizar e gerenciar todas as empresas
- Controlar licenÃ§as e faturamento
- Gerenciar usuÃ¡rios do sistema
- Acessar relatÃ³rios globais

### Admin Empresa (Administrador da Empresa)
- Gerenciar usuÃ¡rios da prÃ³pria empresa
- Configurar filiais e departamentos
- Controlar permissÃµes internas
- Acessar relatÃ³rios da empresa

### Gerente
- Gerenciar usuÃ¡rios de sua filial/departamento
- Visualizar relatÃ³rios especÃ­ficos
- Executar operaÃ§Ãµes limitadas

### Operador
- Executar operaÃ§Ãµes do dia a dia
- Acessar funcionalidades especÃ­ficas
- Visualizar dados permitidos

### Visualizador
- Apenas visualizaÃ§Ã£o de dados
- Sem permissÃµes de ediÃ§Ã£o
- Acesso limitado a relatÃ³rios

## ðŸš€ Como Usar

### 1. ConfiguraÃ§Ã£o Inicial
1. FaÃ§a o download do projeto
2. Extraia os arquivos em seu servidor web
3. Configure as URLs das APIs no arquivo `assets/js/nexusflow.js`
4. Ajuste as configuraÃ§Ãµes de e-mail para envio de convites

### 2. Primeiro Acesso
1. Acesse a pÃ¡gina de cadastro (`pages/auth/register.html`)
2. Cadastre a primeira empresa (receberÃ¡ status de Super Admin)
3. Configure as informaÃ§Ãµes bÃ¡sicas da empresa
4. Comece a adicionar usuÃ¡rios e filiais

### 3. IntegraÃ§Ã£o com APIs
O sistema foi desenvolvido como template frontend, preparado para integraÃ§Ã£o com APIs REST. Os pontos de integraÃ§Ã£o estÃ£o marcados nos arquivos JavaScript com comentÃ¡rios `// IntegraÃ§Ã£o com API`.

#### Endpoints Reais do Backend (Laravel)
Base: `{BASE_URL}/api`
```
POST /api/registrar
GET  /api/verificar-email/{token}
POST /api/login
POST /api/v1/password/solicitar-reset
POST /api/v1/password/validar-token
POST /api/v1/password/resetar-senha
GET  /api/usuarios
GET  /api/usuarios/empresa/{id_empresa}
GET  /api/empresas
GET  /api/v1/empresas
GET  /api/filiais
GET  /api/v1/filiais
GET  /api/v1/dashboard/resumo
```
## ðŸ“± Responsividade

O sistema Ã© totalmente responsivo e funciona perfeitamente em:
- **Desktop** (1920px+)
- **Laptop** (1366px - 1919px)
- **Tablet** (768px - 1365px)
- **Mobile** (320px - 767px)

## ðŸ”§ PersonalizaÃ§Ã£o

### Cores
Edite as variÃ¡veis CSS no arquivo `assets/css/nexusflow.css`:
```css
:root {
    --primary-dark: #2C3E50;
    --primary-blue: #3498DB;
    /* ... outras variÃ¡veis */
}
```

### Logo
Substitua o Ã­cone no componente `.logo-icon` ou adicione sua prÃ³pria imagem em `assets/images/`.

### Funcionalidades
Adicione novas pÃ¡ginas seguindo a estrutura existente e utilizando o template base em `components/base-layout.html`.

## ðŸ“Š Funcionalidades Implementadas

### âœ… AutenticaÃ§Ã£o
- [x] Login com e-mail e senha
- [x] Cadastro de empresa com wizard
- [x] RecuperaÃ§Ã£o de senha
- [x] IntegraÃ§Ã£o com Google/Microsoft (preparado)

### âœ… Dashboard
- [x] MÃ©tricas principais
- [x] GrÃ¡ficos interativos
- [x] Atividades recentes
- [x] AÃ§Ãµes rÃ¡pidas

### âœ… GestÃ£o de Empresas
- [x] Lista de empresas
- [x] Filtros avanÃ§ados
- [x] Status de licenÃ§as
- [x] AÃ§Ãµes em massa

### âœ… GestÃ£o de UsuÃ¡rios
- [x] CRUD completo de usuÃ¡rios
- [x] Sistema de papÃ©is e permissÃµes
- [x] Convites por e-mail
- [x] GestÃ£o por filiais

### âœ… GestÃ£o de Filiais
- [x] Cadastro de filiais
- [x] VisualizaÃ§Ã£o em cards
- [x] EstatÃ­sticas por filial
- [x] IntegraÃ§Ã£o com mapa (preparado)

## ðŸ”® PrÃ³ximos Passos

### Backend Integration
- [x] APIs REST (Laravel)
- [x] Banco de dados MySQL
- [x] Autenticação via Sanctum
- [ ] Upload de arquivos

### Funcionalidades AvanÃ§adas
- [ ] RelatÃ³rios avanÃ§ados
- [ ] NotificaÃ§Ãµes push
- [ ] IntegraÃ§Ã£o com mapas
- [ ] Sistema de backup

### Mobile App
- [ ] Aplicativo React Native
- [ ] SincronizaÃ§Ã£o offline
- [ ] NotificaÃ§Ãµes mÃ³veis

## ðŸ“ž Suporte

Para dÃºvidas, sugestÃµes ou problemas:
- **E-mail**: suporte@nexusflow.com
- **DocumentaÃ§Ã£o**: Consulte a pasta `docs/`
- **Issues**: Reporte problemas no repositÃ³rio

## ðŸ“„ LicenÃ§a

Este projeto estÃ¡ sob a licenÃ§a MIT. Veja o arquivo `LICENSE` para mais detalhes.

---

**NexusFlow** - Conectando empresas, simplificando gestÃ£o.

*Desenvolvido com â¤ï¸ para facilitar a administraÃ§Ã£o de mÃºltiplas empresas.*

