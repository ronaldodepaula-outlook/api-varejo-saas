<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FornecedorController extends Controller
{
    private function resolveEmpresaId(Request $request): int
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        if (!$empresaId) {
            throw ValidationException::withMessages([
                'id_empresa' => ['id_empresa e obrigatorio quando nao ha usuario autenticado.'],
            ]);
        }
        return (int) $empresaId;
    }

    // Listagem paginada, respeitando id_empresa do auth ou query
    public function index(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $fornecedores = Fornecedor::daEmpresa($empresaId)
            ->orderBy('razao_social')
            ->paginate(20);

        return response()->json($fornecedores);
    }

    // Listar todos sem paginação
    public function todosPorEmpresa(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $fornecedores = Fornecedor::daEmpresa($empresaId)
            ->orderBy('razao_social')
            ->get();

        return response()->json($fornecedores);
    }

    // Rota similar a outras: fornecedoresPorEmpresa($id_empresa)
    public function fornecedoresPorEmpresa($id_empresa)
    {
        $fornecedores = Fornecedor::daEmpresa($id_empresa)
            ->orderBy('razao_social')
            ->get();

        return response()->json($fornecedores);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'required|integer|exists:tb_empresas,id_empresa',
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:30',
            'inscricao_estadual' => 'nullable|string|max:50',
            'contato' => 'nullable|string|max:100',
            'telefone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:10',
            'cep' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:50',
        ]);

        $fornecedor = Fornecedor::create($data);

        return response()->json(['success' => true, 'data' => $fornecedor], 201);
    }

    public function importar(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'nullable|integer|exists:tb_empresas,id_empresa',
            'arquivo' => 'required|file|mimes:xls,xlsx,csv,txt',
            'modo' => 'nullable|in:upsert,skip,update',
            'delimiter' => 'nullable|string|max:2',
        ]);

        $empresaId = $this->resolveEmpresaId($request);
        $modo = $data['modo'] ?? 'upsert';

        $file = $request->file('arquivo');
        $ext = strtolower($file->getClientOriginalExtension());
        $rows = $this->parseFile($file->getRealPath(), $ext, $data['delimiter'] ?? null);

        if (!$rows) {
            return response()->json([
                'success' => false,
                'message' => 'Arquivo vazio ou formato nao suportado.',
            ], 422);
        }

        $headerRow = $rows[0] ?? [];
        $headerMap = $this->buildHeaderMap($headerRow);
        $hasHeader = $headerMap['has_header'];
        $map = $headerMap['map'];

        if (!$hasHeader) {
            $map = [
                'razao_social' => 0,
                'cnpj' => 1,
                'nome_fantasia' => 2,
                'cidade' => 3,
                'estado' => 4,
            ];
        }

        $startIndex = $hasHeader ? 1 : 0;

        $resumo = [
            'total_linhas' => 0,
            'importados' => 0,
            'atualizados' => 0,
            'ignorados' => 0,
            'erros' => 0,
        ];
        $erros = [];
        $detalhes = [];
        $detalhar = $request->boolean('detalhado', true);

        DB::beginTransaction();
        try {
            for ($i = $startIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                $resumo['total_linhas']++;

                try {
                    $razao = $this->getCell($row, $map['razao_social'] ?? null);
                    if (!$razao) {
                        $resumo['ignorados']++;
                        if ($detalhar) {
                            $detalhes[] = [
                                'linha' => $i + 1,
                                'status' => 'ignorado',
                                'motivo' => 'Razao social vazia',
                            ];
                        }
                        continue;
                    }

                    $cnpj = $this->sanitizeCnpj($this->getCell($row, $map['cnpj'] ?? null));
                    $nomeFantasia = $this->getCell($row, $map['nome_fantasia'] ?? null);
                    $cidade = $this->getCell($row, $map['cidade'] ?? null);
                    $estado = $this->getCell($row, $map['estado'] ?? null);
                    if ($estado) {
                        $estado = strtoupper($estado);
                    }

                    $payload = [
                        'id_empresa' => $empresaId,
                        'razao_social' => $razao,
                        'nome_fantasia' => $nomeFantasia,
                        'cnpj' => $cnpj,
                        'cidade' => $cidade,
                        'estado' => $estado,
                        'status' => 'ativo',
                    ];

                    $query = Fornecedor::where('id_empresa', $empresaId);
                    if ($cnpj) {
                        $query->where('cnpj', $cnpj);
                    } else {
                        $query->where('razao_social', $razao);
                    }

                    $existing = $query->first();

                    if ($existing) {
                        if ($modo === 'skip') {
                            $resumo['ignorados']++;
                            if ($detalhar) {
                                $detalhes[] = [
                                    'linha' => $i + 1,
                                    'status' => 'ignorado',
                                    'motivo' => 'Registro ja existe',
                                    'cnpj' => $cnpj,
                                    'razao_social' => $razao,
                                ];
                            }
                            continue;
                        }
                        $existing->update($payload);
                        $resumo['atualizados']++;
                        if ($detalhar) {
                            $detalhes[] = [
                                'linha' => $i + 1,
                                'status' => 'atualizado',
                                'cnpj' => $cnpj,
                                'razao_social' => $razao,
                            ];
                        }
                    } else {
                        if ($modo === 'update') {
                            $resumo['ignorados']++;
                            if ($detalhar) {
                                $detalhes[] = [
                                    'linha' => $i + 1,
                                    'status' => 'ignorado',
                                    'motivo' => 'Registro nao encontrado para atualizar',
                                    'cnpj' => $cnpj,
                                    'razao_social' => $razao,
                                ];
                            }
                            continue;
                        }
                        Fornecedor::create($payload);
                        $resumo['importados']++;
                        if ($detalhar) {
                            $detalhes[] = [
                                'linha' => $i + 1,
                                'status' => 'importado',
                                'cnpj' => $cnpj,
                                'razao_social' => $razao,
                            ];
                        }
                    }
                } catch (\Throwable $rowError) {
                    $resumo['erros']++;
                    $erros[] = ['linha' => $i + 1, 'erro' => $rowError->getMessage()];
                    if ($detalhar) {
                        $detalhes[] = [
                            'linha' => $i + 1,
                            'status' => 'erro',
                            'motivo' => $rowError->getMessage(),
                        ];
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $resumo['erros']++;
            $erros[] = ['linha' => null, 'erro' => $e->getMessage()];
        }

        return response()->json([
            'success' => true,
            'resumo' => $resumo,
            'erros' => $erros,
            'detalhes' => $detalhar ? $detalhes : [],
        ]);
    }

    public function show(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $fornecedor = Fornecedor::daEmpresa($empresaId)->findOrFail($id);

        return response()->json($fornecedor);
    }

    public function update(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $fornecedor = Fornecedor::daEmpresa($empresaId)->findOrFail($id);

        $data = $request->validate([
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:30',
            'inscricao_estadual' => 'nullable|string|max:50',
            'contato' => 'nullable|string|max:100',
            'telefone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:10',
            'cep' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:50',
        ]);

        $fornecedor->update($data);

        return response()->json(['success' => true, 'data' => $fornecedor]);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $fornecedor = Fornecedor::daEmpresa($empresaId)->findOrFail($id);
        $fornecedor->delete();

        return response()->json(['success' => true]);
    }

    private function parseFile(string $path, string $ext, ?string $delimiter = null): array
    {
        if (in_array($ext, ['csv', 'txt'], true)) {
            return $this->readDelimited($path, $delimiter);
        }

        if (in_array($ext, ['xls', 'xlsx'], true)) {
            $sheet = IOFactory::load($path)->getActiveSheet();
            return $sheet->toArray(null, true, true, false);
        }

        return [];
    }

    private function readDelimited(string $path, ?string $delimiter = null): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            return [];
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return [];
        }

        $delimiter = $delimiter ?: $this->detectDelimiter($firstLine);
        rewind($handle);

        $rows = [];
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($data === [null] || $data === false) {
                continue;
            }
            $rows[] = $data;
        }

        fclose($handle);
        return $rows;
    }

    private function detectDelimiter(string $line): string
    {
        $delimiters = [',', ';', "\t", '|'];
        $best = ',';
        $max = 0;
        foreach ($delimiters as $delim) {
            $count = substr_count($line, $delim);
            if ($count > $max) {
                $max = $count;
                $best = $delim;
            }
        }
        return $best;
    }

    private function buildHeaderMap(array $headerRow): array
    {
        $map = [];
        $hasHeader = false;

        foreach ($headerRow as $index => $header) {
            $norm = $this->normalizeHeader($header);
            if (!$norm) {
                continue;
            }

            if (str_contains($norm, 'RAZAO') || str_contains($norm, 'SOCIAL')) {
                $map['razao_social'] = $index;
                $hasHeader = true;
            } elseif (str_contains($norm, 'CNPJ')) {
                $map['cnpj'] = $index;
                $hasHeader = true;
            } elseif (str_contains($norm, 'FANTASIA')) {
                $map['nome_fantasia'] = $index;
                $hasHeader = true;
            } elseif ($norm === 'CIDADE') {
                $map['cidade'] = $index;
                $hasHeader = true;
            } elseif (in_array($norm, ['ESTADO', 'UF'], true)) {
                $map['estado'] = $index;
                $hasHeader = true;
            }
        }

        return [
            'has_header' => $hasHeader,
            'map' => $map,
        ];
    }

    private function normalizeHeader($value): string
    {
        if ($value === null) {
            return '';
        }
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        $value = mb_strtoupper($value, 'UTF-8');
        $trans = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        if ($trans !== false) {
            $value = $trans;
        }
        $value = preg_replace('/[^A-Z0-9]+/', '_', $value);
        return trim($value, '_');
    }

    private function getCell(array $row, ?int $index): ?string
    {
        if ($index === null) {
            return null;
        }
        if (!array_key_exists($index, $row)) {
            return null;
        }
        $value = $row[$index];
        if ($value === null) {
            return null;
        }
        return trim((string) $value);
    }

    private function sanitizeCnpj(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $value);
        if ($digits === '') {
            return null;
        }
        if (strlen($digits) < 14) {
            $digits = str_pad($digits, 14, '0', STR_PAD_LEFT);
        }
        return $digits;
    }
}
