<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - BMS Monitoring System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand {
            font-size: 20px;
            font-weight: 700;
            color: #1a202c;
        }
        
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        
        .btn-logout {
            padding: 8px 16px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-logout:hover {
            background: #dc2626;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px;
        }
        
        .welcome-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 32px;
        }
        
        .welcome-card h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 8px;
        }
        
        .welcome-card p {
            color: #6b7280;
            font-size: 16px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card-title {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .stat-card-value {
            font-size: 32px;
            font-weight: 700;
            color: #1a202c;
        }
        
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 24px;
        }
        
        .info-box h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 8px;
        }
        
        .info-box p {
            font-size: 14px;
            color: #1e3a8a;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 16px;
            }
            
            .navbar-user {
                width: 100%;
                justify-content: space-between;
            }
            
            .container {
                padding: 20px 16px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">BMS Monitoring System</div>
        <div class="navbar-user">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="user-name">{{ Auth::user()->name ?? 'User' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>
    
    <div class="container">
        <div class="welcome-card">
            <h1>Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p>Dashboard Monitoring Sistem BMS Anda</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-title">Status Sistem</div>
                <div class="stat-card-value">Aktif</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-title">Total Data</div>
                <div class="stat-card-value">-</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-title">Update Terakhir</div>
                <div class="stat-card-value">-</div>
            </div>
        </div>
        
        <div class="info-box">
            <h3>Informasi</h3>
            <p>
                Dashboard ini akan menampilkan data monitoring dari ESP32 yang terhubung ke sistem BMS dan Inverter.
                Pastikan ESP32 sudah dikonfigurasi dengan benar dan terhubung ke server ini.
            </p>
        </div>
    </div>
</body>
</html>
