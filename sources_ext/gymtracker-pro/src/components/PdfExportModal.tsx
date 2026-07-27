import React from 'react';
import { useGym } from '../context/GymContext';
import { FileText, X, Printer, Download, Sparkles, Award, Dumbbell } from 'lucide-react';
import {
  calculateAbsoluteDifference,
  calculatePercentageChange,
  formatTrend
} from '../utils/calculations';

interface PdfExportModalProps {
  isOpen: boolean;
  onClose: () => void;
  clientId: string | null;
}

export const PdfExportModal: React.FC<PdfExportModalProps> = ({
  isOpen,
  onClose,
  clientId
}) => {
  const { clients, getClientInBodyHistory, getClientMeasurementHistory, getClientEvaluations, showToast } = useGym();

  const client = clients.find((c) => c.id === clientId) || clients[0];
  const inBodyHistory = client ? getClientInBodyHistory(client.id) : [];
  const measurementHistory = client ? getClientMeasurementHistory(client.id) : [];
  const clientEvals = client ? getClientEvaluations(client.id) : [];

  const latestInBody = inBodyHistory[inBodyHistory.length - 1];
  const initialInBody = inBodyHistory[0];

  const latestMeas = measurementHistory[measurementHistory.length - 1];
  const initialMeas = measurementHistory[0];

  const latestEval = clientEvals[0];

  if (!isOpen || !client) return null;

  const handlePrint = () => {
    window.print();
    showToast('Imprimiendo Reporte', `Reporte de ${client.name} enviado a la impresora.`, 'success');
  };

  const handleSimulateDownload = () => {
    showToast('Reporte Generado', `Reporte PDF de ${client.name} listo para descarga.`, 'success');
  };

  return (
    <div className="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto print:p-0 print:bg-white print:static">
      
      <div className="bg-slate-900 border border-slate-800 rounded-2xl max-w-3xl w-full p-6 shadow-2xl space-y-6 relative my-8 print:border-none print:shadow-none print:bg-white print:text-slate-900 print:w-full print:max-w-none print:m-0">
        
        {/* Header Controls (Hidden on Print) */}
        <div className="flex items-center justify-between pb-4 border-b border-slate-800 print:hidden">
          <div className="flex items-center gap-2">
            <FileText className="w-5 h-5 text-emerald-400" />
            <h3 className="font-bold text-base text-white">Vista Previa de Reporte PDF de Evaluación</h3>
          </div>
          <div className="flex items-center gap-2">
            <button
              onClick={handlePrint}
              className="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl border border-slate-700 transition flex items-center gap-1.5"
            >
              <Printer className="w-3.5 h-3.5 text-cyan-400" /> Imprimir
            </button>
            <button
              onClick={handleSimulateDownload}
              className="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold rounded-xl transition flex items-center gap-1.5"
            >
              <Download className="w-3.5 h-3.5" /> Descargar PDF
            </button>
            <button
              onClick={onClose}
              className="p-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white"
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        </div>

        {/* Printable Document Sheet Container */}
        <div className="bg-slate-950 print:bg-white p-6 rounded-xl border border-slate-800 print:border-none space-y-6 text-slate-200 print:text-slate-900">
          
          {/* Gym Header Branding */}
          <div className="flex items-center justify-between border-b border-slate-800 print:border-slate-300 pb-4">
            <div>
              <h1 className="text-2xl font-black tracking-tight text-white print:text-slate-900">
                GymTracker PRO
              </h1>
              <p className="text-xs text-slate-400 print:text-slate-600">
                Centro de Entrenamiento Personalizado & Control de 21 Días
              </p>
            </div>
            <div className="text-right text-xs text-slate-400 print:text-slate-600">
              <p className="font-bold text-cyan-400 print:text-cyan-700">INFORME DE EVALUACIÓN FISICA</p>
              <p>Fecha de emisión: {new Date().toISOString().split('T')[0]}</p>
            </div>
          </div>

          {/* Student Profile Overview */}
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-900 print:bg-slate-100 p-4 rounded-xl border border-slate-800 print:border-slate-300 text-xs">
            <div>
              <span className="text-slate-400 print:text-slate-500 block text-[10px]">Alumno:</span>
              <strong className="text-white print:text-slate-900 font-bold text-sm">{client.name}</strong>
            </div>
            <div>
              <span className="text-slate-400 print:text-slate-500 block text-[10px]">Objetivo:</span>
              <strong className="text-cyan-300 print:text-cyan-800 font-semibold">{client.goal}</strong>
            </div>
            <div>
              <span className="text-slate-400 print:text-slate-500 block text-[10px]">Fecha de Inicio:</span>
              <strong className="text-slate-200 print:text-slate-800">{client.startDate}</strong>
            </div>
            <div>
              <span className="text-slate-400 print:text-slate-500 block text-[10px]">Entrenador:</span>
              <strong className="text-slate-200 print:text-slate-800">{client.trainerName}</strong>
            </div>
          </div>

          {/* Diagnostic Comparison Table */}
          {initialInBody && latestInBody && (
            <div className="space-y-3">
              <h3 className="text-xs font-bold uppercase tracking-wider text-slate-300 print:text-slate-800 flex items-center gap-1.5">
                <Award className="w-4 h-4 text-amber-400 print:text-amber-600" />
                Resumen de Avance Corporal (Control Inicial vs Control Reciente)
              </h3>

              <div className="overflow-x-auto rounded-xl border border-slate-800 print:border-slate-300">
                <table className="w-full text-left text-xs border-collapse">
                  <thead>
                    <tr className="bg-slate-900 print:bg-slate-200 text-slate-400 print:text-slate-700 font-semibold uppercase text-[10px]">
                      <th className="py-2.5 px-3">Métrica InBody</th>
                      <th className="py-2.5 px-3">Inicial ({initialInBody.recordedAt})</th>
                      <th className="py-2.5 px-3">Actual ({latestInBody.recordedAt})</th>
                      <th className="py-2.5 px-3">Cambio Total</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-800 print:divide-slate-200 text-slate-300 print:text-slate-800">
                    <tr>
                      <td className="py-2 px-3 font-bold">Peso Corporal</td>
                      <td className="py-2 px-3">{initialInBody.weightKg} kg</td>
                      <td className="py-2 px-3 font-bold text-cyan-400 print:text-cyan-800">{latestInBody.weightKg} kg</td>
                      <td className="py-2 px-3 font-bold">
                        {(() => {
                          const diff = calculateAbsoluteDifference(latestInBody.weightKg, initialInBody.weightKg);
                          const trend = formatTrend(diff, true);
                          return <span className={trend.color}>{trend.text} kg</span>;
                        })()}
                      </td>
                    </tr>

                    <tr>
                      <td className="py-2 px-3 font-bold">% Grasa Corporal</td>
                      <td className="py-2 px-3">{initialInBody.bodyFatPercentage}%</td>
                      <td className="py-2 px-3 font-bold text-cyan-400 print:text-cyan-800">{latestInBody.bodyFatPercentage}%</td>
                      <td className="py-2 px-3 font-bold">
                        {(() => {
                          const diff = calculateAbsoluteDifference(latestInBody.bodyFatPercentage, initialInBody.bodyFatPercentage);
                          const trend = formatTrend(diff, true);
                          return <span className={trend.color}>{trend.text}%</span>;
                        })()}
                      </td>
                    </tr>

                    <tr>
                      <td className="py-2 px-3 font-bold">BMI (Índice de Masa)</td>
                      <td className="py-2 px-3">{initialInBody.bmi}</td>
                      <td className="py-2 px-3 font-bold">{latestInBody.bmi}</td>
                      <td className="py-2 px-3 font-bold">
                        {(() => {
                          const diff = calculateAbsoluteDifference(latestInBody.bmi, initialInBody.bmi);
                          const trend = formatTrend(diff, true);
                          return <span className={trend.color}>{trend.text}</span>;
                        })()}
                      </td>
                    </tr>

                    {initialMeas && latestMeas && (
                      <tr>
                        <td className="py-2 px-3 font-bold">Cintura (cm)</td>
                        <td className="py-2 px-3">{initialMeas.waistCm} cm</td>
                        <td className="py-2 px-3 font-bold text-emerald-400 print:text-emerald-800">{latestMeas.waistCm} cm</td>
                        <td className="py-2 px-3 font-bold">
                          {(() => {
                            const diff = calculateAbsoluteDifference(latestMeas.waistCm, initialMeas.waistCm);
                            const trend = formatTrend(diff, true);
                            return <span className={trend.color}>{trend.text} cm</span>;
                          })()}
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* Trainer Notes */}
          {latestEval && (
            <div className="space-y-2 p-4 bg-slate-900 print:bg-slate-100 rounded-xl border border-slate-800 print:border-slate-300">
              <h4 className="text-xs font-bold text-slate-300 print:text-slate-800">
                Observaciones & Recomendaciones del Entrenador:
              </h4>
              <p className="text-xs italic text-slate-300 print:text-slate-700">
                "{latestEval.trainerNotes}"
              </p>
            </div>
          )}

          {/* Footer Signature line for print */}
          <div className="pt-8 flex justify-between items-end border-t border-slate-800 print:border-slate-300 text-[10px] text-slate-500">
            <div>
              <p className="font-bold text-slate-400 print:text-slate-700">GymTracker PRO</p>
              <p>Sistema de Gestión de Clientes de Entrenamiento Personalizado</p>
            </div>
            <div className="text-center">
              <div className="w-36 border-b border-slate-600 print:border-slate-400 mb-1"></div>
              <p className="font-bold text-slate-300 print:text-slate-800">{client.trainerName}</p>
              <p>Entrenador Personal</p>
            </div>
          </div>

        </div>

      </div>

    </div>
  );
};
