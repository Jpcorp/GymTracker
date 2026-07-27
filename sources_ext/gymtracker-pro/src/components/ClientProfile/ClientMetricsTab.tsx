import React from 'react';
import { Client } from '../../types';
import { useGym } from '../../context/GymContext';
import {
  Activity,
  Plus,
  Scale,
  Percent,
  Flame,
  HeartPulse,
  TrendingDown,
  TrendingUp,
  Ruler,
  Check
} from 'lucide-react';
import {
  calculatePercentageChange,
  calculateAbsoluteDifference,
  getBMICategory,
  getVisceralFatCategory,
  formatTrend
} from '../../utils/calculations';

interface ClientMetricsTabProps {
  client: Client;
  onOpenNewMetric: () => void;
}

export const ClientMetricsTab: React.FC<ClientMetricsTabProps> = ({ client, onOpenNewMetric }) => {
  const { getClientInBodyHistory, getClientMeasurementHistory } = useGym();
  
  const inBodyHistory = getClientInBodyHistory(client.id);
  const measurementHistory = getClientMeasurementHistory(client.id);

  const latestInBody = inBodyHistory[inBodyHistory.length - 1];
  const previousInBody = inBodyHistory.length > 1 ? inBodyHistory[inBodyHistory.length - 2] : null;

  const latestMeas = measurementHistory[measurementHistory.length - 1];
  const previousMeas = measurementHistory.length > 1 ? measurementHistory[measurementHistory.length - 2] : null;

  const bmiCategory = latestInBody ? getBMICategory(latestInBody.bmi) : null;
  const visceralCategory = latestInBody ? getVisceralFatCategory(latestInBody.visceralFat) : null;

  return (
    <div className="space-y-6">
      
      {/* Header Bar */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 p-4 rounded-2xl border border-slate-800">
        <div>
          <h2 className="text-base font-bold text-white flex items-center gap-2">
            <Activity className="w-5 h-5 text-cyan-400" />
            Métricas InBody & Antropometría
          </h2>
          <p className="text-xs text-slate-400 mt-0.5">
            Registro de composición corporal con báscula InBody e historial de mediciones en cm.
          </p>
        </div>

        <button
          onClick={onOpenNewMetric}
          className="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5 self-start sm:self-auto"
        >
          <Plus className="w-4 h-4 stroke-[3]" />
          Nueva Medición InBody
        </button>
      </div>

      {/* Latest InBody Cards Grid */}
      {latestInBody ? (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          
          {/* Weight Card */}
          <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 space-y-2">
            <div className="flex items-center justify-between text-slate-400 text-xs">
              <span className="flex items-center gap-1.5 font-medium">
                <Scale className="w-4 h-4 text-cyan-400" /> Peso Corporal
              </span>
              <span className="text-[10px] text-slate-500">{latestInBody.recordedAt}</span>
            </div>

            <div className="flex items-baseline gap-2">
              <span className="text-3xl font-black text-white">{latestInBody.weightKg}</span>
              <span className="text-xs text-slate-400 font-bold">kg</span>
            </div>

            {previousInBody && (
              <div className="pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span className="text-slate-400 text-[11px]">vs Anterior ({previousInBody.weightKg}kg):</span>
                {(() => {
                  const diff = calculateAbsoluteDifference(latestInBody.weightKg, previousInBody.weightKg);
                  const trend = formatTrend(diff, true); // weight drop is good usually
                  return (
                    <span className={`font-bold ${trend.color}`}>
                      {trend.symbol} {trend.text} kg
                    </span>
                  );
                })()}
              </div>
            )}
          </div>

          {/* Body Fat Card */}
          <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 space-y-2">
            <div className="flex items-center justify-between text-slate-400 text-xs">
              <span className="flex items-center gap-1.5 font-medium">
                <Percent className="w-4 h-4 text-cyan-400" /> Grasa Corporal
              </span>
              <span className="text-[10px] text-slate-500">{latestInBody.recordedAt}</span>
            </div>

            <div className="flex items-baseline gap-2">
              <span className="text-3xl font-black text-cyan-400">{latestInBody.bodyFatPercentage}</span>
              <span className="text-xs text-slate-400 font-bold">%</span>
            </div>

            {previousInBody && (
              <div className="pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span className="text-slate-400 text-[11px]">vs Anterior ({previousInBody.bodyFatPercentage}%):</span>
                {(() => {
                  const diff = calculateAbsoluteDifference(latestInBody.bodyFatPercentage, previousInBody.bodyFatPercentage);
                  const trend = formatTrend(diff, true);
                  return (
                    <span className={`font-bold ${trend.color}`}>
                      {trend.symbol} {trend.text}%
                    </span>
                  );
                })()}
              </div>
            )}
          </div>

          {/* BMI Card */}
          <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 space-y-2">
            <div className="flex items-center justify-between text-slate-400 text-xs">
              <span className="flex items-center gap-1.5 font-medium">
                <HeartPulse className="w-4 h-4 text-emerald-400" /> BMI (Índice Masa)
              </span>
              <span className="text-[10px] text-slate-500">Calculado</span>
            </div>

            <div className="flex items-baseline gap-2">
              <span className="text-3xl font-black text-white">{latestInBody.bmi}</span>
              {bmiCategory && (
                <span className={`text-[10px] font-bold px-2 py-0.5 rounded border ${bmiCategory.color}`}>
                  {bmiCategory.label}
                </span>
              )}
            </div>

            <p className="text-[11px] text-slate-400 pt-1">
              Edad Metabólica: <strong className="text-slate-200">{latestInBody.metabolicAge} años</strong>
            </p>
          </div>

          {/* Kcal Basal & Visceral */}
          <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 space-y-2">
            <div className="flex items-center justify-between text-slate-400 text-xs">
              <span className="flex items-center gap-1.5 font-medium">
                <Flame className="w-4 h-4 text-amber-400" /> Metabolismo Basal
              </span>
              <span className="text-[10px] text-slate-500">InBody</span>
            </div>

            <div className="flex items-baseline gap-2">
              <span className="text-3xl font-black text-amber-400">{latestInBody.basalKcal}</span>
              <span className="text-xs text-slate-400 font-bold">kcal/día</span>
            </div>

            {visceralCategory && (
              <div className="pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span className="text-slate-400 text-[11px]">Grasa Visceral:</span>
                <span className={`font-bold text-[11px] ${visceralCategory.color} px-1.5 py-0.5 rounded`}>
                  Nivel {latestInBody.visceralFat}
                </span>
              </div>
            )}
          </div>

        </div>
      ) : (
        <div className="p-8 bg-slate-900 rounded-2xl border border-slate-800 text-center text-slate-400 space-y-3">
          <Scale className="w-10 h-10 mx-auto text-slate-600" />
          <p className="text-sm font-medium">No se han ingresado datos de la báscula InBody todavía.</p>
          <button
            onClick={onOpenNewMetric}
            className="px-4 py-2 bg-cyan-500 text-slate-950 font-bold text-xs rounded-xl shadow hover:bg-cyan-400 transition"
          >
            Registrar Primer InBody
          </button>
        </div>
      )}

      {/* Body Measurements Table (Antropometría) */}
      <div className="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div className="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/50">
          <div className="flex items-center gap-2">
            <Ruler className="w-5 h-5 text-cyan-400" />
            <h3 className="text-sm font-bold text-white">
              Historial de Medidas Corporales (cm)
            </h3>
          </div>
          {latestMeas && (
            <span className="text-xs text-slate-400">Último registro: {latestMeas.recordedAt}</span>
          )}
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs border-collapse">
            <thead>
              <tr className="bg-slate-800/60 text-slate-400 font-semibold uppercase text-[10px] border-b border-slate-800">
                <th className="py-3 px-4">Fecha</th>
                <th className="py-3 px-4">Cintura</th>
                <th className="py-3 px-4">Cadera</th>
                <th className="py-3 px-4">Pecho</th>
                <th className="py-3 px-4">Brazo Der / Izq</th>
                <th className="py-3 px-4">Muslo Der / Izq</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/60 text-slate-300">
              {measurementHistory.length > 0 ? (
                measurementHistory.slice().reverse().map((meas) => (
                  <tr key={meas.id} className="hover:bg-slate-800/50 transition">
                    <td className="py-3 px-4 font-bold text-white">{meas.recordedAt}</td>
                    <td className="py-3 px-4 font-semibold text-cyan-300">{meas.waistCm} cm</td>
                    <td className="py-3 px-4">{meas.hipsCm} cm</td>
                    <td className="py-3 px-4">{meas.chestCm} cm</td>
                    <td className="py-3 px-4">
                      {meas.rightArmCm} cm / {meas.leftArmCm} cm
                    </td>
                    <td className="py-3 px-4">
                      {meas.rightThighCm} cm / {meas.leftThighCm} cm
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={6} className="py-6 text-center text-slate-400">
                    Sin historial de medidas antropométricas registradas.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* InBody Sequential Table */}
      <div className="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div className="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/50">
          <h3 className="text-sm font-bold text-white flex items-center gap-2">
            <Scale className="w-4 h-4 text-cyan-400" /> Historial Completo de Registros InBody
          </h3>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs border-collapse">
            <thead>
              <tr className="bg-slate-800/60 text-slate-400 font-semibold uppercase text-[10px] border-b border-slate-800">
                <th className="py-3 px-4">Fecha</th>
                <th className="py-3 px-4">Peso (kg)</th>
                <th className="py-3 px-4">% Grasa</th>
                <th className="py-3 px-4">BMI</th>
                <th className="py-3 px-4">Kcal Basal</th>
                <th className="py-3 px-4">Grasa Visceral</th>
                <th className="py-3 px-4">Edad Metabólica</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/60 text-slate-300">
              {inBodyHistory.length > 0 ? (
                inBodyHistory.slice().reverse().map((item) => (
                  <tr key={item.id} className="hover:bg-slate-800/50 transition">
                    <td className="py-3 px-4 font-bold text-white">{item.recordedAt}</td>
                    <td className="py-3 px-4 font-bold text-cyan-300">{item.weightKg} kg</td>
                    <td className="py-3 px-4 font-semibold">{item.bodyFatPercentage}%</td>
                    <td className="py-3 px-4">{item.bmi}</td>
                    <td className="py-3 px-4">{item.basalKcal} kcal</td>
                    <td className="py-3 px-4">Nivel {item.visceralFat}</td>
                    <td className="py-3 px-4">{item.metabolicAge} años</td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={7} className="py-6 text-center text-slate-400">
                    No existen registros.
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
