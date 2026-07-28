# Manual de Usuario — GymTracker Pro

Guía práctica para el entrenador. No cubre instalación/despliegue (ver `CLAUDE.md` para eso) — es para el uso diario de la app ya funcionando.

## 1. Ingreso

Entrá a la URL de la app — te lleva directo al login. Iniciá sesión con tu correo y contraseña de entrenador. Si olvidaste tu contraseña, usá "¿Olvidaste tu contraseña?" en el login.

## 2. Panel principal (Dashboard)

Al entrar ves: total de clientes, clientes activos, y una lista de alertas de clientes con evaluación de 21 días próxima a vencer (3 días o menos).

## 3. Clientes

**Lista** (`Clientes` en el menú): buscá por nombre o correo, filtrá por estado. Cada cliente puede estar **Activo**, **Pausado** o **Inactivo** — pausar/reactivar es reversible, eliminar no (queda oculto pero recuperable por soporte).

**Nuevo cliente**: nombre, correo, teléfono, fecha de nacimiento, género, fecha de inicio, objetivo (texto libre).

**Meta SMART (opcional)**: además del objetivo en texto, podés fijar una meta medible: elegí la métrica (peso o % grasa corporal), el valor objetivo y la fecha límite. La ficha del cliente va a mostrar automáticamente el % de progreso real, calculado contra sus métricas físicas registradas.

**Meta nutricional (opcional)**: kcal y proteína objetivo, más una nota de fase (ej. "Fase de volumen"). Se muestra como referencia en la pestaña Bienestar.

## 4. Ficha del cliente

Al abrir un cliente, la cabecera muestra: nombre, estado, meta SMART (si tiene), badge de lesiones activas (si tiene alguna), y accesos a Descargar PDF / Exportar Excel / Editar.

Las pestañas:

### Registrar Métrica Física
Peso, altura, % grasa, edad metabólica, kcal basales, grasa visceral (datos de balanza InBody). El IMC se calcula solo. También podés registrar medidas corporales (cintura, cadera, pecho, brazos, muslos) — el gráfico de **Simetría Corporal** compara automáticamente lado derecho vs. izquierdo del último registro.

### Subir Fotos Corporales
4 posiciones (frente, espalda, lado izquierdo, lado derecho) por fecha. Se comprimen automáticamente al subirlas.

### Evaluaciones
Se generan solas cada 21 días (no hace falta hacer nada) y muestran, en formato línea de tiempo:
- Comparación de métricas vs. la evaluación anterior.
- **Logros del período**: récords de carga nuevos, % de asistencia, % de cumplimiento nutricional — calculado automático.

El entrenador recibe un correo cuando se genera una evaluación nueva.

### Gráfico de Progreso
Peso, % grasa e IMC en el tiempo. Cada gráfico tiene un ícono ⓘ junto al título — pasale el mouse (o tocá si estás en el teclado) para ver qué mide y cómo interpretarlo.

### Asistencia
Calendario del mes + registro de check-in. Junto al **Riesgo de Carga (ACWR)** vas a ver, si corresponde:
- **Recomendación de descarga**: si el cliente lleva 6+ semanas sin una rutina en fase de descarga.
- **Retorno gradual recomendado**: si el cliente acaba de volver tras una ausencia de 7+ días.

### Bienestar
Cuatro registros semanales/puntuales: ánimo/energía/motivación + horas y calidad de sueño, cumplimiento nutricional, encuesta de satisfacción, y evaluaciones de movilidad/flexibilidad (vos elegís el nombre del test, ej. "Sit and reach").

### Lesiones
Registrá zona afectada, severidad (1-10), estado (activa/en recuperación/resuelta) y notas. Mientras haya una lesión sin resolver, aparece un aviso rojo en la cabecera del cliente.

### Rutinas
Cada rutina tiene una **fase de periodización**: Acumulación, Intensificación, Realización o Descarga — elegí la que corresponda al bloque de entrenamiento actual. Dentro de cada rutina agregás ejercicios y registrás cargas (peso, series, reps, RPE). Por ejercicio vas a ver:
- **1RM Estimado**: fuerza máxima estimada (no hace falta un test real de una repetición).
- **Nivel de fuerza**: comparación contra estándares de fuerza relativa al peso corporal (para sentadilla, press de banca y peso muerto).
- Aviso si hace 4+ semanas que no se registra carga para ese ejercicio (conviene un test nuevo).
- **Volumen por grupo muscular** y **RPE en el tiempo** a nivel de rutina completa.

## 5. Notificaciones automáticas

No requieren acción tuya, llegan solas por correo al entrenador:
- Cliente sin asistencia hace exactamente 7 días.
- Evaluación de 21 días recién generada.

## 6. Compartir progreso con el cliente

Desde la ficha del cliente, generá el enlace de "portal" (ver botón correspondiente) y compartíselo por WhatsApp o correo. Es un link de solo lectura, sin necesidad de que el cliente tenga usuario/contraseña — muestra su meta, evolución de métricas y logros por período. El link no permite editar nada.

## 7. Exportables

- **Descargar PDF**: ficha resumen con última métrica, comparación de evaluación, logros del período, y rutina activa.
- **Exportar Excel**: historial completo de métricas y medidas corporales.
