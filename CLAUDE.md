# CLAUDE.md

Contexto para trabajar en GymTracker Pro con asistencia de IA.

## Qué es esto

App Laravel para que un gimnasio de entrenamiento personal gestione hasta 100 clientes: métricas físicas, fotos corporales, evaluaciones automáticas cada 21 días, rutinas/cargas, asistencia, gráficos de progreso.

**La especificación completa vive en `docs/planning/`** — léela antes de inventar alcance nuevo:
- `1.-Plan_trabajo_gimnasio_laravel.md`: requisitos funcionales (RF-XXX), esquema de BD exacto, arquitectura.
- `2.-Plan_estrategico_gimnasio.md`: priorización Pareto/MoSCoW, riesgos, roadmap.
- `3.-kanban_tasks_smart.md`: historias de usuario SMART (US-001..US-013) con story points.

Cuando te pidan una funcionalidad, ubica primero el RF-XXX / US-XXX correspondiente. Prioriza las 8 funcionalidades críticas del Pareto (doc 2 §2) antes que las "Should/Could have" del MoSCoW.

## Stack (ya decidido, no reabrir la discusión)

Laravel 12, PHP 8.4, MySQL 8.4, Redis, Livewire + Alpine + Tailwind, Breeze (stack livewire) para auth, Sail para Docker local, Pest para tests, DomPDF para export (fase posterior).

## Convenciones

- **Toda funcionalidad nueva lleva su test Pest.** No se da por terminada una feature sin test (`tests/Feature/` o `tests/Unit/`) cubriendo al menos el happy path.
- Modelos usan atributos PHP para fillable/hidden (`#[Fillable([...])]`, ver `app/Models/Client.php`), no el array `protected $fillable` clásico — seguir ese estilo.
- Migraciones son clases anónimas (`return new class extends Migration`), estilo por defecto de Laravel 12.
- Orden de dependencia de FKs entre las tablas de dominio: `clients` → `evaluations` → `physical_metrics`/`body_measurements` → `routines` → `exercises` → `workout_logs` → `attendances`/`mood_records`/`nutrition_logs`/`satisfaction_surveys`/`body_photos`.
- `PhysicalMetric` calcula `bmi` automáticamente en un hook `static::saving()` a partir de `weight_kg` y `height_cm` (este campo no estaba en el doc original, se agregó porque el cálculo de BMI lo requiere).

## Cómo correr cosas

No hay PHP/Composer/Node en el host — todo vía Docker:

```bash
./vendor/bin/sail up -d               # levantar
./vendor/bin/sail artisan migrate     # migraciones
./vendor/bin/sail php vendor/bin/pest # tests
./vendor/bin/sail down                # bajar
```

Si `sail` no está disponible aún (primera vez, sin `vendor/`): usar `docker compose` directo sobre `compose.yaml`, o `docker run --rm -v "$(pwd)":/opt -w /opt laravelsail/php84-composer:latest <comando>` para composer/artisan sin contenedores levantados.

## Nota WSL2 / Windows

Si este repo vive bajo `/mnt/c/...`, instalar dependencias (`composer install`, `npm install`) directo ahí es muy lento (miles de archivos chicos sobre el mount 9p). Si hay que reinstalar todo desde cero, conviene hacerlo primero en el filesystem nativo de Linux (`~/algo`) y copiar el resultado con `rsync -a --no-perms --no-owner --no-group --exclude node_modules`.
