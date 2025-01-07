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
            'memory_limit' => round($this->convertToBytes(ini_get('memory_limit')) / 1024 / 1024, 2) . ' MB',
            'disk_free_space' => round($diskFreeSpace / 1024 / 1024 / 1024, 2), // Convert to GB
            'disk_usage_percentage' => round($diskUsagePercentage, 2),
            'os' => php_uname('s'), // Get the operating system name
            'php_version' => phpversion(), // PHP version
        ];

        return view('welcome', compact('metrics'));
    }

    public function getCpuLoad()
{
    // For Windows
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $load = shell_exec('powershell -Command "Get-WmiObject Win32_Processor | Select-Object -ExpandProperty LoadPercentage"');
        $load = trim($load); // Clean up the result
        return is_numeric($load) ? (int) $load : 0;
    }

    // For Unix-based systems (Linux, macOS)
    if (function_exists('sys_getloadavg')) {
        $load = sys_getloadavg();
        $cpuCores = $this->getCpuCores(); // Get the number of CPU cores
        if ($cpuCores > 0) {
            return round(($load[0] / $cpuCores) * 100, 2); // Convert to percentage
        }
    }

    // Fallback
    return 0; // Default to 0 if load cannot be determined
}


    private function getCpuCores()
    {
        // Detect number of CPU cores
        if (function_exists('shell_exec')) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $cores = shell_exec('wmic cpu get NumberOfLogicalProcessors');
                if ($cores) {
                    return (int) trim(preg_replace('/\D/', '', $cores));
                }
            } elseif (is_file('/proc/cpuinfo')) {
                return (int) shell_exec('grep -c ^processor /proc/cpuinfo');
            } elseif (function_exists('sysctl')) {
                return (int) shell_exec('sysctl -n hw.ncpu');
            }
        }
        return 1; // Default to 1 core if undetectable
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

    // Function to get total system memory (RAM)
    public function getTotalMemory()
    {
        // For Unix-based systems (Linux/macOS)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
            $output = shell_exec('free -m');
            $lines = explode("\n", $output);
            $memory = explode(" ", preg_replace('/\s+/', ' ', $lines[1]));
            return (int) $memory[1]; // Total RAM in MB
        }

        // For Windows systems
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec('wmic computersystem get totalphysicalmemory');
            $output = trim($output);
            return (int) $output / 1024 / 1024; // Convert bytes to MB
        }

        // For unknown systems
        return 'N/A';
    }
}
