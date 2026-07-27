import React from 'react';
import { Client } from '../../types';
import { useGym } from '../../context/GymContext';
import { Activity, TrendingDown, Dumbbell, Ruler, Award } from 'lucide-react';
import {
  ResponsiveContainer,
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  BarChart,
  Bar
} from 'recharts';

interface ClientChartsTabProps {
  client: Client;
}

export const ClientChartsTab: React.FC<ClientChartsTabProps> = ({ client }) => {
  const { getClientInBodyHistory, getClientMeasurementHistory, getClientWorkoutLogs } = useGym();

  const inBodyHistory = getClientInBodyHistory(client.id);
  const measurementHistory = getClientMeasurementHistory(client.id);
  const workoutLogs = getClientWorkoutLogs(client.id);

  // InBody Chart Data
  const weightChartData = inBodyHistory.map((item) => ({
    date: item.recordedAt,
    peso: item.weightKg,
    grasa: item.bodyFatPercentage,
    bmi: item.bmi,
    basal: item.basalKcal
  }));

  // Max Effective Load Chart Data (Press de Banca, Sentadilla)
  const benchLogs = workoutLogs
    .filter((l) => l.exerciseName.toLowerCase().includes('banca'))
    .sort((a, b) => new Date(a.workoutDate).getTime() - new Date(b.workoutDate).getTime());

  const squatLogs = workoutLogs
    .filter((l) => l.exerciseName.toLowerCase().includes('sentadilla'))
    .sort((a, b) => new Date(a.workoutDate).getTime() - new Date(b.workoutDate).getTime());

  const loadChartData = benchLogs.map((log) => ({
    date: log.workoutDate,
    bancaEfectiva: log.effectiveSetWeightKg,
    bancaBase: log.baseSetWeightKg
  }));

  // Body Measurements Chart Data
  const measChartData = measurementHistory.map((m) => ({
    date: m.recordedAt,
    cintura: m.waistCm,
    cadera: m.hipsCm,
    pecho: m.chestCm,
    brazo: m.rightArmCm,
    muslo: m.rightThighCm
  }));

  return (
    <div className="space-y-6">
      
      {/* Header Bar */}
      <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-xl">
        <h2 className="text-base font-bold text-white flex items-center gap-2">
          <Activity className="w-5 h-5 text-cyan-400" />
          Gráficos e Indicadores de Progresión Corporal
        </h2>
        <p className="text-xs text-slate-400 mt-1">
          Visualización de tendencias temporales para peso, % de grasa, perímetro abdominal y progresión de fuerza.
        </p>
      </div>

      {/* 2 Grid Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {/* Weight & Body Fat Line Chart */}
        <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-xl space-y-4">
          <div className="flex items-center justify-between border-b border-slate-800 pb-3">
            <div>
              <h3 className="text-sm font-bold text-white">Evolución de Peso (kg) vs % Grasa</h3>
              <p className="text-[11px] text-slate-400">Tendencia InBody en controles periódicos</p>
            </div>
            <span className="text-xs font-bold text-cyan-400 bg-cyan-500/10 px-2.5 py-1 rounded-lg">
              InBody
            </span>
          </div>

          <div className="h-64 w-full">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={weightChartData} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="#334155" opacity={0.5} />
                <XAxis dataKey="date" stroke="#94a3b8" fontSize={11} />
                <YAxis yAxisId="left" stroke="#06b6d4" fontSize={11} domain={['auto', 'auto']} />
                <YAxis yAxisId="right" orientation="right" stroke="#f59e0b" fontSize={11} domain={['auto', 'auto']} />
                <Tooltip
                  contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '12px', fontSize: '12px' }}
                />
                <Legend wrapperStyle={{ fontSize: '11px', paddingTop: '10px' }} />
                <Line yAxisId="left" type="monotone" dataKey="peso" name="Peso (kg)" stroke="#06b6d4" strokeWidth={3} dot={{ r: 5 }} />
                <Line yAxisId="right" type="monotone" dataKey="grasa" name="% Grasa Corporal" stroke="#f59e0b" strokeWidth={2} strokeDasharray="4 4" dot={{ r: 4 }} />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Max Effective Load Progression Line Chart */}
        <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-xl space-y-4">
          <div className="flex items-center justify-between border-b border-slate-800 pb-3">
            <div>
              <h3 className="text-sm font-bold text-white">Progresión de Fuerza: Press de Banca</h3>
              <p className="text-[11px] text-slate-400">1ª Serie Base vs 3ª Serie Efectiva Máxima (kg)</p>
            </div>
            <span className="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg">
              Cargas
            </span>
          </div>

          <div className="h-64 w-full">
            {loadChartData.length > 0 ? (
              <ResponsiveContainer width="100%" height="100%">
                <LineChart data={loadChartData} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
                  <CartesianGrid strokeDasharray="3 3" stroke="#334155" opacity={0.5} />
                  <XAxis dataKey="date" stroke="#94a3b8" fontSize={11} />
                  <YAxis stroke="#10b981" fontSize={11} domain={['auto', 'auto']} />
                  <Tooltip
                    contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '12px', fontSize: '12px' }}
                  />
                  <Legend wrapperStyle={{ fontSize: '11px', paddingTop: '10px' }} />
                  <Line type="monotone" dataKey="bancaEfectiva" name="3ª Serie Efectiva Máx (8-10 reps)" stroke="#10b981" strokeWidth={3} dot={{ r: 5 }} />
                  <Line type="monotone" dataKey="bancaBase" name="1ª Serie Base (15 reps)" stroke="#3b82f6" strokeWidth={2} strokeDasharray="3 3" />
                </LineChart>
              </ResponsiveContainer>
            ) : (
              <div className="h-full flex flex-col items-center justify-center text-slate-500 text-xs">
                Aún no hay suficientes registros de cargas para generar gráfico.
              </div>
            )}
          </div>
        </div>

      </div>

      {/* Body Measurements Bar Chart */}
      <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-xl space-y-4">
        <div className="flex items-center justify-between border-b border-slate-800 pb-3">
          <div>
            <h3 className="text-sm font-bold text-white">Evolución de Medidas Antropométricas (cm)</h3>
            <p className="text-[11px] text-slate-400">Comparativa de Cintura, Cadera, Pecho y Brazos</p>
          </div>
          <span className="text-xs font-bold text-purple-400 bg-purple-500/10 px-2.5 py-1 rounded-lg">
            Antropometría
          </span>
        </div>

        <div className="h-64 w-full">
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={measChartData} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
              <CartesianGrid strokeDasharray="3 3" stroke="#334155" opacity={0.5} />
              <XAxis dataKey="date" stroke="#94a3b8" fontSize={11} />
              <YAxis stroke="#94a3b8" fontSize={11} domain={['auto', 'auto']} />
              <Tooltip
                contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '12px', fontSize: '12px' }}
              />
              <Legend wrapperStyle={{ fontSize: '11px', paddingTop: '10px' }} />
              <Bar dataKey="cintura" name="Cintura (cm)" fill="#06b6d4" radius={[4, 4, 0, 0]} />
              <Bar dataKey="cadera" name="Cadera (cm)" fill="#3b82f6" radius={[4, 4, 0, 0]} />
              <Bar dataKey="pecho" name="Pecho (cm)" fill="#10b981" radius={[4, 4, 0, 0]} />
              <Bar dataKey="brazo" name="Brazo (cm)" fill="#a855f7" radius={[4, 4, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

    </div>
  );
};
