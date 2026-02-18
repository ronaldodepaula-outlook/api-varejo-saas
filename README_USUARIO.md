# API - Usuários

Documentação técnica para uso do `UsuarioController` e suas rotas protegidas por Sanctum.

**Base URLs**

```
API_BASE: {BASE_URL}/api
```

## Autenticação

Todas as requisições exigem os headers:

```
Authorization: Bearer {seu_token}
Content-Type: application/json
Accept: application/json
```

## Endpoints

### Listar todos os usuários
`GET {API_BASE}/usuarios`

Retorna lista de usuários com relação `empresa` carregada.

### Listar usuários de uma empresa
`GET {API_BASE}/usuarios/empresa/{id_empresa}`

### Consultar usuário por ID
`GET {API_BASE}/usuarios/{id}`

### Criar novo usuário
`POST {API_BASE}/usuarios`

**Body (JSON)**
```json
{
  "id_empresa": 1,
  "nome": "João Silva",
  "email": "joao@exemplo.com",
  "senha": "123456",
  "perfil": "usuario",
  "ativo": true,
  "aceitou_termos": true,
  "newsletter": false
}
```

**Validações aplicadas**
- `id_empresa` obrigatório e existente em `tb_empresas`
- `nome` obrigatório
- `email` obrigatório e único
- `senha` obrigatória (mínimo 6)
- `perfil` obrigatório
- `ativo`, `aceitou_termos`, `newsletter` são booleanos

A senha é armazenada com hash.

### Atualizar usuário
`PUT/PATCH {API_BASE}/usuarios/{id}`

**Body (JSON)**
```json
{
  "nome": "João S. Silva",
  "email": "joao.silva@exemplo.com",
  "senha": "NovaSenha123",
  "ativo": false
}
```

**Validações aplicadas**
- Campos são opcionais
- `email` precisa ser único (exceto o próprio usuário)
- `senha` mínima 6 caracteres

### Deletar usuário
`DELETE {API_BASE}/usuarios/{id}`

**Resposta**
- 204 (No Content) em caso de sucesso

---

## Observações

- Todas as rotas exigem autenticação via Sanctum.
- Em caso de erro, o backend responde em JSON com status apropriado.
