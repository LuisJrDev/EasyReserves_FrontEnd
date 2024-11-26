<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas Disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <!-- Formulario -->
            <div class="col-md-6">
                <h2>Reservar Actividad</h2>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @elseif(session('success'))
                    <div class="alert alert-success">
                        <strong>Éxito:</strong> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('makeReservation') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="actividad" class="form-label">Actividad</label>
                        <select class="form-select" id="actividad" name="actividad" required>
                            <option value="">Selecciona una actividad</option>
                            @if(isset($disponibilidad))
                                @foreach($disponibilidad as $actividad => $fechas)
                                    <option value="{{ $actividad }}">{{ $actividad }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" required>
                    </div>
                    <div class="mb-3">
                        <label for="asientos" class="form-label">Asientos</label>
                        <input type="number" class="form-control" id="asientos" name="asientos" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Reservar</button>
                </form>
            </div>

            <!-- Lista de actividades disponibles -->
            <div class="col-md-6">
                <h2>Actividades Disponibles</h2>
                @if(isset($error))
                    <div class="alert alert-danger">
                        <strong>Error:</strong> {{ $error }}
                    </div>
                @elseif(isset($disponibilidad) && !empty($disponibilidad))
                    @foreach($disponibilidad as $actividad => $fechas)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">{{ $actividad }}</h5>
                                <ul class="list-group list-group-flush">
                                    @foreach($fechas as $fecha => $detalles)
                                        <li class="list-group-item actividad-item"
                                            data-actividad="{{ $actividad }}"
                                            data-fecha="{{ $fecha }}">
                                            <strong>{{ $fecha }}:</strong>
                                            Estado: {{ $detalles['estado'] }},
                                            Asientos disponibles: {{ $detalles['asientos_disponibles'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-warning">
                        No hay reservas disponibles en este momento.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript para completar los campos automáticamente -->
    <script>
        // Escuchar el evento de clic en las actividades disponibles
        document.querySelectorAll('.actividad-item').forEach(item => {
            item.addEventListener('click', function() {
                const actividad = this.getAttribute('data-actividad');
                const fecha = this.getAttribute('data-fecha');

                // Completar los campos del formulario
                document.getElementById('actividad').value = actividad;
                document.getElementById('fecha').value = fecha;
            });
        });
    </script>
</body>
</html>
