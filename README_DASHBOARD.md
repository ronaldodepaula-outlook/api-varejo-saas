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
