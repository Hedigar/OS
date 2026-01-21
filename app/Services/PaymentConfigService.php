<?php

namespace App\Services;

class PaymentConfigService
{
    public function parse(string $json): array
    {
        if ($json === '') return [];
        try { return json_decode($json, true) ?: []; } catch (\Throwable $e) { return []; }
    }

    public function enabledMachines(array $cfg): array
    {
        $out = [];
        if (!empty($cfg['maquinas']) && is_array($cfg['maquinas'])) {
            foreach ($cfg['maquinas'] as $m) {
                if (!empty($m['habilitada']) && !empty($m['nome'])) {
                    $out[] = (string)$m['nome'];
                }
            }
        }
        return $out;
    }

    public function aggregateForms(array $cfg): array
    {
        $set = [];
        if (!empty($cfg['maquinas']) && is_array($cfg['maquinas'])) {
            foreach ($cfg['maquinas'] as $m) {
                if (empty($m['habilitada'])) continue;
                if (!empty($m['formas']) && is_array($m['formas'])) {
                    foreach ($m['formas'] as $f) {
                        if (is_string($f) && $f !== '') $set[$f] = true;
                    }
                }
            }
        }
        return array_keys($set);
    }

    public function aggregateBrands(array $cfg): array
    {
        $set = [];
        if (!empty($cfg['maquinas']) && is_array($cfg['maquinas'])) {
            foreach ($cfg['maquinas'] as $m) {
                if (empty($m['habilitada'])) continue;
                if (!empty($m['bandeiras']) && is_array($m['bandeiras'])) {
                    foreach ($m['bandeiras'] as $b) {
                        if (is_string($b) && $b !== '') $set[$b] = true;
                    }
                }
            }
        }
        return array_keys($set);
    }
}
