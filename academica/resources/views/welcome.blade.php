<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistema Integral - Inicio</title>
        
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .main-container {
                background: rgba(255, 255, 255, 0.95);
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                padding: 40px;
                max-width: 1000px;
                width: 100%;
            }
            
            .header {
                text-align: center;
                margin-bottom: 50px;
            }
            
            .header h1 {
                color: #667eea;
                font-weight: 700;
                margin-bottom: 10px;
                font-size: 2.5rem;
            }
            
            .header p {
                color: #666;
                font-size: 1.1rem;
            }
            
            .modules-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 25px;
                margin-bottom: 30px;
            }
            
            .module-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 12px;
                overflow: hidden;
                transition: all 0.3s ease;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                height: 100%;
            }
            
            .module-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            }
            
            .module-card.primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .module-card.info {
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            }
            
            .module-card.success {
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            }
            
            .module-card.danger {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            }
            
            .module-header {
                color: white;
                padding: 25px;
                text-align: center;
            }
            
            .module-icon {
                font-size: 3rem;
                margin-bottom: 10px;
                display: block;
            }
            
            .module-title {
                font-size: 1.3rem;
                font-weight: 600;
                margin: 0;
            }
            
            .module-body {
                padding: 20px;
                background: rgba(255, 255, 255, 0.9);
                color: #333;
            }
            
            .module-description {
                font-size: 0.95rem;
                color: #666;
                margin-bottom: 15px;
                line-height: 1.5;
            }
            
            .module-link {
                display: inline-block;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 10px 20px;
                border-radius: 6px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
                width: 100%;
                text-align: center;
            }
            
            .module-link:hover {
                color: white;
                text-decoration: none;
                transform: scale(1.05);
            }
            
            .module-card.info .module-link {
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            }
            
            .module-card.success .module-link {
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            }
            
            .module-card.danger .module-link {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            }
            
            .footer-text {
                text-align: center;
                color: #666;
                font-size: 0.9rem;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #e0e0e0;
            }
            
            @media (max-width: 768px) {
                .main-container {
                    padding: 20px;
                }
                
                .header h1 {
                    font-size: 1.8rem;
                }
                
                .modules-grid {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }
            }
        </style>
    </head>
    <body>
        <div class="main-container">
            <div class="header">
                <h1><i class="fas fa-cube"></i> Sistema Integral</h1>
                <p>Gestión académica y registro de eventos</p>
            </div>
            
            <div class="modules-grid">
                <!-- Sistema Académico -->
                <div class="module-card info">
                    <div class="module-header">
                        <span class="module-icon"><i class="fas fa-graduation-cap"></i></span>
                        <h5 class="module-title">Sistema Académico</h5>
                    </div>
                    <div class="module-body">
                        <p class="module-description">Gestión de alumnos, docentes, materias, matrículas e inscripciones.</p>
                        <a href="/sistema" class="module-link"><i class="fas fa-arrow-right"></i> Acceder</a>
                    </div>
                </div>
                
                <!-- Registro de Material -->
                <div class="module-card primary">
                    <div class="module-header">
                        <span class="module-icon"><i class="fas fa-box-open"></i></span>
                        <h5 class="module-title">Registro de Material</h5>
                    </div>
                    <div class="module-body">
                        <p class="module-description">Registra y gestiona el material con fecha, ubicación y verificación.</p>
                        <a href="/registromaterial" class="module-link"><i class="fas fa-arrow-right"></i> Acceder</a>
                    </div>
                </div>
                
                <!-- Registro de Fallecido -->
                <div class="module-card danger">
                    <div class="module-header">
                        <span class="module-icon"><i class="fas fa-heartbeat"></i></span>
                        <h5 class="module-title">Registro de Fallecido</h5>
                    </div>
                    <div class="module-body">
                        <p class="module-description">Registra eventos de fallecimiento con datos detallados y testigos.</p>
                        <a href="/registrofallecido" class="module-link"><i class="fas fa-arrow-right"></i> Acceder</a>
                    </div>
                </div>
            </div>
            
            <div class="footer-text">
                <p><strong>Sistema Integral</strong> - Todos los módulos integrados en una plataforma</p>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
