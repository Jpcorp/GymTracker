import React, { useState } from 'react';
import { useGym } from '../context/GymContext';
import {
  Users,
  Activity,
  CalendarCheck,
  AlertTriangle,
  Search,
  Plus,
  ArrowRight,
  TrendingUp,
  Award,
  Zap,
  Sparkles,
  ChevronRight,
  Filter,
  CheckCircle2,
  Calendar
} from 'lucide-react';
import {
  ResponsiveContainer,
  BarChart,
  Bar,
  XAxis,
  YAxis,
  Tooltip,
  CartesianGrid,
  LineChart,
  Line
} from 'recharts';
import { getNextEvaluationDate, getBMICategory } from '../utils/calculations';

interface DashboardOverviewProps {
  onSelectClient: (clientId: string) => void;
  onOpenNewClient: () => void;
  onOpenCheckIn: () => void;
  onOpenNewMetric: () => void;
}

export const DashboardOverview: React.FC<DashboardOverviewProps> = ({
  onSelectClient,
  onOpenNewClient,
  onOpenCheckIn,
  onOpenNewMetric
}) => {
  const { clients, inBodyMetrics, attendances, evaluations, getLatestInBody } = useGym();
  const [filterStatus, setFilterStatus] = useState<'all' | 'active' | 'paused' | 'inactive'>('all');
  const [searchTerm, setSearchTerm] = useState('');

  const activeClients = clients.filter((c) => c.status === 'active');
  const totalClientsCount = clients.length;
  const activeCount = activeClients.length;

  // Attendance today
  const todayStr = new Date().toISOString().split('T')[0];
  const todayAttendanceCount = attendances.filter((a) => a.attendanceDate === todayStr).length;

  // Evaluated this month
  const totalEvaluationsCount = evaluations.length;

  // Upcoming 21-day evaluation alerts
  const clientsWithEvalAlerts = clients.map((client) => {
    const clientEvals = evaluations.filter((e) => e.clientId === client.id);
    const lastEvalNum = clientEvals.length;
    const { nextDate, daysRemaining, evalNumber } = getNextEvaluationDate(client.startDate, lastEvalNum);
    return {
      client,
      nextDate,
      daysRemaining,
      evalNumber
    };
  }).filter((item) => item.daysRemaining <= 5);

  // Filter clients
  const filteredClients = clients.filter((client) => {
    const matchesStatus = filterStatus === 'all' || client.status === filterStatus;
    const matchesSearch =
      client.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      client.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
      client.phone.includes(searchTerm);
    return matchesStatus && matchesSearch;
  });

  // Mock chart data for Gym performance over months
  const monthlyData = [
    { month: 'Mayo', clientes: 62, asistencias: 420, evals: 28 },
    { month: 'Junio', clientes: 74, asistencias: 580, evals: 36 },
    { month: 'Julio', clientes: 87, asistencias: 710, evals: 45 }
  ];

  return (
    <div className="space-y-6 pb-12">
      
      {/* Top Banner & Quick Controls */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 p-6 rounded-2xl border border-slate-800 shadow-xl">
        <div>
          <div className="flex items-center gap-2 mb-1">
            <span className="px-2.5 py-0.5 rounded-full bg-cyan-500/20 text-cyan-400 text-xs font-semibold border border-cyan-500/30 flex items-center gap-1">
              <Sparkles className="w-3 h-3" /> Panel del Entrenador
            </span>
            <span className="text-slate-400 text-xs">• 100 Cupos Máximos</span>
          </div>
          <h1 className="text-2xl sm:text-3xl font-black text-white tracking-tight">
            Gestión de Clientes & Evaluaciones (21 Días)
          </h1>
          <p className="text-slate-400 text-xs sm:text-sm mt-1 max-w-2xl">
            Sincronización en tiempo real de mediciones InBody, progresión de cargas efectivas y galerías corporales en 4 poses.
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-3">
          <button
            onClick={onOpenCheckIn}
            className="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl border border-slate-700 transition flex items-center gap-2 shadow-sm"
          >
            <CalendarCheck className="w-4 h-4 text-cyan-400" />
            Check-In Rápido
          </button>
          <button
            onClick={onOpenNewClient}
            className="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-extrabold text-xs rounded-xl shadow-lg shadow-cyan-500/20 transition flex items-center gap-2"
          >
            <Plus className="w-4 h-4 stroke-[3]" />
            Nuevo Cliente
          </button>
        </div>
      </div>

      {/* KPI Stats Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {/* Active Clients KPI */}
        <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden group">
          <div className="flex items-center justify-between">
            <span className="text-xs font-medium text-slate-400">Clientes Activos</span>
            <div className="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center border border-cyan-500/20">
              <Users className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-2">
            <span className="text-3xl font-black text-white">{activeCount}</span>
            <span className="text-xs text-slate-400 font-medium">/ 100 cupos</span>
          </div>
          {/* Progress bar */}
          <div className="mt-3 w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
            <div
              className="bg-gradient-to-r from-cyan-500 to-blue-500 h-1.5 rounded-full"
              style={{ width: `${(activeCount / 100) * 100}%` }}
            ></div>
          </div>
          <p className="mt-2 text-[11px] text-slate-400 flex items-center gap-1">
            <TrendingUp className="w-3 h-3 text-emerald-400" /> Capacidad operativa al {activeCount}%
          </p>
        </div>

        {/* 21-Day Evaluations KPI */}
        <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden">
          <div className="flex items-center justify-between">
            <span className="text-xs font-medium text-slate-400">Evaluaciones (21 Días)</span>
            <div className="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20">
              <Award className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-2">
            <span className="text-3xl font-black text-white">{totalEvaluationsCount}</span>
            <span className="text-xs text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded-full">
              Automáticas
            </span>
          </div>
          <p className="mt-3 text-[11px] text-slate-400 flex items-center gap-1">
            <CheckCircle2 className="w-3 h-3 text-blue-400" /> Hitos comparativos generados
          </p>
        </div>

        {/* Attendance Today KPI */}
        <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden">
          <div className="flex items-center justify-between">
            <span className="text-xs font-medium text-slate-400">Asistencia Hoy</span>
            <div className="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
              <CalendarCheck className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-2">
            <span className="text-3xl font-black text-white">{todayAttendanceCount}</span>
            <span className="text-xs text-slate-400">asistencias</span>
          </div>
          <p className="mt-3 text-[11px] text-slate-400 flex items-center gap-1">
            <Zap className="w-3 h-3 text-amber-400" /> Registro directo con check-in
          </p>
        </div>

        {/* Pending Alerts KPI */}
        <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden">
          <div className="flex items-center justify-between">
            <span className="text-xs font-medium text-slate-400">Alertas de Control</span>
            <div className="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
              <AlertTriangle className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-2">
            <span className="text-3xl font-black text-amber-400">{clientsWithEvalAlerts.length}</span>
            <span className="text-xs text-amber-400/90 font-medium">próximos a 21 días</span>
          </div>
          <p className="mt-3 text-[11px] text-slate-400 flex items-center gap-1">
            <Calendar className="w-3 h-3 text-amber-400" /> Programados por ciclo
          </p>
        </div>

      </div>

      {/* 21-Day Evaluation Alert Banner Section */}
      {clientsWithEvalAlerts.length > 0 && (
        <div className="bg-gradient-to-r from-amber-950/40 via-slate-900 to-amber-950/20 border border-amber-500/30 p-5 rounded-2xl shadow-xl">
          <div className="flex items-center justify-between gap-2 mb-3">
            <div className="flex items-center gap-2">
              <Zap className="w-5 h-5 text-amber-400 animate-pulse" />
              <h3 className="text-sm font-bold text-white">
                Próximos Controles de 21 Días Pendientes ({clientsWithEvalAlerts.length})
              </h3>
            </div>
            <span className="text-[11px] text-amber-300 bg-amber-500/20 px-2.5 py-0.5 rounded-full border border-amber-500/30">
              Ciclo Automático
            </span>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            {clientsWithEvalAlerts.map(({ client, daysRemaining, evalNumber, nextDate }) => (
              <div
                key={client.id}
                className="bg-slate-900/90 border border-slate-800 p-3.5 rounded-xl flex items-center justify-between hover:border-amber-500/40 transition group"
              >
                <div className="flex items-center gap-3">
                  <img
                    src={client.profilePhoto || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100'}
                    alt={client.name}
                    className="w-10 h-10 rounded-full object-cover border border-slate-700"
                  />
                  <div>
                    <p className="text-xs font-bold text-white group-hover:text-amber-300 transition">
                      {client.name}
                    </p>
                    <p className="text-[11px] text-slate-400">
                      Evaluación #{evalNumber} • {nextDate}
                    </p>
                    <span className="inline-block mt-1 text-[10px] font-semibold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded">
                      {daysRemaining <= 0 ? '¡HOY le toca InBody!' : `Faltan ${daysRemaining} días`}
                    </span>
                  </div>
                </div>

                <button
                  onClick={() => {
                    onSelectClient(client.id);
                    onOpenNewMetric();
                  }}
                  className="px-2.5 py-1.5 bg-amber-500 text-slate-950 font-bold text-[11px] rounded-lg hover:bg-amber-400 transition shrink-0"
                >
                  Tomar InBody
                </button>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Gym Overview Performance Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {/* Active Clients Evolution Bar Chart */}
        <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-lg">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h3 className="text-sm font-bold text-white">Retención & Crecimiento Mensual</h3>
              <p className="text-[11px] text-slate-400">Evolución de alumnos activos en la plataforma</p>
            </div>
            <span className="text-xs font-bold text-cyan-400 bg-cyan-500/10 px-2.5 py-1 rounded-lg border border-cyan-500/20">
              87 Alumnos
            </span>
          </div>

          <div className="h-60 w-full">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={monthlyData} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="#334155" opacity={0.5} />
                <XAxis dataKey="month" stroke="#94a3b8" fontSize={12} />
                <YAxis stroke="#94a3b8" fontSize={12} />
                <Tooltip
                  contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '12px', fontSize: '12px' }}
                />
                <Bar dataKey="clientes" name="Clientes Activos" fill="#06b6d4" radius={[6, 6, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Monthly Attendance Volume Line Chart */}
        <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-lg">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h3 className="text-sm font-bold text-white">Volumen de Asistencias & Controles</h3>
              <p className="text-[11px] text-slate-400">Total asistencias registradas por mes</p>
            </div>
            <span className="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20">
              710 Sesiones
            </span>
          </div>

          <div className="h-60 w-full">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={monthlyData} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="#334155" opacity={0.5} />
                <XAxis dataKey="month" stroke="#94a3b8" fontSize={12} />
                <YAxis stroke="#94a3b8" fontSize={12} />
                <Tooltip
                  contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '12px', fontSize: '12px' }}
                />
                <Line type="monotone" dataKey="asistencias" name="Asistencias" stroke="#10b981" strokeWidth={3} dot={{ r: 5 }} />
                <Line type="monotone" dataKey="evals" name="Evaluaciones 21d" stroke="#3b82f6" strokeWidth={2} strokeDasharray="4 4" />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </div>

      </div>

      {/* Main Client List Section */}
      <div className="bg-slate-900 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
        
        {/* Table Header & Controls */}
        <div className="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900/50">
          <div>
            <h2 className="text-base font-bold text-white flex items-center gap-2">
              <Users className="w-5 h-5 text-cyan-400" />
              Listado General de Alumnos ({filteredClients.length})
            </h2>
            <p className="text-xs text-slate-400 mt-0.5">
              Acceso a métricas InBody, fotos en 4 poses y progresión de cargas
            </p>
          </div>

          {/* Filters & Search */}
          <div className="flex flex-wrap items-center gap-3">
            
            {/* Search Input */}
            <div className="relative flex-1 sm:w-64">
              <Search className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                placeholder="Buscar por nombre o correo..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full bg-slate-800 text-xs text-slate-200 placeholder-slate-400 pl-9 pr-3 py-1.5 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500"
              />
            </div>

            {/* Filter status tabs */}
            <div className="flex items-center gap-1 bg-slate-800 p-1 rounded-xl border border-slate-700 text-xs font-medium">
              <button
                onClick={() => setFilterStatus('all')}
                className={`px-2.5 py-1 rounded-lg transition ${
                  filterStatus === 'all'
                    ? 'bg-cyan-500 text-slate-950 font-bold'
                    : 'text-slate-400 hover:text-white'
                }`}
              >
                Todos
              </button>
              <button
                onClick={() => setFilterStatus('active')}
                className={`px-2.5 py-1 rounded-lg transition ${
                  filterStatus === 'active'
                    ? 'bg-cyan-500 text-slate-950 font-bold'
                    : 'text-slate-400 hover:text-white'
                }`}
              >
                Activos
              </button>
              <button
                onClick={() => setFilterStatus('paused')}
                className={`px-2.5 py-1 rounded-lg transition ${
                  filterStatus === 'paused'
                    ? 'bg-amber-500 text-slate-950 font-bold'
                    : 'text-slate-400 hover:text-white'
                }`}
              >
                Pausados
              </button>
            </div>

          </div>
        </div>

        {/* Client Table */}
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse text-xs">
            <thead>
              <tr className="bg-slate-800/60 text-slate-400 font-semibold uppercase tracking-wider text-[10px] border-b border-slate-800">
                <th className="py-3 px-4">Alumno</th>
                <th className="py-3 px-4">Entrenador & Inicio</th>
                <th className="py-3 px-4">Último Peso / InBody</th>
                <th className="py-3 px-4">% Grasa & BMI</th>
                <th className="py-3 px-4">Próxima Eval (21d)</th>
                <th className="py-3 px-4 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/60 text-slate-300">
              {filteredClients.length > 0 ? (
                filteredClients.map((client) => {
                  const latestInBody = getLatestInBody(client.id);
                  const clientEvals = evaluations.filter((e) => e.clientId === client.id);
                  const { daysRemaining, evalNumber, nextDate } = getNextEvaluationDate(
                    client.startDate,
                    clientEvals.length
                  );
                  const bmiCat = latestInBody ? getBMICategory(latestInBody.bmi) : null;

                  return (
                    <tr
                      key={client.id}
                      onClick={() => onSelectClient(client.id)}
                      className="hover:bg-slate-800/50 cursor-pointer transition group"
                    >
                      {/* Client Avatar & Name */}
                      <td className="py-3 px-4">
                        <div className="flex items-center gap-3">
                          <img
                            src={
                              client.profilePhoto ||
                              'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100'
                            }
                            alt={client.name}
                            className="w-9 h-9 rounded-full object-cover border border-slate-700 group-hover:border-cyan-400 transition"
                          />
                          <div>
                            <p className="font-bold text-white group-hover:text-cyan-400 transition">
                              {client.name}
                            </p>
                            <p className="text-[11px] text-slate-400">{client.goal}</p>
                          </div>
                        </div>
                      </td>

                      {/* Trainer & Start */}
                      <td className="py-3 px-4">
                        <p className="font-medium text-slate-200">{client.trainerName}</p>
                        <p className="text-[10px] text-slate-400">Inicio: {client.startDate}</p>
                      </td>

                      {/* Weight InBody */}
                      <td className="py-3 px-4">
                        {latestInBody ? (
                          <div>
                            <span className="font-bold text-white text-sm">
                              {latestInBody.weightKg} kg
                            </span>
                            <p className="text-[10px] text-slate-400">
                              Visceral: {latestInBody.visceralFat}
                            </p>
                          </div>
                        ) : (
                          <span className="text-slate-500 italic">Sin InBody</span>
                        )}
                      </td>

                      {/* Fat % & BMI */}
                      <td className="py-3 px-4">
                        {latestInBody ? (
                          <div className="space-y-0.5">
                            <span className="font-semibold text-cyan-300">
                              {latestInBody.bodyFatPercentage}% Grasa
                            </span>
                            {bmiCat && (
                              <span className={`block text-[10px] font-medium px-1.5 py-0.5 rounded border w-fit ${bmiCat.color}`}>
                                BMI {latestInBody.bmi} ({bmiCat.label})
                              </span>
                            )}
                          </div>
                        ) : (
                          <span className="text-slate-500 italic">--</span>
                        )}
                      </td>

                      {/* 21-Day Evaluation Badge */}
                      <td className="py-3 px-4">
                        <div className="space-y-1">
                          <span className="inline-block text-[11px] font-bold text-slate-200">
                            Eval #{evalNumber}
                          </span>
                          <p
                            className={`text-[10px] font-bold px-2 py-0.5 rounded-full w-fit ${
                              daysRemaining <= 3
                                ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30'
                                : 'bg-slate-800 text-slate-300'
                            }`}
                          >
                            {daysRemaining <= 0
                              ? '¡Evaluar hoy!'
                              : `En ${daysRemaining} días (${nextDate})`}
                          </p>
                        </div>
                      </td>

                      {/* Actions */}
                      <td className="py-3 px-4 text-right" onClick={(e) => e.stopPropagation()}>
                        <div className="flex items-center justify-end gap-2">
                          <button
                            onClick={() => onSelectClient(client.id)}
                            className="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-cyan-400 text-xs font-semibold flex items-center gap-1"
                          >
                            <span>Ver Perfil</span>
                            <ChevronRight className="w-3.5 h-3.5" />
                          </button>
                        </div>
                      </td>

                    </tr>
                  );
                })
              ) : (
                <tr>
                  <td colSpan={6} className="py-8 text-center text-slate-400">
                    No se encontraron alumnos con los filtros seleccionados.
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
