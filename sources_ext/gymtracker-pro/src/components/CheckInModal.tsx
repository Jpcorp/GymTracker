import React, { useState } from 'react';
import { useGym } from '../context/GymContext';
import { CalendarCheck, X, Users } from 'lucide-react';
import { SessionType } from '../types';

interface CheckInModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export const CheckInModal: React.FC<CheckInModalProps> = ({ isOpen, onClose }) => {
  const { clients, addAttendance } = useGym();

  const [selectedClientId, setSelectedClientId] = useState<string>(clients[0]?.id || '');
  const [attendanceDate, setAttendanceDate] = useState(new Date().toISOString().split('T')[0]);
  const [checkIn, setCheckIn] = useState('09:00');
  const [sessionType, setSessionType] = useState<SessionType>('personal');
  const [durationMinutes, setDurationMinutes] = useState(75);

  if (!isOpen) return null;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    if (!selectedClientId) return;

    addAttendance({
      clientId: selectedClientId,
      attendanceDate,
      checkIn,
      sessionType,
      durationMinutes
    });

    onClose();
  };

  return (
    <div className="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
      <div className="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full space-y-5 shadow-2xl relative">
        
        <div className="flex items-center justify-between pb-3 border-b border-slate-800">
          <div className="flex items-center gap-2">
            <CalendarCheck className="w-5 h-5 text-cyan-400" />
            <h3 className="font-bold text-base text-white">Check-in de Asistencia Alumno</h3>
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
            <label className="block text-slate-300 font-semibold mb-1">Seleccionar Alumno</label>
            <select
              value={selectedClientId}
              onChange={(e) => setSelectedClientId(e.target.value)}
              className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-bold"
            >
              {clients.map((c) => (
                <option key={c.id} value={c.id}>
                  {c.name} ({c.status})
                </option>
              ))}
            </select>
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-slate-300 font-semibold mb-1">Fecha</label>
              <input
                type="date"
                required
                value={attendanceDate}
                onChange={(e) => setAttendanceDate(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
              />
            </div>

            <div>
              <label className="block text-slate-300 font-semibold mb-1">Hora Entrada</label>
              <input
                type="time"
                required
                value={checkIn}
                onChange={(e) => setCheckIn(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
              />
            </div>
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-slate-300 font-semibold mb-1">Tipo de Sesión</label>
              <select
                value={sessionType}
                onChange={(e) => setSessionType(e.target.value as SessionType)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
              >
                <option value="personal">Personalizado 1-a-1</option>
                <option value="group">Grupo Reducido</option>
                <option value="free">Libre Guiado</option>
              </select>
            </div>

            <div>
              <label className="block text-slate-300 font-semibold mb-1">Duración (min)</label>
              <input
                type="number"
                value={durationMinutes}
                onChange={(e) => setDurationMinutes(Number(e.target.value))}
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
              Registrar Check-in
            </button>
          </div>

        </form>

      </div>
    </div>
  );
};
