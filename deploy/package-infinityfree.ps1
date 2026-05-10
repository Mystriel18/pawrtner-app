param(
    [string]$OutputZip = 'pawrtner-deploy.zip'
)

$ErrorActionPreference = 'Stop'

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$stagePath = Join-Path $projectRoot '.deploy-stage'
$zipPath = Join-Path $projectRoot $OutputZip
$ignoreFile = Join-Path $projectRoot '.deployignore'

if (-not (Test-Path $ignoreFile)) {
    throw 'Missing .deployignore file in project root.'
}

$excludePatterns = Get-Content $ignoreFile |
    Where-Object { $_ -and -not $_.Trim().StartsWith('#') } |
    ForEach-Object { ($_.Trim() -replace '[\\/]+$', '') } |
    Where-Object { $_ -ne '' }

if (Test-Path $stagePath) {
    Remove-Item $stagePath -Recurse -Force
}

New-Item -ItemType Directory -Path $stagePath | Out-Null

$robocopyArgs = @(
    $projectRoot,
    $stagePath,
    '/MIR',
    '/R:1',
    '/W:1',
    '/NFL',
    '/NDL',
    '/NJH',
    '/NJS',
    '/NP',
    '/XD'
) + $excludePatterns + @('/XF') + $excludePatterns

& robocopy @robocopyArgs | Out-Null

if ($LASTEXITCODE -ge 8) {
    throw "robocopy failed with exit code $LASTEXITCODE"
}

if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

Push-Location $stagePath
& tar -a -c -f $zipPath .
$tarExitCode = $LASTEXITCODE
Pop-Location

if ($tarExitCode -ne 0) {
    throw "tar failed with exit code $tarExitCode"
}

Remove-Item $stagePath -Recurse -Force

Write-Host "Created deployment archive: $zipPath"
