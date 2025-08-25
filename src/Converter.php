<?php

namespace WordToHtml;


class Converter
{
  /** @var string */
  private string $libreofficePath;

  /**
   * Construct for class Converter.
   *
   * @param string $libreofficePath
   */
  public function __construct(string $libreofficePath = 'libreoffice')
  {
    $this->libreofficePath = $libreofficePath;
  }

  /**
   * Convertit un .doc/.docx en HTML via LibreOffice (headless)
   * et retourne le contenu HTML sous forme de string.
   *
   * @param string $inputFile
   * @param string|null $outputDir
   * @param int $timeoutSec
   *
   * @return string
   *
   * @throws \Exception
   */
  public function convert(string $inputFile, ?string $outputDir, int $timeoutSec = 60): string
  {
    if (!is_file($inputFile)) {
      throw new \RuntimeException("Fichier introuvable: {$inputFile}");
    }
    $tmpCreated = false;
    if ($outputDir === null) {
      $outputDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'lohtml_' . bin2hex(random_bytes(4));
      if (!mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
        throw new \RuntimeException("Impossible de créer le dossier temporaire: {$outputDir}");
      }
      $tmpCreated = true;
    } elseif (!is_dir($outputDir)) {
      throw new \RuntimeException("Dossier de sortie invalide: {$outputDir}");
    }

    // Construction de la commande (avec échappement)
    $cmd = sprintf(
      '%s --headless --convert-to html --outdir %s %s 2>&1',
      escapeshellcmd($this->libreofficePath),
      escapeshellarg($outputDir),
      escapeshellarg($inputFile)
    );

    // Exécution avec timeout simple
    $start = time();
    $output = [];
    $exitCode = 0;
    exec($cmd, $output, $exitCode);
    if ((time() - $start) > $timeoutSec) {
      throw new \RuntimeException("Conversion dépassée (timeout {$timeoutSec}s).");
    }
    if ($exitCode !== 0) {
      throw new \RuntimeException("Erreur LibreOffice (code {$exitCode}) : " . implode("\n", $output));
    }

    // LibreOffice crée <basename>.html dans $outputDir
    $base = pathinfo($inputFile, PATHINFO_FILENAME);
    $htmlPath = $outputDir . DIRECTORY_SEPARATOR . $base . '.html';
    if (!is_file($htmlPath)) {
      // Certains fichiers ont des extensions/encodages bizarres → on tente une recherche
      $candidates = glob($outputDir . DIRECTORY_SEPARATOR . '*.html');
      if (!$candidates) {
        // Nettoyage si tmp créé
        if ($tmpCreated) @rmdir($outputDir);
        throw new \RuntimeException("Conversion échouée: aucun fichier HTML généré dans {$outputDir}");
      }
      $htmlPath = $candidates[0];
    }
    $html = file_get_contents($htmlPath);
    if ($html === false) {
      throw new \RuntimeException("Impossible de lire le fichier HTML: {$htmlPath}");
    }

    return $html;
  }
}
