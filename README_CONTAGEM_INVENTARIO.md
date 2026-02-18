# Documentação Técnica - Contagem de Inventário

Este documento descreve as rotas, parâmetros e exemplos para `ContagemInventarioController`.

**Base URLs**

```
API_BASE: {BASE_URL}/api
```

**Autenticação**
Atualmente as rotas de contagem não estão protegidas por `auth:sanctum` no `routes/api.php`.

---

## Endpoints

### 1. Listar contagens
`GET {API_BASE}/contagens`

**Filtros opcionais**
- `id_empresa`
- `id_filial`
- `id_inventario`
- `id_produto`
- `id_usuario`
- `tipo_operacao` (`Adicionar`, `Substituir`, `Excluir`)
- `data_inicial` e `data_final` (filtra `data_contagem` entre as datas)

Retorna contagens com relações `empresa`, `filial`, `produto`, `usuario`, `inventario`.

### 2. Criar contagem
`POST {API_BASE}/contagens`

**Body (JSON)**
```json
{
  "id_inventario": 3,
  "id_empresa": 1,
  "id_filial": 3,
  "id_produto": 6,
  "tipo_operacao": "Adicionar",
  "quantidade": 10.0,
  "observacao": "Ajuste manual",
  "id_usuario": 1
}
```

**Validações aplicadas**
- `id_inventario`, `id_empresa`, `id_filial`, `id_produto`, `id_usuario` obrigatórios e existentes
- `tipo_operacao` obrigatório e um de `Adicionar`, `Substituir`, `Excluir`
- `quantidade` obrigatório, numérico, mínimo 0
- `observacao` opcional, máximo 255

**Resposta**
- 201 com o objeto criado

### 3. Visualizar contagem
`GET {API_BASE}/contagens/{id}`

Retorna contagem com relações.

### 4. Atualizar contagem
`PUT {API_BASE}/contagens/{id}`

Campos opcionais com as mesmas validações quando presentes.

### 5. Excluir contagem
`DELETE {API_BASE}/contagens/{id}`

**Resposta**
- 200 com `{ "message": "Contagem excluída com sucesso" }`

### 6. Listar contagens por inventário
`GET {API_BASE}/contagens/inventario/{id_inventario}`

**Filtros opcionais**
- `tipo_operacao`
- `id_filial`

Retorna contagens com relações `produto` e `usuario`.

### 7. Listar produtos contados por inventário (agregado)
`GET {API_BASE}/contagens/inventario/{id_inventario}/produtos`

**Filtro opcional**
- `id_filial`

Retorna agregação por produto com `quantidade_total`, `ultima_contagem` e `total_registros`.

---

## Erros e códigos de resposta

- 200: sucesso em leitura/atualização/remoção
- 201: recurso criado
- 404: recurso não encontrado
- 422: erro de validação
- 500: erro interno (logado)
