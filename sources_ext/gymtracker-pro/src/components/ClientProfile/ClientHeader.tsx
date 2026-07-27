import React from 'react';
import { Client } from '../../types';
import { useGym } from '../../context/GymContext';
import {
  Calendar,
  Goal,
  UserCheck,
  Plus,
  FileText,
  Activity,
  Camera,
  Dumbbell,
  Clock,
  Edit2,
  Trash2,
  Sparkles,
  Award
} from 'lucide-react';
import { getNextEvaluationDate } from '../../utils/calculations';

interface ClientHeaderProps {
  client: Client;
  activeTab: string;
  setActiveTab: (tab: string) => void;
  onOpenNewMetric: () => void;
  onOpenNewWorkoutLog: () => void;
  onOpenPdfReport: () => void;
  onOpenEditClient: () => void;
}

export const ClientHeader: React.FC<ClientHeaderProps> = ({
  client,
  activeTab,
  setActiveTab,
  onOpenNewMetric,
  onOpenNewWorkoutLog,
  onOpenPdfReport,
  onOpenEditClient
}) => {
  const { getLatestInBody, evaluations, deleteClient } = useGym();
  const latestInBody = getLatestInBody(client.id);
  const clientEvals = evaluations.filter((e) => e.clientId === client.id);
  
  const { daysRemaining, evalNumber } = getNextEvaluationDate(client.startDate, clientEvals.length);

  // Calculate total training days
  const start = new Date(client.startDate);
  const today = new Date();
  const daysTraining = Math.max(0, Math.floor((today.getTime() - start.getTime()) / (1000 * 60 * 60 * 24)));
  const monthsTraining = Math.floor(daysTraining / 30);

  const tabs = [
    { id: 'evaluations', label: 'Evaluaciones (21 Días)', icon: Award, badge: `${clientEvals.length}` },
    { id: 'metrics', label: 'InBody & Medidas', icon: Activity },
    { id: 'photos', label: 'Fotos 4 Poses', icon: Camera },
    { id: 'routines', label: 'Rutina & Cargas', icon: Dumbbell },
    { id: 'charts', label: 'Gráficos Progreso', icon: Activity },
    { id: 'attendance', label: 'Asistencia', icon: Clock },
    { id: 'wellness', label: 'Bienestar & Nutrición', icon: Sparkles }
  ];

  return (
    <div className="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
      
      {/* Client Overview Card */}
      <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        
        {/* Left: Avatar & Basic Info */}
        <div className="flex items-start sm:items-center gap-4">
          <img
            src={
              client.profilePhoto ||
              'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=300'
            }
            alt={client.name}
            className="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border-2 border-cyan-500/30 shadow-lg shrink-0"
          />

          <div className="space-y-1">
            <div className="flex flex-wrap items-center gap-2">
              <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight">
                {client.name}
              </h1>
              <span
                className={`text-[10px] font-bold px-2 py-0.5 rounded-full border ${
                  client.status === 'active'
                    ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'
                    : client.status === 'paused'
                    ? 'bg-amber-500/20 text-amber-400 border-amber-500/30'
                    : 'bg-rose-500/20 text-rose-400 border-rose-500/30'
                }`}
              >
                {client.status.toUpperCase()}
              </span>
            </div>

            <p className="text-xs text-slate-400 flex items-center gap-1.5">
              <Goal className="w-3.5 h-3.5 text-cyan-400 shrink-0" />
              <span className="font-semibold text-slate-200">{client.goal}</span>
            </p>

            <div className="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400 pt-1">
              <span className="flex items-center gap-1">
                <Calendar className="w-3.5 h-3.5 text-slate-500" />
                Inicio: {client.startDate} ({monthsTraining > 0 ? `${monthsTraining} meses` : `${daysTraining} días`})
              </span>
              <span className="flex items-center gap-1">
                <UserCheck className="w-3.5 h-3.5 text-slate-500" />
                Entrenador: <strong className="text-slate-300">{client.trainerName}</strong>
              </span>
            </div>
          </div>
        </div>

        {/* Center: Latest InBody Quick Badge */}
        {latestInBody && (
          <div className="bg-slate-800/80 p-3.5 rounded-xl border border-slate-700/80 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center sm:text-left">
            <div>
              <p className="text-[10px] text-slate-400 font-medium">Último Peso</p>
              <p className="text-lg font-black text-white">{latestInBody.weightKg} kg</p>
            </div>
            <div>
              <p className="text-[10px] text-slate-400 font-medium">% Grasa</p>
              <p className="text-lg font-black text-cyan-400">{latestInBody.bodyFatPercentage}%</p>
            </div>
            <div>
              <p className="text-[10px] text-slate-400 font-medium">Kcal Basal</p>
              <p className="text-lg font-black text-emerald-400">{latestInBody.basalKcal}</p>
            </div>
            <div>
              <p className="text-[10px] text-slate-400 font-medium">Visceral</p>
              <p className="text-lg font-black text-amber-400">{latestInBody.visceralFat}</p>
            </div>
          </div>
        )}

        {/* Right: Actions */}
        <div className="flex flex-wrap items-center gap-2">
          <button
            onClick={onOpenNewMetric}
            className="px-3 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5"
          >
            <Plus className="w-3.5 h-3.5 stroke-[3]" />
            Ingresar InBody
          </button>

          <button
            onClick={onOpenNewWorkoutLog}
            className="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5"
          >
            <Dumbbell className="w-3.5 h-3.5 text-cyan-400" />
            Registrar Carga
          </button>

          <button
            onClick={onOpenPdfReport}
            className="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5"
            title="Generar Reporte Imprimible de Evaluación"
          >
            <FileText className="w-3.5 h-3.5 text-emerald-400" />
            PDF
          </button>

          <button
            onClick={onOpenEditClient}
            className="p-2 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-xl border border-slate-700 transition"
            title="Editar Alumno"
          >
            <Edit2 className="w-4 h-4" />
          </button>

          <button
            onClick={() => {
              if (confirm(`¿Seguro que deseas eliminar el cliente ${client.name}?`)) {
                deleteClient(client.id);
              }
            }}
            className="p-2 bg-slate-800 hover:bg-rose-950 text-slate-400 hover:text-rose-400 rounded-xl border border-slate-700 transition"
            title="Eliminar Alumno"
          >
            <Trash2 className="w-4 h-4" />
          </button>
        </div>

      </div>

      {/* 21-Day Next Evaluation Banner */}
      <div className="bg-slate-800/50 p-3 rounded-xl border border-slate-700 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div className="flex items-center gap-2">
          <Award className="w-4 h-4 text-amber-400" />
          <span className="font-semibold text-slate-200">
            Próximo Control de 21 Días (# {evalNumber}):
          </span>
          <span
            className={`font-bold px-2 py-0.5 rounded-full ${
              daysRemaining <= 3
                ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                : 'bg-cyan-500/10 text-cyan-400'
            }`}
          >
            {daysRemaining <= 0 ? '¡Corresponde evaluar hoy!' : `Faltan ${daysRemaining} días`}
          </span>
        </div>

        <span className="text-[11px] text-slate-400">
          Evaluaciones registradas: <strong className="text-white">{clientEvals.length}</strong>
        </span>
      </div>

      {/* Navigation Tabs */}
      <div className="flex items-center gap-1 overflow-x-auto pb-1 border-b border-slate-800 scrollbar-none">
        {tabs.map((tab) => {
          const Icon = tab.icon;
          const isActive = activeTab === tab.id;

          return (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap ${
                isActive
                  ? 'bg-cyan-500 text-slate-950 shadow-md'
                  : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800'
              }`}
            >
              <Icon className="w-3.5 h-3.5" />
              <span>{tab.label}</span>
              {tab.badge && (
                <span
                  className={`text-[10px] px-1.5 py-0.2 rounded-full ${
                    isActive ? 'bg-slate-950 text-cyan-300' : 'bg-slate-800 text-slate-300'
                  }`}
                >
                  {tab.badge}
                </span>
              )}
            </button>
          );
        })}
      </div>

    </div>
  );
};
