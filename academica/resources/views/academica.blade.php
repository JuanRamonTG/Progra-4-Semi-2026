<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistema Académico - Laravel</title>
    
    <!-- Bootstrap & Plugins -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/bootstrap.min.css" />
    
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; }
    </style>
    <script>window.assetBase = "{{ asset('') }}";</script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">::.. SISTEMA ACADÉMICO (LARAVEL) ..::</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="navbar-nav">
                        <a class="nav-link" href="#" @click="abrirVentana('alumnos')">Alumnos</a>
                        <a class="nav-link" href="#" @click="abrirVentana('materias')">Materias</a>
                        <a class="nav-link" href="#" @click="abrirVentana('docentes')">Docentes</a>
                        <a class="nav-link" href="#" @click="abrirVentana('matriculas')">Matriculas</a>
                        <a class="nav-link" href="#" @click="abrirVentana('inscripciones')">Inscripciones</a>
                        <div class="nav-divider"></div>
                        <a class="nav-link" href="/registromaterial">📦 Registro Material</a>
                        <a class="nav-link" href="/registrofallecido">💔 Registro Fallecido</a>
                    </div>
                </div>
            </div>
        </nav>

        <div id="appSistema" class="container-fluid">
            <!-- Componentes Vue -->
            <alumnos @buscar='buscar("busqueda_alumnos","obtenerAlumnos")' :forms="forms" ref="alumnos" v-show="forms.alumnos.mostrar"></alumnos>
            <busqueda_alumnos @modificar='modificar("alumnos","modificarAlumno", $event)' ref="busqueda_alumnos" v-show="forms.busqueda_alumnos.mostrar"></busqueda_alumnos>

            <materias @buscar='buscar("busqueda_materias","obtenerMaterias")' :forms="forms" ref="materias" v-show="forms.materias.mostrar"></materias>
            <busqueda_materias @modificar='modificar("materias","modificarMateria", $event)' ref="busqueda_materias" v-show="forms.busqueda_materias.mostrar"></busqueda_materias>

            <docentes @buscar='buscar("busqueda_docentes","obtenerDocentes")' :forms="forms" ref="docentes" v-show="forms.docentes.mostrar"></docentes>
            <busqueda_docentes @modificar='modificar("docentes","modificarDocente", $event)' ref="busqueda_docentes" v-show="forms.busqueda_docentes.mostrar"></busqueda_docentes>

            <matriculas @buscar='buscar("busqueda_matriculas","obtenerMatriculas")' :forms="forms" ref="matriculas" v-show="forms.matriculas.mostrar"></matriculas>
            <busqueda_matriculas @modificar='modificar("matriculas","modificarMatricula", $event)' ref="busqueda_matriculas" v-show="forms.busqueda_matriculas.mostrar"></busqueda_matriculas>

            <inscripciones @buscar='buscar("busqueda_inscripciones","obtenerInscripciones")' :forms="forms" ref="inscripciones" v-show="forms.inscripciones.mostrar"></inscripciones>
            <busqueda_inscripciones @modificar='modificar("inscripciones","modificarInscripcion", $event)' ref="busqueda_inscripciones" v-show="forms.busqueda_inscripciones.mostrar"></busqueda_inscripciones>
        </div>
    </div>

    <!-- Dependencias Externas -->
    <script src="https://cdn.jsdelivr.net/npm/crypto-js@4.1.1/crypto-js.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <!-- App Logic con paths absolutos para Laravel -->
    <script src="{{ asset('componentes/sqlite3-worker1-promiser.js') }}"></script>
    <script src="{{ asset('db.js') }}"></script>
    
    <!-- Componentes -->
    <script src="{{ asset('componentes/alumnos.js') }}"></script>
    <script src="{{ asset('componentes/busqueda_alumnos.js') }}"></script>
    <script src="{{ asset('componentes/materias.js') }}"></script>
    <script src="{{ asset('componentes/busqueda_materias.js') }}"></script>
    <script src="{{ asset('componentes/docentes.js') }}"></script>
    <script src="{{ asset('componentes/busqueda_docentes.js') }}"></script>
    <script src="{{ asset('componentes/matriculas.js') }}"></script>
    <script src="{{ asset('componentes/busqueda_matriculas.js') }}"></script>
    <script src="{{ asset('componentes/inscripciones.js') }}"></script>
    <script src="{{ asset('componentes/busqueda_inscripciones.js') }}"></script>
    
    <script src="{{ asset('main.js') }}"></script>
</body>
</html>
