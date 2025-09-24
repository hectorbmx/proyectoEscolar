<?php
// includes/functions.php

function h($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

function qs(array $overrides = []): string {
  // Preserva sort/dir/p (si existen) y permite sobreescribir
  $base = [
    'sort' => $_GET['sort'] ?? null,
    'dir'  => $_GET['dir']  ?? null,
    'p'    => $_GET['p']    ?? null,
  ];
  $q = array_filter(array_merge($base, $overrides), fn($v) => $v !== null && $v !== '');
  return http_build_query($q);
}

function paginate(int $page, int $perPage, int $total): string {
  $totalPages = max(1, (int)ceil($total / $perPage));
  if ($totalPages <= 1) return '';

  $html = '<nav aria-label="Paginación"><ul class="pagination justify-content-center">';
  $prevDisabled = $page <= 1 ? ' disabled' : '';
  $nextDisabled = $page >= $totalPages ? ' disabled' : '';

  $html .= '<li class="page-item'.$prevDisabled.'"><a class="page-link" href="?'.qs(['p'=>1]).'">« Primero</a></li>';
  $html .= '<li class="page-item'.$prevDisabled.'"><a class="page-link" href="?'.qs(['p'=>max(1,$page-1)]).'">‹ Anterior</a></li>';

  // Ventana corta de páginas
  $start = max(1, $page - 2);
  $end   = min($totalPages, $page + 2);
  for ($i=$start; $i<=$end; $i++) {
    $active = $i == $page ? ' active' : '';
    $html .= '<li class="page-item'.$active.'"><a class="page-link" href="?'.qs(['p'=>$i]).'">'.$i.'</a></li>';
  }

  $html .= '<li class="page-item'.$nextDisabled.'"><a class="page-link" href="?'.qs(['p'=>min($totalPages,$page+1)]).'">Siguiente ›</a></li>';
  $html .= '<li class="page-item'.$nextDisabled.'"><a class="page-link" href="?'.qs(['p'=>$totalPages]).'">Último »</a></li>';
  $html .= '</ul></nav>';
  return $html;
}
