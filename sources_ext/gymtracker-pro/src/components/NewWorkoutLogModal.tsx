import React, { useState } from 'react';
import { Exercise } from '../types';
import { useGym } from '../context/GymContext';
import { Dumbbell, X, Flame } from 'lucide-react';

interface NewWorkoutLogModalProps {
  isOpen: boolean;
  onClose: () => void;
  clientId: string | null;
  exercise?: Exercise | null;
}

export const NewWorkoutLogModal: React.FC<NewWorkoutLogModalProps> = ({
  isOpen,
  onClose,
  clientId,
  exercise
}) => {
  const { clients, getClientRoutines, addWorkoutLog } = useGym();

  const client = clients.find((c) => c.id === clientId) || clients[0];
  const routines = client ? getClientRoutines(client.id) : [];
  const activeRoutine = routines.find((r) => r.isActive) || routines[0];

  const [selectedExerciseId, setSelectedExerciseId] = useState<string>(
    exercise?.id || activeRoutine?.exercises[0]?.id || ''
  );

  const currentExercise =
    activeRoutine?.exercises.find((e) => e.id === selectedExerciseId) || exercise;

  const [workoutDate, setWorkoutDate] = useState(new Date().toISOString().split('T')[0]);
  const [baseSetWeightKg, setBaseSetWeightKg] = useState<number>(60);
  const [effectiveSetWeightKg, setEffectiveSetWeightKg] = useState<number>(80);
  const [rpe, setRpe] = useState<number>(9);
  const [notes, setNotes] = useState<string>('');

  if (!isOpen || !client) return null;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    addWorkoutLog({
      clientId: client.id,
      exerciseId: currentExercise?.id || 'ex-default',
      exerciseName: currentExercise?.name || 'Press de Banca',
      workoutDate,
      baseSetWeightKg,
      effectiveSetWeightKg,
      completedSets: currentExercise?.sets || 3,
      completedReps: currentExercise?.repsRange || '15, 12, 8-10',
      rpe,
      notes: notes || 'Sesión de entrenamiento de fuerza'
    });

    onClose();
  };

  return (
    <div className="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
      <div className="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full space-y-5 shadow-2xl relative">
        
        <div className="flex items-center justify-between pb-3 border-b border-slate-800">
          <div>
            <h3 className="font-bold text-base text-white flex items-center gap-2">
              <Dumbbell className="w-5 h-5 text-cyan-400" />
              Anotar Carga de Ejercicio
            </h3>
            <p className="text-xs text-slate-400">Alumno: <strong className="text-white">{client.name}</strong></p>
          </div>
          <button
            onClick={onClose}
            className="p-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4 text-xs">
          
          <div>
            <label className="block text-slate-300 font-semibold mb-1">Seleccionar Ejercicio</label>
            <select
              value={selectedExerciseId}
              onChange={(e) => setSelectedExerciseId(e.target.value)}
              className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
            >
              {activeRoutine?.exercises.map((ex) => (
                <option key={ex.id} value={ex.id}>
                  {ex.name} ({ex.muscleGroup})
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-slate-300 font-semibold mb-1">Fecha de la Sesión</label>
            <input
              type="date"
              required
              value={workoutDate}
              onChange={(e) => setWorkoutDate(e.target.value)}
              className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
            />
          </div>

          <div className="grid grid-cols-2 gap-3 p-3 bg-slate-800/60 rounded-xl border border-slate-700/60">
            <div>
              <label className="block text-slate-300 font-semibold mb-1">
                1ª Serie Base (kg)
              </label>
              <input
                type="number"
                step="0.5"
                required
                value={baseSetWeightKg}
                onChange={(e) => setBaseSetWeightKg(Number(e.target.value))}
                className="w-full bg-slate-900 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-bold text-sm text-cyan-300"
              />
              <span className="text-[10px] text-slate-400 mt-1 block">15 repeticiones</span>
            </div>

            <div>
              <label className="block text-slate-300 font-semibold mb-1">
                3ª Serie Efectiva Máx (kg)
              </label>
              <input
                type="number"
                step="0.5"
                required
                value={effectiveSetWeightKg}
                onChange={(e) => setEffectiveSetWeightKg(Number(e.target.value))}
                className="w-full bg-slate-900 text-cyan-400 py-2 px-3 rounded-xl border border-cyan-500/40 font-black text-sm"
              />
              <span className="text-[10px] text-cyan-400 mt-1 block font-bold">8-10 reps peso máximo</span>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-slate-300 font-semibold mb-1">Esfuerzo Peribido (RPE 1-10)</label>
              <input
                type="number"
                min="1"
                max="10"
                value={rpe}
                onChange={(e) => setRpe(Number(e.target.value))}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
              />
            </div>

            <div>
              <label className="block text-slate-300 font-semibold mb-1">Notas de Sesión</label>
              <input
                type="text"
                placeholder="Sensación o técnica..."
                value={notes}
                onChange={(e) => setNotes(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
              />
            </div>
          </div>

          <div className="pt-2 flex justify-end gap-2">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl transition"
            >
              Cancelar
            </button>
            <button
              type="submit"
              className="px-5 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-extrabold rounded-xl shadow transition"
            >
              Guardar Carga
            </button>
          </div>

        </form>

      </div>
    </div>
  );
};
