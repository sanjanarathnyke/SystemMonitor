<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SystemMetricsController extends Controller
{
    public function Index()
    {
        // Get CPU Usage
        $cpuLoad = $this->getCpuLoad();

        // Get RAM Usage
        $memoryUsage = memory_get_usage(true); // Bytes
        $memoryLimit = $this->convertToBytes(ini_get('memory_limit'));

        // Validate values to ensure they are numeric
        $ramUsage = is_numeric($memoryUsage) ? $memoryUsage : 0;
        $memoryLimit = is_numeric($memoryLimit) && $memoryLimit > 0 ? $memoryLimit : 1; // Avoid division by zero

        // Get Disk Space
        $diskFreeSpace = disk_free_space("/") ?: 0;
        $diskTotalSpace = disk_total_space("/") ?: 0;

        if ($diskTotalSpace > 0) {
            $diskUsagePercentage = ($diskTotalSpace - $diskFreeSpace) / $diskTotalSpace * 100;
        } else {
            $diskUsagePercentage = 0;
        }


        $metrics = [
            'cpu_load' => $cpuLoad,
            'ram_usage' => round($memoryUsage / 1024 / 1024, 2), // Convert to MB
            'memory_limit' => round($memoryLimit / 1024 / 1024, 2) . ' MB',
            'disk_free_space' => round($diskFreeSpace / 1024 / 1024 / 1024, 2), // Convert to GB
            'disk_usage_percentage' => round($diskUsagePercentage, 2),
        ];

        return view('welcome', compact('metrics'));
    }

    public function getCpuLoad()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            $output = [];
            @exec("wmic cpu get loadpercentage", $output);
            if (isset($output[1]) && is_numeric($output[1])) {
                return (float) trim($output[1]);
            }
        } else {
            // Linux or macOS
            if (file_exists('/proc/loadavg')) {
                $load = file_get_contents('/proc/loadavg');
                $loadParts = explode(' ', $load);
                if (isset($loadParts[0]) && is_numeric($loadParts[0])) {
                    return (float) $loadParts[0];
                }
            }
        }
        return 0; // Return 0 if data is unavailable
    }

    public function convertToBytes($value)
    {
        $unit = strtoupper(substr($value, -1));
        $bytes = (int) $value;

        switch ($unit) {
            case 'G':
                $bytes *= 1024 * 1024 * 1024;
                break;
            case 'M':
                $bytes *= 1024 * 1024;
                break;
            case 'K':
                $bytes *= 1024;
                break;
        }
        return $bytes;
    }
}
