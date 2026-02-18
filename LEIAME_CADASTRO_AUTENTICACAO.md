# Documentação Técnica - Cadastro, Validação e Autenticação

**Base URLs**

```
BASE_URL: https://seu-dominio.com
API_BASE: {BASE_URL}/api
```

## 1. Cadastro de Empresa e Usuário

**Endpoint**
`POST {API_BASE}/registrar`

**Request (JSON)**
```json
{
  "empresa": {
    "nome_empresa": "GRUPO MSLZ",
    "cnpj": "03720882000306",
    "email_empresa": "contato@grupomslz.com.br",
    "telefone": "11999999999",
    "website": "https://grupomslz.com.br",
    "endereco": "Rua Exemplo, 123",
    "cep": "01001-000",
    "cidade": "São Paulo",
    "estado": "SP",
    "segmento": "varejo"
  },
  "usuario": {
    "nome": "Ronaldo de Paula",
    "email": "ronaldo@exemplo.com",
    "senha": "123456",
    "aceitou_termos": true,
    "newsletter": true
  }
}
```

**Campos obrigatórios**
- `empresa.nome_empresa`
- `usuario.nome`
- `usuario.email`
- `usuario.senha` (mínimo 6 caracteres)

**Campos opcionais (empresa)**
- `cnpj`, `email_empresa`, `telefone`, `website`, `endereco`, `cep`, `cidade`, `estado`, `segmento`

**Valores aceitos para `empresa.segmento`**
```
varejo, ecommerce, alimentacao, turismo_hotelaria, imobiliario, esportes_lazer,
midia_entretenimento, industria, construcao, agropecuaria, energia_utilities,
logistica_transporte, financeiro, contabilidade_auditoria, seguros, marketing,
saude, educacao, ciencia_pesquisa, rh_recrutamento, juridico, ongs_terceiro_setor,
seguranca, outros
```

**Comportamento do backend**
- Cria `empresa` com `status` = `pendente`.
- Cria `filial_matriz` automaticamente com nome `MATRIZ - {nome_empresa}`.
- Cria `usuario` com `perfil` = `admin_empresa` e `ativo` = `0`.
- Cria `licenca` `trial` com duração de 3 meses.
- Gera token de verificação de e-mail.

**Resposta de sucesso (201)**
```json
{
  "empresa": { "id_empresa": 1, "status": "pendente", "segmento": "varejo" },
  "usuario": { "id_usuario": 10, "perfil": "admin_empresa", "ativo": 0 },
  "licenca": { "plano": "trial", "status": "ativa" },
  "filial_matriz": { "id_filial": 1, "nome_filial": "MATRIZ - GRUPO MSLZ" },
  "message": "Empresa, usuário, licença trial e filial matriz cadastrados. Verifique o e-mail para ativar a conta."
}
```

**Erros (409)**
- `Usuário e empresa já cadastrados`
- `Empresa com o CNPJ já cadastrada`
- `Usuário já cadastrado com o email informado`

---

## 2. E-mail de Validação

O e-mail enviado contém o link:

```
{BASE_URL}/valida-email.php?token=SEU_TOKEN_AQUI
```

A página `valida-email.php` dispara a validação via API.

---

## 3. Validação de E-mail

**Endpoint**
`GET {API_BASE}/verificar-email/{token}`

**Resposta de sucesso**
```json
{
  "message": "Conta validada com sucesso!"
}
```

**Erros**
```json
{
  "message": "Token inválido ou expirado."
}
```

---

## 4. Autenticação (Login)

**Endpoint**
`POST {API_BASE}/login`

**Request (JSON)**
```json
{
  "email": "ronaldo@exemplo.com",
  "senha": "123456"
}
```

**Resposta de sucesso**
```json
{
  "usuario": {
    "id_usuario": 10,
    "nome": "Ronaldo de Paula",
    "email": "ronaldo@exemplo.com",
    "perfil": "admin_empresa"
  },
  "empresa": { "id_empresa": 1, "nome_empresa": "GRUPO MSLZ" },
  "licenca": { "plano": "trial", "status": "ativa" },
  "token": "TOKEN_SANCTUM",
  "message": "Login realizado com sucesso."
}
```

**Erros**
- 401: Credenciais inválidas
- 403: Usuário inativo ou e-mail não verificado

---

## 5. Fluxo Resumido

1. Envie os dados para `POST {API_BASE}/registrar`.
2. O usuário recebe o e-mail e clica no link de validação.
3. A página `valida-email.php` chama `GET {API_BASE}/verificar-email/{token}`.
4. Faça login via `POST {API_BASE}/login`.
5. Use o token retornado para acessar rotas protegidas.

---

**Observações**
- Todos os endpoints retornam JSON.
- As rotas protegidas exigem `Authorization: Bearer {token}`.
