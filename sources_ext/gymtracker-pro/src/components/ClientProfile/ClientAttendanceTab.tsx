import React, { useState } from 'react';
import { Client } from '../../types';
import { useGym } from '../../context/GymContext';
import { CalendarCheck, Clock, CheckCircle2, Plus, Zap, Calendar as CalendarIcon } from 'lucide-react';

interface ClientAttendanceTabProps {
  client: Client;
  onOpenCheckIn: () => void;
}

export const ClientAttendanceTab: React.FC<ClientAttendanceTabProps> = ({ client, onOpenCheckIn }) => {
  const { getClientAttendances } = useGym();
  const attendances = getClientAttendances(client.id);

  // Compute attendance stats
  const totalSessions = attendances.length;
  const thisMonthSessions = attendances.filter((a) => a.attendanceDate.startsWith('2026-07')).length;
  const attendanceRatePct = Math.min(100, Math.round((thisMonthSessions / 16) * 100)); // Target 16 sessions/month

  return (
    <div className="space-y-6">
      
      {/* Header Controls */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-xl">
        <div>
          <h2 className="text-base font-bold text-white flex items-center gap-2">
            <CalendarCheck className="w-5 h-5 text-cyan-400" />
            Registro de Asistencias & Frecuencia
          </h2>
          <p className="text-xs text-slate-400 mt-0.5">
            Check-in diario de sesiones de entrenamiento personalizado.
          </p>
        </div>

        <button
          onClick={onOpenCheckIn}
          className="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5 self-start sm:self-auto"
        >
          <Plus className="w-4 h-4 stroke-[3]" />
          Registrar Asistencia Hoy
        </button>
      </div>

      {/* Attendance Stats Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div className="bg-slate-900 p-4 rounded-2xl border border-slate-800 flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center font-bold text-lg border border-cyan-500/20">
            {totalSessions}
          </div>
          <div>
            <p className="text-xs text-slate-400 font-medium">Sesiones Totales</p>
            <p className="text-lg font-black text-white">Acumuladas</p>
          </div>
        </div>

        <div className="bg-slate-900 p-4 rounded-2xl border border-slate-800 flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-lg border border-emerald-500/20">
            {thisMonthSessions}
          </div>
          <div>
            <p className="text-xs text-slate-400 font-medium">Asistencias Este Mes</p>
            <p className="text-lg font-black text-white">Julio 2026</p>
          </div>
        </div>

        <div className="bg-slate-900 p-4 rounded-2xl border border-slate-800 flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-lg border border-amber-500/20">
            {attendanceRatePct}%
          </div>
          <div>
            <p className="text-xs text-slate-400 font-medium">Cumplimiento Meta</p>
            <p className="text-xs font-bold text-emerald-400 flex items-center gap-1">
              <CheckCircle2 className="w-3.5 h-3.5" /> Meta &gt; 80% alcanzada
            </p>
          </div>
        </div>
      </div>

      {/* Attendances Table */}
      <div className="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div className="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/50">
          <h3 className="text-sm font-bold text-white flex items-center gap-2">
            <Clock className="w-4 h-4 text-cyan-400" />
            Historial Completo de Check-ins
          </h3>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs border-collapse">
            <thead>
              <tr className="bg-slate-800/60 text-slate-400 font-semibold uppercase text-[10px] border-b border-slate-800">
                <th className="py-3 px-4">Fecha</th>
                <th className="py-3 px-4">Hora Entrada / Salida</th>
                <th className="py-3 px-4">Tipo de Sesión</th>
                <th className="py-3 px-4">Duración</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/60 text-slate-300">
              {attendances.length > 0 ? (
                attendances.map((att) => (
                  <tr key={att.id} className="hover:bg-slate-800/50 transition">
                    <td className="py-3 px-4 font-bold text-white">{att.attendanceDate}</td>
                    <td className="py-3 px-4 font-medium text-slate-300">
                      {att.checkIn} {att.checkOut ? `- ${att.checkOut}` : ''}
                    </td>
                    <td className="py-3 px-4">
                      <span className="px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 font-bold text-[10px] uppercase">
                        {att.sessionType}
                      </span>
                    </td>
                    <td className="py-3 px-4 font-semibold text-slate-300">
                      {att.durationMinutes} minutos
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={4} className="py-6 text-center text-slate-500 italic">
                    Sin registros de asistencia acumulados.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

    </div>
  );
};
