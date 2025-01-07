<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Monitor Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="libraries/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-desktop me-2"></i>
                System Monitor
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="refreshBtn">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            System Overview
                            <span class="float-end text-muted" style="font-size: 0.9rem;">
                                Last updated: <span id="lastUpdate">{{ date('Y-m-d H:i:s') }}</span>
                            </span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- CPU Load -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-microchip metric-icon text-primary"></i>
                        <h5 class="card-title">CPU Load</h5>
                        <h2 class="mb-3">{{ $metrics['cpu_load'] }}%</h2>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ $metrics['cpu_load'] }}%" 
                                 aria-valuenow="{{ $metrics['cpu_load'] }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RAM Usage -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-memory metric-icon text-success"></i>
                        <h5 class="card-title">RAM Usage</h5>
                        <h2 class="mb-3">{{ $metrics['ram_usage'] ?? 0 }} MB</h2>
                        <p class="text-muted">of {{ $metrics['memory_limit'] ?? 'N/A' }} MB</p>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ isset($metrics['ram_usage'], $metrics['memory_limit']) && is_numeric($metrics['ram_usage']) && is_numeric($metrics['memory_limit']) && $metrics['memory_limit'] > 0 ? ($metrics['ram_usage'] / $metrics['memory_limit']) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disk Usage -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-hdd metric-icon text-warning"></i>
                        <h5 class="card-title">Disk Usage</h5>
                        <h2 class="mb-3">{{ $metrics['disk_free_space'] }} GB</h2>
                        <p class="text-muted">Free Space</p>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: {{ $metrics['disk_usage_percentage'] }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Details -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            System Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <th scope="row">Operating System</th>
                                        <td>{{ $metrics['os'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Server Uptime</th>
                                        <td>{{ $metrics['uptime'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">PHP Version</th>
                                        <td>{{ $metrics['php_version'] ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="libraries/scripts.js"></script>
</body>
</html>