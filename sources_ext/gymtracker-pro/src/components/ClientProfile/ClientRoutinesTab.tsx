import React, { useState } from 'react';
import { Client, Exercise, WorkoutLog } from '../../types';
import { useGym } from '../../context/GymContext';
import {
  Dumbbell,
  Plus,
  Flame,
  Clock,
  TrendingUp,
  History,
  CheckCircle2,
  Calendar,
  Sparkles
} from 'lucide-react';

interface ClientRoutinesTabProps {
  client: Client;
  onOpenNewWorkoutLog: (exercise?: Exercise) => void;
}

export const ClientRoutinesTab: React.FC<ClientRoutinesTabProps> = ({
  client,
  onOpenNewWorkoutLog
}) => {
  const { getClientRoutines, getClientWorkoutLogs } = useGym();
  
  const routines = getClientRoutines(client.id);
  const workoutLogs = getClientWorkoutLogs(client.id);
  const activeRoutine = routines.find((r) => r.isActive) || routines[0];

  const [selectedExerciseId, setSelectedExerciseId] = useState<string | null>(
    activeRoutine?.exercises[0]?.id || null
  );

  const selectedExercise = activeRoutine?.exercises.find((e) => e.id === selectedExerciseId);
  const exerciseLogs = selectedExercise
    ? workoutLogs.filter((l) => l.exerciseId === selectedExercise.id)
    : [];

  return (
    <div className="space-y-6">
      
      {/* Active Routine Overview Banner */}
      {activeRoutine ? (
        <div className="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl space-y-4">
          <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div>
              <div className="flex items-center gap-2 mb-1">
                <span className="px-2.5 py-0.5 rounded-full bg-cyan-500/20 text-cyan-400 text-[10px] font-bold border border-cyan-500/30 uppercase">
                  Plan Activo de 3 Meses
                </span>
                <span className="text-slate-400 text-xs">
                  • {activeRoutine.weeklyFrequency} días por semana
                </span>
              </div>
              <h2 className="text-xl font-extrabold text-white">{activeRoutine.name}</h2>
              <p className="text-xs text-slate-400 mt-1">{activeRoutine.description}</p>
            </div>

            <button
              onClick={() => onOpenNewWorkoutLog(selectedExercise || activeRoutine.exercises[0])}
              className="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-extrabold text-xs rounded-xl shadow transition flex items-center gap-1.5 shrink-0"
            >
              <Plus className="w-4 h-4 stroke-[3]" />
              Registrar Carga de Hoy
            </button>
          </div>

          <div className="flex flex-wrap items-center gap-4 text-xs text-slate-300">
            <span className="flex items-center gap-1">
              <Calendar className="w-3.5 h-3.5 text-cyan-400" />
              Inicio: {activeRoutine.startDate}
            </span>
            <span className="flex items-center gap-1">
              <Dumbbell className="w-3.5 h-3.5 text-emerald-400" />
              Ejercicios: {activeRoutine.exercises.length}
            </span>
            <span className="flex items-center gap-1">
              <Sparkles className="w-3.5 h-3.5 text-amber-400" />
              Estructura: 1ª Serie Base (15 reps) + 3ª Serie Efectiva Máxima (8-10 reps)
            </span>
          </div>
        </div>
      ) : (
        <div className="p-8 bg-slate-900 rounded-2xl border border-slate-800 text-center text-slate-400 space-y-3">
          <Dumbbell className="w-10 h-10 mx-auto text-slate-600" />
          <p className="text-sm font-medium">No hay rutina asignada a este cliente.</p>
        </div>
      )}

      {/* Routine Exercises & Max Load Progression Section */}
      {activeRoutine && (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          {/* Exercises List (1 Column) */}
          <div className="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-xl space-y-3">
            <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider px-2">
              Ejercicios del Plan ({activeRoutine.exercises.length})
            </h3>

            <div className="space-y-2">
              {activeRoutine.exercises.map((ex) => {
                const isSelected = ex.id === selectedExerciseId;
                const logsForEx = workoutLogs.filter((l) => l.exerciseId === ex.id);
                const latestLog = logsForEx[0];

                return (
                  <button
                    key={ex.id}
                    onClick={() => setSelectedExerciseId(ex.id)}
                    className={`w-full text-left p-3 rounded-xl border transition flex items-center justify-between ${
                      isSelected
                        ? 'bg-cyan-500/10 border-cyan-500/50 text-white shadow-md'
                        : 'bg-slate-800/60 border-slate-700/60 text-slate-300 hover:bg-slate-800'
                    }`}
                  >
                    <div>
                      <p className="font-bold text-xs">{ex.name}</p>
                      <p className="text-[10px] text-slate-400">
                        {ex.muscleGroup} • {ex.sets} Series ({ex.repsRange})
                      </p>
                    </div>

                    {latestLog && (
                      <div className="text-right">
                        <span className="text-xs font-black text-cyan-400">
                          {latestLog.effectiveSetWeightKg} kg
                        </span>
                        <span className="block text-[9px] text-slate-500">Máx Efectiva</span>
                      </div>
                    )}
                  </button>
                );
              })}
            </div>
          </div>

          {/* Exercise Progression Detail (2 Columns) */}
          <div className="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-5">
            {selectedExercise ? (
              <>
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
                  <div>
                    <span className="text-[10px] font-bold text-cyan-400 uppercase tracking-wider">
                      {selectedExercise.muscleGroup}
                    </span>
                    <h3 className="text-lg font-bold text-white">{selectedExercise.name}</h3>
                    <p className="text-xs text-slate-400 mt-0.5">{selectedExercise.notes}</p>
                  </div>

                  <button
                    onClick={() => onOpenNewWorkoutLog(selectedExercise)}
                    className="px-3 py-1.5 bg-cyan-500 text-slate-950 font-bold text-xs rounded-xl shadow hover:bg-cyan-400 transition flex items-center gap-1 self-start sm:self-auto"
                  >
                    <Plus className="w-3.5 h-3.5 stroke-[3]" />
                    Anotar Carga
                  </button>
                </div>

                {/* Target Scheme Card */}
                <div className="grid grid-cols-3 gap-3 bg-slate-800/60 p-3 rounded-xl border border-slate-700/50 text-center text-xs">
                  <div>
                    <span className="text-slate-400 text-[10px]">Series Programadas</span>
                    <p className="font-bold text-white text-sm">{selectedExercise.sets} Series</p>
                  </div>
                  <div>
                    <span className="text-slate-400 text-[10px]">Rango Repeticiones</span>
                    <p className="font-bold text-cyan-300 text-sm">{selectedExercise.repsRange}</p>
                  </div>
                  <div>
                    <span className="text-slate-400 text-[10px]">Descanso</span>
                    <p className="font-bold text-emerald-400 text-sm">{selectedExercise.restSeconds} seg</p>
                  </div>
                </div>

                {/* Exercise Load Progression History Table */}
                <div className="space-y-3">
                  <h4 className="text-xs font-bold text-slate-300 flex items-center gap-1.5">
                    <History className="w-4 h-4 text-cyan-400" />
                    Historial de Cargas: 1ª Serie Base vs 3ª Serie Efectiva Máxima
                  </h4>

                  <div className="overflow-x-auto rounded-xl border border-slate-800">
                    <table className="w-full text-left text-xs border-collapse">
                      <thead>
                        <tr className="bg-slate-800 text-slate-400 uppercase text-[10px] font-semibold border-b border-slate-700">
                          <th className="py-2.5 px-3">Fecha</th>
                          <th className="py-2.5 px-3">1ª Serie Base (15 reps)</th>
                          <th className="py-2.5 px-3">3ª Serie Efectiva Máx (8-10 reps)</th>
                          <th className="py-2.5 px-3">Esfuerzo RPE</th>
                          <th className="py-2.5 px-3">Notas</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-slate-800 text-slate-300">
                        {exerciseLogs.length > 0 ? (
                          exerciseLogs.map((log) => (
                            <tr key={log.id} className="hover:bg-slate-800/50 transition">
                              <td className="py-2.5 px-3 font-bold text-white">{log.workoutDate}</td>
                              <td className="py-2.5 px-3 font-medium text-slate-300">
                                {log.baseSetWeightKg} kg
                              </td>
                              <td className="py-2.5 px-3 font-black text-cyan-400">
                                {log.effectiveSetWeightKg} kg
                              </td>
                              <td className="py-2.5 px-3">
                                {log.rpe ? (
                                  <span className="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold text-[10px]">
                                    RPE {log.rpe}/10
                                  </span>
                                ) : (
                                  '--'
                                )}
                              </td>
                              <td className="py-2.5 px-3 text-[11px] text-slate-400 italic">
                                {log.notes || '--'}
                              </td>
                            </tr>
                          ))
                        ) : (
                          <tr>
                            <td colSpan={5} className="py-6 text-center text-slate-500 italic">
                              Aún no hay registros de carga para este ejercicio.
                            </td>
                          </tr>
                        )}
                      </tbody>
                    </table>
                  </div>
                </div>

              </>
            ) : (
              <div className="py-12 text-center text-slate-500">
                Selecciona un ejercicio de la lista para ver su progresión de peso.
              </div>
            )}
          </div>

        </div>
      )}

    </div>
  );
};
