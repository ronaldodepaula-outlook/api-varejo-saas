# API - Dashboard SAS Multi

Documentação técnica para integração do frontend com o backend via endpoint de dashboard.

**Base URLs**

```
API_V1: {BASE_URL}/api/v1
```

## Autenticação

Requer header:

```
Authorization: Bearer {seu_token}
Accept: application/json
```

## Endpoint

### Resumo do Dashboard
`GET {API_V1}/dashboard/resumo`

Retorna dados agregados do sistema.

**Observação importante**
- `disponibilidade`, `tentativas_login_suspeitas` e `backup_status` são valores simulados no backend atual.

**Exemplo de resposta**
```json
{
  "empresas": {
    "total": 29,
    "pendentes": 8,
    "por_segmento": [
      {"segmento": "varejo", "total": 12},
      {"segmento": "industria", "total": 4}
    ]
  },
  "usuarios": {
    "total": 25,
    "ativos": 15,
    "inativos": 5,
    "admin_empresa": 5,
    "super_admin": 1,
    "newsletter": 9
  },
  "licencas": {
    "ativas": 17,
    "expiradas": 0,
    "canceladas": 0,
    "por_plano": [
      {"plano": "trial", "total": 17}
    ],
    "proximas_expiracao": 5
  },
  "assinaturas": {
    "total": 20,
    "ativas": 18
  },
  "pagamentos": {
    "pendentes": 3,
    "receita_mensal": 4250
  },
  "disponibilidade": 98.7,
  "alertas": {
    "licencas_expirando": 5,
    "empresas_pendentes": 8,
    "pagamentos_pendentes": 3,
    "tentativas_login_suspeitas": 3,
    "backup_status": "executado"
  }
}
```

### Indicadores (Dashboard Operacional)
`GET {API_V1}/dashboard/indicadores`

Retorna indicadores consolidados de vendas, compras, estoque, movimentações, financeiro, precificação e cadastros.

**Filtros**
- `filtro`: `diario`, `semanal`, `mensal`, `anual`, `personalizado` (default `mensal`)
- `data_inicio` e `data_fim`: obrigatórios apenas quando `filtro=personalizado`
- `id_empresa`: obrigatório quando não há usuário autenticado
- `id_filial`: opcional

**Exemplo de chamada**
```
GET {API_V1}/dashboard/indicadores?id_empresa=7&id_filial=12&filtro=mensal
```

### Dashboard Executivo (Varejo)
Prefixo: `GET {API_V1}/dashboard/exec/*`

Endpoints:
- `GET /dashboard/exec/metricas`
- `GET /dashboard/exec/vendas-periodo`
- `GET /dashboard/exec/vendas-por-filial`
- `GET /dashboard/exec/formas-pagamento`
- `GET /dashboard/exec/contas-receber-resumo`
- `GET /dashboard/exec/contas-pagar-resumo`
- `GET /dashboard/exec/fluxo-caixa`
- `GET /dashboard/exec/top-produtos`
- `GET /dashboard/exec/estoque-critico`
- `GET /dashboard/exec/pedidos-pendentes`
- `GET /dashboard/exec/entradas-recentes`
- `GET /dashboard/exec/promocoes-ativas`
- `GET /dashboard/exec/produtos-promocao`
- `GET /dashboard/exec/historico-precos`
- `GET /dashboard/exec/top-clientes`
- `GET /dashboard/exec/vendas-assistidas-status`
- `GET /dashboard/exec/debitos-clientes`
- `GET /dashboard/exec/movimentacoes-recentes`
- `GET /dashboard/exec/movimentacoes-resumo`

Filtros aceitos: `id_empresa`, `id_filial`, `filtro`, `data_inicio`, `data_fim`.
Alguns endpoints aceitam `limit` e `fluxo-caixa` aceita `meses`.
