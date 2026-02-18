# API - Filiais

Documentação técnica para uso do `FilialController`.

**Base URLs**

```
API_BASE: {BASE_URL}/api
API_V1: {BASE_URL}/api/v1
```

## Autenticação

- As rotas em `{API_V1}` estão protegidas por Sanctum.
- As rotas em `{API_BASE}` não possuem middleware de autenticação.

## Endpoints

### Listar filiais
`GET {API_BASE}/filiais`
`GET {API_V1}/filiais`

### Listar filiais por empresa
`GET {API_BASE}/filiais/empresa/{id_empresa}`

### Consultar filial por ID
`GET {API_BASE}/filiais/{id}`
`GET {API_V1}/filiais/{id}`

### Criar nova filial
`POST {API_BASE}/filiais`
`POST {API_V1}/filiais`

**Body (JSON)**
```json
{
  "id_empresa": 1,
  "nome_filial": "Filial Centro",
  "endereco": "Rua Exemplo, 123",
  "cidade": "São Paulo",
  "estado": "SP",
  "cep": "01000-000",
  "data_cadastro": "2025-10-05 12:00:00"
}
```

**Validações aplicadas**
- `id_empresa` obrigatório e existente em `tb_empresas`
- `nome_filial` obrigatório, máximo 150
- `endereco`, `cidade`, `estado`, `cep` são opcionais
- `data_cadastro` opcional, formato `Y-m-d H:i:s`

Se `data_cadastro` não for enviado, o backend usa a data/hora atual.

### Atualizar filial
`PUT/PATCH {API_BASE}/filiais/{id}`
`PUT/PATCH {API_V1}/filiais/{id}`

**Body (JSON)**
```json
{
  "nome_filial": "Filial Atualizada",
  "cidade": "Campinas"
}
```

### Deletar filial
`DELETE {API_BASE}/filiais/{id}`
`DELETE {API_V1}/filiais/{id}`

**Resposta**
- 204 (No Content) em caso de sucesso

---

## Observações

- Em caso de erro, o backend responde em JSON com status apropriado.
- Recomenda-se usar a versão `{API_V1}` com autenticação.
