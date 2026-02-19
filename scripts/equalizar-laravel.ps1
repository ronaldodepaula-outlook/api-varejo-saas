param(
    [string]$EnvPath = "",
    [switch]$NoCache,
    [switch]$ShowAll
)

$scriptRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$laravelRoot = Split-Path -Parent $scriptRoot
if (-not $EnvPath -or $EnvPath -eq "") {
    $EnvPath = Join-Path $laravelRoot ".env"
}

if (-not (Test-Path $EnvPath)) {
    Write-Error "Arquivo .env nao encontrado em $EnvPath"
    exit 1
}

function Parse-EnvFile {
    param([string]$Path)
    $map = @{}
    Get-Content -Path $Path -Encoding UTF8 | ForEach-Object {
        $line = $_.Trim()
        if ($line -eq "" -or $line.StartsWith("#")) { return }
        if ($line.StartsWith("export ")) { $line = $line.Substring(7) }
        $parts = $line.Split("=", 2)
        if ($parts.Count -ne 2) { return }
        $key = $parts[0].Trim()
        $value = $parts[1].Trim()
        if (($value.StartsWith('"') -and $value.EndsWith('"')) -or ($value.StartsWith("'") -and $value.EndsWith("'"))) {
            $value = $value.Substring(1, $value.Length - 2)
        }
        if ($key -ne "") {
            $map[$key] = $value
        }
    }
    return $map
}

function Set-ProcessEnv {
    param([hashtable]$Map, [string[]]$Keys)
    foreach ($k in $Keys) {
        if ($Map.ContainsKey($k)) {
            $env:$k = $Map[$k]
        }
    }
}

$envMap = Parse-EnvFile -Path $EnvPath

$keys = @(
    'APP_NAME','APP_ENV','APP_URL','WEB_URL',
    'MAIL_MAILER','MAIL_HOST','MAIL_HOST_IP','MAIL_PORT','MAIL_USERNAME','MAIL_PASSWORD','MAIL_ENCRYPTION','MAIL_TIMEOUT','MAIL_TIMEOUT_TOTAL','MAIL_MAX_ATTEMPTS','MAIL_DEBUG'
)

Set-ProcessEnv -Map $envMap -Keys $keys

Write-Host "=== Laravel Equalizar ==="
Write-Host "Laravel root: $laravelRoot"
Write-Host "Env file:     $EnvPath"

$mask = if ($envMap.ContainsKey('MAIL_PASSWORD')) { ('*' * 8) } else { '' }
Write-Host "MAIL_HOST:       $($envMap['MAIL_HOST'])"
Write-Host "MAIL_HOST_IP:    $($envMap['MAIL_HOST_IP'])"
Write-Host "MAIL_PORT:       $($envMap['MAIL_PORT'])"
Write-Host "MAIL_ENCRYPTION: $($envMap['MAIL_ENCRYPTION'])"
Write-Host "MAIL_USERNAME:   $($envMap['MAIL_USERNAME'])"
Write-Host "MAIL_PASSWORD:   $mask"

if ($ShowAll) {
    Write-Host "--- .env completo ---"
    $envMap.GetEnumerator() | Sort-Object Name | ForEach-Object {
        $val = $_.Value
        if ($_.Name -match 'PASSWORD|SECRET|KEY') { $val = '***' }
        Write-Host ("{0}={1}" -f $_.Name, $val)
    }
}

Push-Location $laravelRoot

Write-Host "Limpando caches..."
php artisan config:clear | Out-Host
php artisan cache:clear | Out-Host
php artisan route:clear | Out-Host
php artisan view:clear | Out-Host
php artisan optimize:clear | Out-Host

if (-not $NoCache) {
    Write-Host "Gerando config cache..."
    php artisan config:cache | Out-Host
}

Pop-Location

Write-Host "Equalizacao concluida."
