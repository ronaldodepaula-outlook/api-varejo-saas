# Documentação Técnica - Capas de Inventário e Inventários

Este documento descreve as rotas, parâmetros e regras reais do backend para `capa-inventarios` e `inventarios`.

**Base URLs**

```
API_BASE: {BASE_URL}/api
API_V1: {BASE_URL}/api/v1
```

**Autenticação**
Atualmente as rotas de inventário não estão protegidas por `auth:sanctum` no `routes/api.php`.

---

## Capa de Inventário (`capa-inventarios`)

**Rota base**: `{API_BASE}/capa-inventarios`

### Listar todas as capas
`GET {API_BASE}/capa-inventarios`

Retorna lista com relações `empresa`, `filial`, `usuario` e `inventarios`, ordenada por `data_inicio` desc.

### Criar nova capa
`POST {API_BASE}/capa-inventarios`

**Body (JSON)**
```json
{
  "id_empresa": 1,
  "id_filial": 3,
  "descricao": "Inventário Mensal - Outubro 2025",
  "data_inicio": "2025-10-01",
  "status": "em_andamento",
  "observacao": "...",
  "id_usuario": 1
}
```

**Validações aplicadas**
- `id_empresa` obrigatório e existente em `tb_empresas`
- `id_filial` obrigatório e existente em `tb_filiais`
- `descricao` obrigatório, máximo 150
- `data_inicio` obrigatório, formato date
- `status` obrigatório e um de `em_andamento`, `concluido`, `cancelado`
- `observacao` opcional
- `id_usuario` obrigatório e existente em `tb_usuarios`

**Resposta**
- 201 com o objeto criado (sem relações carregadas)

### Visualizar uma capa
`GET {API_BASE}/capa-inventarios/{id}`

Retorna a capa com relações `empresa`, `filial`, `usuario`, `inventarios`.

### Atualizar uma capa
`PUT/PATCH {API_BASE}/capa-inventarios/{id}`

Campos são opcionais, com as mesmas regras de validação quando presentes.

`data_fechamento` é aceito como `nullable|date`.

### Deletar uma capa
`DELETE {API_BASE}/capa-inventarios/{id}`

**Resposta**
- 200 com `{ "message": "Inventário excluído com sucesso" }`

### Listar capas por empresa
`GET {API_V1}/capas-inventario/empresa/{id_empresa}`

Retorna as capas da empresa com relações `filial`, `usuario`, `inventarios`.

---

## Inventário (`inventarios`)

**Rota base**: `{API_BASE}/inventarios`

### Listar todos os inventários
`GET {API_BASE}/inventarios`

Retorna itens com relações `capaInventario`, `empresa`, `filial`, `produto`, `usuario`.

### Criar item de inventário
`POST {API_BASE}/inventarios`

**Body (JSON)**
```json
{
  "id_capa_inventario": 3,
  "id_empresa": 1,
  "id_filial": 3,
  "id_produto": 6,
  "quantidade_fisica": 95.0,
  "quantidade_sistema": 20.0,
  "motivo": "Quebra durante o manuseio",
  "data_inventario": "2025-10-01T10:00:00Z",
  "id_usuario": 1
}
```

**Validações aplicadas**
- `id_capa_inventario`, `id_empresa`, `id_filial`, `id_produto`, `id_usuario` obrigatórios e existentes
- `quantidade_fisica`, `quantidade_sistema` obrigatórios e numéricos
- `motivo` opcional, máximo 255
- `data_inventario` é aceito, mas não é obrigatório na validação

**Resposta**
- 201 com o objeto criado (sem relações carregadas)

### Visualizar item de inventário
`GET {API_BASE}/inventarios/{id}`

Retorna item com relações `capaInventario`, `empresa`, `filial`, `produto`, `usuario`.

### Atualizar item de inventário
`PUT/PATCH {API_BASE}/inventarios/{id}`

Campos opcionais, com as mesmas validações quando presentes.

### Deletar item de inventário
`DELETE {API_BASE}/inventarios/{id}`

**Resposta**
- 204 (No Content)

### Listar itens por capa de inventário
`GET {API_BASE}/inventarios/capa/{id_capa_inventario}`
`GET {API_V1}/inventarios/capa/{id_capa_inventario}`

Retorna itens com relações `produto`, `usuario`, `filial`.

---

## Erros e códigos de resposta

- 200: sucesso em leitura/atualização
- 201: recurso criado
- 204: recurso removido
- 401: não autorizado (quando rota exigir autenticação)
- 404: recurso não encontrado
- 422: erro de validação
