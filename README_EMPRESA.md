# API - Empresas

Documentação técnica para uso do `EmpresaController`.

**Base URLs**

```
API_BASE: {BASE_URL}/api
API_V1: {BASE_URL}/api/v1
```

## Autenticação

- As rotas em `{API_V1}` estão protegidas por Sanctum.
- As rotas em `{API_BASE}` não possuem middleware de autenticação, mas retornam dados do mesmo controller.

## Endpoints principais (mesmo comportamento)

### Listar empresas
`GET {API_BASE}/empresas`
`GET {API_V1}/empresas`

### Consultar empresa por ID
`GET {API_BASE}/empresas/{id}`
`GET {API_V1}/empresas/{id}`

### Criar nova empresa
`POST {API_BASE}/empresas`
`POST {API_V1}/empresas`

### Atualizar empresa
`PUT/PATCH {API_BASE}/empresas/{id}`
`PUT/PATCH {API_V1}/empresas/{id}`

### Deletar empresa
`DELETE {API_BASE}/empresas/{id}`
`DELETE {API_V1}/empresas/{id}`

**Campos aceitos pelo backend**
- `nome_empresa`
- `cnpj`
- `email_empresa`
- `telefone`
- `website`
- `endereco`
- `cep`
- `cidade`
- `estado`
- `segmento`
- `status`

O controller não aplica validação de payload neste CRUD. As regras efetivas podem depender do banco de dados.

---

## Endpoints adicionais

### Empresa do usuário autenticado
`GET {API_BASE}/empresas/usuario/{id_usuario}`

Retorna `{usuario, empresa}` e exige token válido, pois usa o usuário autenticado (`Auth::user`).

### Empresa por ID de usuário
`GET {API_BASE}/empresas/por-usuario/{id_usuario}`

Retorna `{usuario, empresa}` para o usuário informado.

---

## Observações

- Em caso de erro, o backend responde em JSON com status apropriado.
- Recomenda-se usar a versão `{API_V1}` com autenticação.
