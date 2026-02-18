# SaaS MultiEmpresas - Backend API

Este repositório contém o backend Laravel do SaaS MultiEmpresas e templates de frontend em `public/app` e `public/appV1`.

**Base URLs**
Use estas variáveis nos exemplos da documentação.

```
BASE_URL: https://seu-dominio.com
API_BASE: {BASE_URL}/api
API_V1: {BASE_URL}/api/v1
```

Se o projeto não estiver na raiz do domínio, inclua o subpath na `BASE_URL`.

**Autenticação**

```
POST {API_BASE}/login
Authorization: Bearer {token}
```

**Documentação do sistema**
- `LEIAME_CADASTRO_AUTENTICACAO.md` Cadastro, verificação de e-mail e login.
- `LEIAME_RECUPERACAO_SENHA.md` Fluxo de recuperação de senha.
- `README_USUARIO.md` CRUD de usuários.
- `README_EMPRESA.md` CRUD de empresas.
- `README_FILIAL.md` CRUD de filiais.
- `README_INVENTARIO.md` Capas de inventário e inventários.
- `README_CONTAGEM_INVENTARIO.md` Contagem de inventário.
- `README_DASHBOARD.md` Resumo do dashboard.
- `public/app/README.md` Template frontend e integração.
- `public/appV1/README.md` Template frontend e integração.
