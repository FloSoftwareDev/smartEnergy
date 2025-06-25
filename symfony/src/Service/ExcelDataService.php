<?php

namespace App\Service;

class ExcelDataService
{
    /**
     * Reads the CSV file and returns mapped energy data as an array.
     *
     * @param string $filePath Path to the CSV file
     * @return array Mapped data
     */
    public function getEnergyData(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File '$filePath' does not exist.");
        }

        $headerMap = [
            'Tijdstip' => 'timestamp',
            'Zonnepaneelspanning (V)' => 'solar_panel_voltage',
            'Zonnepaneelstroom (A)' => 'solar_panel_current',
            'Waterstofproductie (L/u)' => 'hydrogen_production',
            'Stroomverbruik woning (kW)' => 'house_power_consumption',
            'Waterstofverbruik auto (L/u)' => 'car_hydrogen_consumption',
            'Buitentemperatuur (°C)' => 'outside_temperature',
            'Binnentemperatuur (°C)' => 'inside_temperature',
            'Luchtdruk (hPa)' => 'air_pressure',
            'Luchtvochtigheid (%)' => 'humidity',
            'Accuniveau (%)' => 'battery_level',
            'CO2-concentratie binnen (ppm)' => 'co2_concentration_inside',
            'Waterstofopslag woning (%)' => 'house_hydrogen_storage',
            'Waterstofopslag auto (%)' => 'car_hydrogen_storage',
        ];

        $data = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headerRow = fgetcsv($handle, 0, ",");
            if ($headerRow === false) {
                fclose($handle);
                return $data;
            }
            // Remove BOM if present
            $headerRow[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headerRow[0]);

            while (($row = fgetcsv($handle, 0, ",")) !== false) {
                $mappedRow = [];
                foreach ($headerRow as $colIdx => $header) {
                    if (isset($headerMap[$header])) {
                        $mappedRow[$headerMap[$header]] = $row[$colIdx] ?? null;
                    }
                }
                $data[] = $mappedRow;
            }
            fclose($handle);
        }
        return $data;
    }
} 