<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro de Material</title>
    
    <!-- Bootstrap & Plugins -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .navbar-brand { 
            font-weight: bold;
            font-size: 1.3rem;
        }
        .container-fluid {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .btn {
            border-radius: 6px;
        }
        .form-control, .form-select {
            border-radius: 6px;
        }
    </style>
    <script>window.assetBase = "{{ asset('') }}";</script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">
                    <i class="fas fa-box-open"></i> REGISTRO DE MATERIAL
                </a>
                <a href="/" class="btn btn-light btn-sm">
                    <i class="fas fa-home"></i> Inicio
                </a>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="text-center text-primary mb-4">
                        <i class="fas fa-warehouse"></i> Sistema de Registro de Material
                    </h1>
                </div>
            </div>

            <div id="appSistema">
                <!-- Componente de búsqueda -->
                <busqueda_registromaterial @resultados="actualizarResultados" :forms="forms" ref="busqueda_registromaterial"></busqueda_registromaterial>
                
                <!-- Componente principal -->
                <registromaterial @buscar='cargarRegistros()' :forms="forms" ref="registromaterial"></registromaterial>
            </div>
        </div>
    </div>

    <!-- Vue 3 -->
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    
    <!-- Alertify -->
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
    
    <!-- Componentes -->
    <script src="/componentes/registromaterial.js"></script>
    <script src="/componentes/busqueda_registromaterial.js"></script>

    <script>
        const { createApp } = Vue;

        const app = createApp({
            data() {
                return {
                    forms: {
                        registromaterial: {
                            mostrar: true
                        },
                        busqueda_registromaterial: {
                            mostrar: false
                        }
                    }
                }
            },
            components: {
                registromaterial,
                busqueda_registromaterial
            },
            methods: {
                async cargarRegistros() {
                    try {
                        let response = await fetch((window.assetBase || '') + `private/modulos/registromaterial/registromaterial.php?accion=consultar`, {
                            method: 'GET'
                        });
                        let data = await response.json();
                        this.$refs.registromaterial.data_registromateriales = data;
                    } catch(err) {
                        console.error('Error al cargar:', err);
                    }
                },
                actualizarResultados(datos) {
                    this.$refs.registromaterial.data_registromateriales = datos;
                }
            },
            mounted() {
                this.cargarRegistros();
            }
        });

        app.mount('#appSistema');
    </script>
</body>
</html>
