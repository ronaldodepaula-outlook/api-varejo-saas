# Documentação Técnica - Recuperação de Senha

**Base URLs**

```
BASE_URL: https://seu-dominio.com
API_BASE: {BASE_URL}/api
API_V1: {BASE_URL}/api/v1
```

## Fluxo oficial do backend

### 1. Solicitar recuperação de senha

**URL**: `POST {API_V1}/password/solicitar-reset`

**Body (JSON)**
```json
{
  "email": "usuario@exemplo.com"
}
```

**Resposta de sucesso**
```json
{
  "mensagem": "E-mail de redefinição enviado."
}
```

**Erros**
- 404: `E-mail não encontrado.`
- 500: erro ao enviar e-mail

O e-mail enviado contém um link como:
```
{BASE_URL}/resetar-senha.php?token=SEU_TOKEN&email=usuario@exemplo.com
```

Também existe a rota equivalente sem versão:
`POST {API_BASE}/password/solicitar-reset`

---

### 2. Validar token

**URL**: `POST {API_V1}/password/validar-token`

**Body (JSON)**
```json
{
  "email": "usuario@exemplo.com",
  "token": "SEU_TOKEN"
}
```

**Resposta de sucesso**
```json
{
  "mensagem": "Token válido.",
  "email": "usuario@exemplo.com",
  "token": "SEU_TOKEN"
}
```

**Erros**
- 404: `Token inválido ou expirado.`
- 403: `Token expirado. Solicite um novo.`

O token é válido por 60 minutos.

---

### 3. Resetar senha

**URL**: `POST {API_V1}/password/resetar-senha`

**Body (JSON)**
```json
{
  "email": "usuario@exemplo.com",
  "token": "SEU_TOKEN",
  "senha": "NovaSenha123",
  "senha_confirmation": "NovaSenha123"
}
```

**Resposta de sucesso**
```json
{
  "mensagem": "Senha alterada com sucesso!"
}
```

**Erros**
- 404: `Token inválido ou expirado.`
- 403: `Token expirado. Solicite um novo.`
- 404: `Usuário não encontrado.`

---

## Observações

- O fluxo ativo usa `solicitar-reset`, `validar-token` e `resetar-senha`.
- As rotas retornam JSON e seguem validação padrão do Laravel (422 em erro de validação).
