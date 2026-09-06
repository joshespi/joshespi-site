# Regenerates src/public/og.png - the 1200x630 Open Graph card used for
# link previews. Windows-only (GDI+ via System.Drawing); run it from anywhere:
#   pwsh ./scripts/generate-og-image.ps1
# Only the rendered PNG ships; the fonts stay on the machine that ran this.
# Kept pure ASCII so it behaves identically under pwsh 7 and Windows PowerShell
# 5.1, which disagree about the encoding of a BOM-less UTF-8 file.

Add-Type -AssemblyName System.Drawing

$W = 1200; $H = 630
$bmp = New-Object System.Drawing.Bitmap($W, $H)
$g = [System.Drawing.Graphics]::FromImage($bmp)
$g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
$g.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::AntiAliasGridFit

# Site palette
$ink      = [System.Drawing.ColorTranslator]::FromHtml("#111111")
$brand    = [System.Drawing.ColorTranslator]::FromHtml("#B52929")
$white    = [System.Drawing.Color]::White
$muted    = [System.Drawing.ColorTranslator]::FromHtml("#9CA3AF")
$hairline = [System.Drawing.ColorTranslator]::FromHtml("#2A2A2A")

$g.Clear($ink)

# Brand bar down the left edge
$g.FillRectangle((New-Object System.Drawing.SolidBrush($brand)), 0, 0, 14, $H)

$PAD = 88

# Eyebrow, drawn glyph-by-glyph to mimic the site's tracking-widest
$monoFont = New-Object System.Drawing.Font("Consolas", 21, [System.Drawing.FontStyle]::Bold)
$brandBrush = New-Object System.Drawing.SolidBrush($brand)
$fmt = [System.Drawing.StringFormat]::GenericTypographic
$TRACK = 4.5   # matches the site's tracking-widest eyebrows
$x = [float]$PAD
$eyebrow = "// AVAILABLE FOR HIRE"
foreach ($ch in $eyebrow.ToCharArray()) {
    if ($ch -eq " ") {
        # GenericTypographic measures a bare space as zero width
        $x += $g.MeasureString("M", $monoFont, [System.Drawing.PointF]::Empty, $fmt).Width + $TRACK
        continue
    }
    $g.DrawString($ch, $monoFont, $brandBrush, (New-Object System.Drawing.PointF($x, 84)), $fmt)
    $x += $g.MeasureString($ch, $monoFont, [System.Drawing.PointF]::Empty, $fmt).Width + $TRACK
}

# Headline
$h1 = New-Object System.Drawing.Font("Segoe UI Black", 60, [System.Drawing.FontStyle]::Bold)
$whiteBrush = New-Object System.Drawing.SolidBrush($white)
$g.DrawString("Fixed-price WordPress,",  $h1, $whiteBrush, ($PAD - 6), 146)
$g.DrawString("Laravel & DevOps work.",  $h1, $whiteBrush, ($PAD - 6), 232)

# Subhead
$body = New-Object System.Drawing.Font("Segoe UI", 25)
$mutedBrush = New-Object System.Drawing.SolidBrush($muted)
$g.DrawString("From a 15-year engineer. No calls required.", $body, $mutedBrush, ($PAD - 3), 344)
$g.DrawString("Written quote within 1 business day.",        $body, $mutedBrush, ($PAD - 3), 384)

# Hairline above the footer row
$g.DrawLine((New-Object System.Drawing.Pen($hairline, 2)), $PAD, 482, ($W - $PAD), 482)

# Footer: name left, domain right
$nameFont = New-Object System.Drawing.Font("Segoe UI", 26, [System.Drawing.FontStyle]::Bold)
$g.DrawString("Josh Espinoza", $nameFont, $whiteBrush, ($PAD - 3), 520)

$roleFont = New-Object System.Drawing.Font("Segoe UI", 21)
$g.DrawString("Full Stack Engineer", $roleFont, $mutedBrush, ($PAD - 1), 560)

$domainFont = New-Object System.Drawing.Font("Consolas", 24, [System.Drawing.FontStyle]::Bold)
$domain = "joshespi.com"
$dw = $g.MeasureString($domain, $domainFont).Width
$g.DrawString($domain, $domainFont, $brandBrush, ($W - $PAD - $dw), 528)

# Resolved from the script's own location, so this runs on any checkout rather
# than only the machine it was first written on.
$out = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..\src\public\og.png'))
$outDir = Split-Path $out -Parent
if (-not (Test-Path $outDir)) { New-Item -ItemType Directory -Path $outDir -Force | Out-Null }

$bmp.Save($out, [System.Drawing.Imaging.ImageFormat]::Png)
$g.Dispose(); $bmp.Dispose()
Write-Output "wrote $out"
