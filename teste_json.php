<?php
$teste = "Olá, tudo bem?\nSim, perfeito!\nEspero que funcione!";
echo "Original: " . $teste . "\n\n";
echo "json_encode (com flags): " . json_encode($teste, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG) . "\n\n";
echo "htmlspecialchars: " . htmlspecialchars($teste, ENT_QUOTES, 'UTF-8') . "\n";
echo "json_encode de htmlspecialchars: " . json_encode(htmlspecialchars($teste, ENT_QUOTES, 'UTF-8'), JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG) . "\n";
?>
