import React, { useState } from 'react';
import { Client, Evaluation } from '../../types';
import { useGym } from '../../context/GymContext';
import {
  Award,
  Sparkles,
  CheckCircle2,
  TrendingDown,
  TrendingUp,
  FileText,
  Calendar,
  AlertCircle,
  Plus
} from 'lucide-react';
import {
  calculateAbsoluteDifference,
  calculatePercentageChange,
  getNextEvaluationDate,
  formatTrend
} from '../../utils/calculations';

interface ClientEvaluationsTabProps {
  client: Client;
  onOpenPdfReport: () => void;
  onOpenNewMetric: () => void;
}

export const ClientEvaluationsTab: React.FC<ClientEvaluationsTabProps> = ({
  client,
  onOpenPdfReport,
  onOpenNewMetric
}) => {
  const {
    getClientEvaluations,
    getClientInBodyHistory,
    getClientMeasurementHistory,
    generate21DayEvaluation
  } = useGym();

  const evaluations = getClientEvaluations(client.id);
  const inBodyHistory = getClientInBodyHistory(client.id);
  const measurementHistory = getClientMeasurementHistory(client.id);

  const [selectedEvalId, setSelectedEvalId] = useState<string>(
    evaluations[0]?.id || ''
  );

  const currentEval = evaluations.find((e) => e.id === selectedEvalId) || evaluations[0];
  const evalIndex = evaluations.findIndex((e) => e.id === currentEval?.id);
  const previousEval = evaluations[evalIndex + 1]; // evaluations sorted descending

  const { daysRemaining, evalNumber } = getNextEvaluationDate(client.startDate, evaluations.length);

  // Get InBody readings for selected evaluation and previous
  const currentInBody = inBodyHistory.find((m) => m.evaluationId === currentEval?.id) || inBodyHistory[inBodyHistory.length - 1];
  const previousInBody = previousEval
    ? inBodyHistory.find((m) => m.evaluationId === previousEval.id)
    : inBodyHistory.length > 1
    ? inBodyHistory[inBodyHistory.length - 2]
    : null;

  // Get Body measurements for selected evaluation and previous
  const currentMeas = measurementHistory.find((m) => m.evaluationId === currentEval?.id) || measurementHistory[measurementHistory.length - 1];
  const previousMeas = previousEval
    ? measurementHistory.find((m) => m.evaluationId === previousEval.id)
    : measurementHistory.length > 1
    ? measurementHistory[measurementHistory.length - 2]
    : null;

  const handleCreateEvaluation = () => {
    generate21DayEvaluation(client.id, 'Evaluación de control de 21 días generada manualmente.');
  };

  return (
    <div className="space-y-6">
      
      {/* 21-Day Cycle Banner */}
      <div className="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-2 mb-1">
            <span className="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 text-[10px] font-bold border border-amber-500/30 uppercase flex items-center gap-1">
              <Sparkles className="w-3 h-3" /> Ciclo Automático 21 Días
            </span>
            <span className="text-slate-400 text-xs">• Control Periódico InBody</span>
          </div>
          <h2 className="text-xl font-extrabold text-white">Evaluaciones & Comparativas Físicas</h2>
          <p className="text-xs text-slate-400 mt-1 max-w-xl">
            Cada 21 días el sistema compara la evolución corporal, la adherencia y la ganancia de fuerza del alumno.
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-3">
          <button
            onClick={handleCreateEvaluation}
            className="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-extrabold text-xs rounded-xl shadow transition flex items-center gap-1.5"
          >
            <Plus className="w-4 h-4 stroke-[3]" />
            Generar Evaluación 21d
          </button>

          <button
            onClick={onOpenPdfReport}
            className="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5"
          >
            <FileText className="w-4 h-4 text-emerald-400" />
            Descargar PDF
          </button>
        </div>
      </div>

      {/* Evaluations Selector Tabs */}
      {evaluations.length > 0 && (
        <div className="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-800 scrollbar-none">
          {evaluations.map((ev) => {
            const isSelected = ev.id === currentEval?.id;

            return (
              <button
                key={ev.id}
                onClick={() => setSelectedEvalId(ev.id)}
                className={`px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap ${
                  isSelected
                    ? 'bg-amber-500 text-slate-950 shadow-md'
                    : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'
                }`}
              >
                <Award className="w-4 h-4" />
                <span>Evaluación #{ev.evaluationNumber}</span>
                <span className="text-[10px] opacity-80">({ev.evaluatedAt})</span>
              </button>
            );
          })}
        </div>
      )}

      {/* Evaluation Content Details */}
      {currentEval ? (
        <div className="space-y-6">
          
          {/* Comparative Table: Current Eval vs Previous Eval */}
          <div className="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
              <div>
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <Award className="w-4 h-4 text-amber-400" />
                  Tabla Comparativa — Evaluación #{currentEval.evaluationNumber} ({currentEval.evaluatedAt})
                </h3>
                <p className="text-xs text-slate-400">
                  {previousEval
                    ? `Comparado con Evaluación #${previousEval.evaluationNumber} (${previousEval.evaluatedAt})`
                    : 'Medición de Diagnóstico Inicial'}
                </p>
              </div>

              <span className="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 border border-slate-700">
                Período: 21 Días
              </span>
            </div>

            {/* Comparison Metrics Table */}
            <div className="overflow-x-auto rounded-xl border border-slate-800">
              <table className="w-full text-left text-xs border-collapse">
                <thead>
                  <tr className="bg-slate-800/80 text-slate-400 uppercase text-[10px] font-semibold border-b border-slate-700">
                    <th className="py-3 px-4">Métrica</th>
                    <th className="py-3 px-4">Anterior</th>
                    <th className="py-3 px-4">Actual (Eval #{currentEval.evaluationNumber})</th>
                    <th className="py-3 px-4">Cambio Absoluto</th>
                    <th className="py-3 px-4">Tendencia %</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800 text-slate-300">
                  
                  {/* Weight Row */}
                  <tr className="hover:bg-slate-800/40 transition">
                    <td className="py-3 px-4 font-bold text-white">Peso Corporal (kg)</td>
                    <td className="py-3 px-4">{previousInBody ? `${previousInBody.weightKg} kg` : '--'}</td>
                    <td className="py-3 px-4 font-black text-cyan-300">{currentInBody?.weightKg} kg</td>
                    <td className="py-3 px-4">
                      {previousInBody && currentInBody ? (
                        (() => {
                          const diff = calculateAbsoluteDifference(currentInBody.weightKg, previousInBody.weightKg);
                          const trend = formatTrend(diff, true);
                          return <span className={`font-bold ${trend.color}`}>{trend.text} kg</span>;
                        })()
                      ) : '--'}
                    </td>
                    <td className="py-3 px-4">
                      {previousInBody && currentInBody ? (
                        (() => {
                          const pct = calculatePercentageChange(currentInBody.weightKg, previousInBody.weightKg);
                          const trend = formatTrend(pct, true);
                          return <span className={`font-bold ${trend.color}`}>{trend.symbol} {pct}%</span>;
                        })()
                      ) : '--'}
                    </td>
                  </tr>

                  {/* Body Fat % Row */}
                  <tr className="hover:bg-slate-800/40 transition">
                    <td className="py-3 px-4 font-bold text-white">% Grasa Corporal</td>
                    <td className="py-3 px-4">{previousInBody ? `${previousInBody.bodyFatPercentage}%` : '--'}</td>
                    <td className="py-3 px-4 font-black text-cyan-300">{currentInBody?.bodyFatPercentage}%</td>
                    <td className="py-3 px-4">
                      {previousInBody && currentInBody ? (
                        (() => {
                          const diff = calculateAbsoluteDifference(currentInBody.bodyFatPercentage, previousInBody.bodyFatPercentage);
                          const trend = formatTrend(diff, true);
                          return <span className={`font-bold ${trend.color}`}>{trend.text}%</span>;
                        })()
                      ) : '--'}
                    </td>
                    <td className="py-3 px-4">
                      {previousInBody && currentInBody ? (
                        (() => {
                          const pct = calculatePercentageChange(currentInBody.bodyFatPercentage, previousInBody.bodyFatPercentage);
                          const trend = formatTrend(pct, true);
                          return <span className={`font-bold ${trend.color}`}>{trend.symbol} {pct}%</span>;
                        })()
                      ) : '--'}
                    </td>
                  </tr>

                  {/* BMI Row */}
                  <tr className="hover:bg-slate-800/40 transition">
                    <td className="py-3 px-4 font-bold text-white">BMI (Índice de Masa)</td>
                    <td className="py-3 px-4">{previousInBody ? previousInBody.bmi : '--'}</td>
                    <td className="py-3 px-4 font-bold text-slate-200">{currentInBody?.bmi}</td>
                    <td className="py-3 px-4">
                      {previousInBody && currentInBody ? (
                        (() => {
                          const diff = calculateAbsoluteDifference(currentInBody.bmi, previousInBody.bmi);
                          const trend = formatTrend(diff, true);
                          return <span className={`font-bold ${trend.color}`}>{trend.text}</span>;
                        })()
                      ) : '--'}
                    </td>
                    <td className="py-3 px-4">
                      {previousInBody && currentInBody ? (
                        (() => {
                          const pct = calculatePercentageChange(currentInBody.bmi, previousInBody.bmi);
                          const trend = formatTrend(pct, true);
                          return <span className={`font-bold ${trend.color}`}>{trend.symbol} {pct}%</span>;
                        })()
                      ) : '--'}
                    </td>
                  </tr>

                  {/* Waist Cm Row */}
                  <tr className="hover:bg-slate-800/40 transition">
                    <td className="py-3 px-4 font-bold text-white">Cintura (cm)</td>
                    <td className="py-3 px-4">{previousMeas ? `${previousMeas.waistCm} cm` : '--'}</td>
                    <td className="py-3 px-4 font-bold text-emerald-400">{currentMeas?.waistCm} cm</td>
                    <td className="py-3 px-4">
                      {previousMeas && currentMeas ? (
                        (() => {
                          const diff = calculateAbsoluteDifference(currentMeas.waistCm, previousMeas.waistCm);
                          const trend = formatTrend(diff, true);
                          return <span className={`font-bold ${trend.color}`}>{trend.text} cm</span>;
                        })()
                      ) : '--'}
                    </td>
                    <td className="py-3 px-4">
                      {previousMeas && currentMeas ? (
                        (() => {
                          const pct = calculatePercentageChange(currentMeas.waistCm, previousMeas.waistCm);
                          const trend = formatTrend(pct, true);
                          return <span className={`font-bold ${trend.color}`}>{trend.symbol} {pct}%</span>;
                        })()
                      ) : '--'}
                    </td>
                  </tr>

                  {/* Basal Kcal Row */}
                  <tr className="hover:bg-slate-800/40 transition">
                    <td className="py-3 px-4 font-bold text-white">Metabolismo Basal (Kcal)</td>
                    <td className="py-3 px-4">{previousInBody ? `${previousInBody.basalKcal} kcal` : '--'}</td>
                    <td className="py-3 px-4 font-bold text-amber-400">{currentInBody?.basalKcal} kcal</td>
                    <td className="py-3 px-4">
                      {previousInBody && currentInBody ? (
                        (() => {
                          const diff = calculateAbsoluteDifference(currentInBody.basalKcal, previousInBody.basalKcal);
                          const trend = formatTrend(diff, false); // higher kcal basal is usually muscle gain
                          return <span className={`font-bold ${trend.color}`}>{trend.text} kcal</span>;
                        })()
                      ) : '--'}
                    </td>
                    <td className="py-3 px-4">
                      {previousInBody && currentInBody ? (
                        (() => {
                          const pct = calculatePercentageChange(currentInBody.basalKcal, previousInBody.basalKcal);
                          const trend = formatTrend(pct, false);
                          return <span className={`font-bold ${trend.color}`}>{trend.symbol} {pct}%</span>;
                        })()
                      ) : '--'}
                    </td>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>

          {/* Achievements & Milestones List */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {/* Achievements Card */}
            <div className="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-3">
              <h3 className="text-sm font-bold text-white flex items-center gap-2">
                <Sparkles className="w-4 h-4 text-amber-400" />
                Logros & Hitos Alcanzados en el Período
              </h3>

              <div className="space-y-2">
                {currentEval.achievementsSummary.map((item, idx) => (
                  <div
                    key={idx}
                    className="p-3 bg-slate-800/60 rounded-xl border border-slate-700/50 flex items-start gap-2.5 text-xs text-slate-200"
                  >
                    <CheckCircle2 className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                    <span>{item}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Trainer Notes Card */}
            <div className="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-3">
              <h3 className="text-sm font-bold text-white flex items-center gap-2">
                <FileText className="w-4 h-4 text-cyan-400" />
                Observaciones del Entrenador
              </h3>

              <div className="p-4 bg-slate-800/60 rounded-xl border border-slate-700/50 text-xs text-slate-300 leading-relaxed italic min-h-[120px]">
                "{currentEval.trainerNotes}"
              </div>
            </div>

          </div>

        </div>
      ) : (
        <div className="p-8 bg-slate-900 rounded-2xl border border-slate-800 text-center text-slate-400 space-y-3">
          <Award className="w-10 h-10 mx-auto text-slate-600" />
          <p className="text-sm font-medium">Aún no se han generado evaluaciones para este cliente.</p>
          <button
            onClick={handleCreateEvaluation}
            className="px-4 py-2 bg-amber-500 text-slate-950 font-bold text-xs rounded-xl shadow hover:bg-amber-400 transition"
          >
            Generar Primera Evaluación de 21 Días
          </button>
        </div>
      )}

    </div>
  );
};
