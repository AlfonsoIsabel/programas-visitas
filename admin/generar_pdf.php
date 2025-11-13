<?php
ob_start();
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$form_id = intval($_GET['form_id'] ?? 0);
if ($form_id <= 0) die("Formulario no válido.");

$form = $conexion->query("SELECT * FROM formularios WHERE id = $form_id")->fetch_assoc();
if (!$form) die("Formulario no encontrado.");

$respuestas = $conexion->query("SELECT * FROM respuestas_formularios WHERE form_id = $form_id ORDER BY fecha ASC")->fetch_all(MYSQLI_ASSOC);

$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Programa de Visitas');
$pdf->SetTitle($form['titulo']);
$pdf->SetMargins(15, 25, 15);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Portada
$pdf->AddPage();
$logo1 = __DIR__ . '/../public/img/logo_principal.png';
$logo2 = __DIR__ . '/../public/img/logo_secundario1.png';
$logo3 = __DIR__ . '/../public/img/logo_secundario2.png';
if(file_exists($logo1)) $pdf->Image($logo1, 15, 10, 40);
if(file_exists($logo2)) $pdf->Image($logo2, 140, 10, 25);
if(file_exists($logo3)) $pdf->Image($logo3, 170, 10, 25);

$pdf->Ln(60);
$pdf->SetFont('helvetica', 'B', 22);
$pdf->Cell(0, 10, strtoupper($form['titulo']), 0, 1, 'C');
$pdf->SetFont('helvetica', '', 14);
$pdf->Cell(0, 10, 'Program Schedule', 0, 1, 'C');
$pdf->Cell(0, 10, 'Generated on: ' . date('d/m/Y'), 0, 1, 'C');
$pdf->AddPage();

// Cuerpo: filas de dos columnas
$colIzqWidth = 45;
$colDerWidth = 135;
$alt = true;

foreach ($respuestas as $r) {
    // Cabecera común con logo
    if(file_exists($logo1)) $pdf->Image($logo1, 15, 10, 40);
    $pdf->Ln(20);

    // Día + fecha
    $dia = strtoupper($r['dia_semana']);
    $fecha = date('d M Y', strtotime($r['fecha']));
    $textoIzq = "$dia\n$fecha";

    $colorFondo = $alt ? [200,0,0] : [255,255,255];
    $colorTexto = $alt ? [255,255,255] : [200,0,0];
    $pdf->SetFillColor(...$colorFondo);
    $pdf->SetTextColor(...$colorTexto);

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->MultiCell($colIzqWidth, 60, $textoIzq, 1, 'C', 1, 0, '', '', true, 0, false, true, 60, 'M', true);

    // Columna derecha
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('helvetica', 'B', 14);
    $detalle = htmlspecialchars($r['titulo'])."\n";
    $pdf->MultiCell($colDerWidth, 8, $detalle, 1, 'L', 0, 1, '', '', true);

    $pdf->SetFont('helvetica', '', 11);
    $texto = htmlspecialchars($r['descripcion']) . "\n\n";
    $texto .= "<b>Location:</b> " . htmlspecialchars($r['lugar']) . "\n";
    $texto .= "<b>Time:</b> " . $r['hora_inicio'] . " - " . $r['hora_fin'];

    $pdf->MultiCell($colDerWidth, 30, $texto, 1, 'L', 0, 1, '', '', true);

    // Imagen
    if ($r['imagen'] && file_exists(__DIR__ . '/../uploads/' . $r['imagen'])) {
        $imgPath = __DIR__ . '/../uploads/' . $r['imagen'];
        $pdf->Image($imgPath, $pdf->GetX()+10, $pdf->GetY(), 70);
        $pdf->Ln(50);
    } else {
        $pdf->Ln(10);
    }

    $alt = !$alt;
}
ob_end_clean();
$pdf->Output('programa_visitas_'.$form_id.'.pdf', 'I');
exit;