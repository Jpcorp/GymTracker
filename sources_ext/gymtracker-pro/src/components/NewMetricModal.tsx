import React, { useState } from 'react';
import { useGym } from '../context/GymContext';
import { Activity, X, Scale, Ruler } from 'lucide-react';
import { calculateBMI } from '../utils/calculations';

interface NewMetricModalProps {
  isOpen: boolean;
  onClose: () => void;
  clientId: string | null;
}

export const NewMetricModal: React.FC<NewMetricModalProps> = ({ isOpen, onClose, clientId }) => {
  const { clients, addInBodyMetric, addBodyMeasurement, getLatestInBody, getLatestMeasurement } = useGym();

  const client = clients.find((c) => c.id === clientId) || clients[0];
  const latestInBody = client ? getLatestInBody(client.id) : null;
  const latestMeas = client ? getLatestMeasurement(client.id) : null;

  const [recordedAt, setRecordedAt] = useState(new Date().toISOString().split('T')[0]);
  
  // InBody fields
  const [weightKg, setWeightKg] = useState<number>(latestInBody?.weightKg || 78.5);
  const [bodyFatPercentage, setBodyFatPercentage] = useState<number>(latestInBody?.bodyFatPercentage || 18.0);
  const [metabolicAge, setMetabolicAge] = useState<number>(latestInBody?.metabolicAge || 28);
  const [basalKcal, setBasalKcal] = useState<number>(latestInBody?.basalKcal || 1850);
  const [visceralFat, setVisceralFat] = useState<number>(latestInBody?.visceralFat || 7);

  // Body Measurements fields (cm)
  const [waistCm, setWaistCm] = useState<number>(latestMeas?.waistCm || 82.0);
  const [hipsCm, setHipsCm] = useState<number>(latestMeas?.hipsCm || 96.0);
  const [chestCm, setChestCm] = useState<number>(latestMeas?.chestCm || 102.0);
  const [rightArmCm, setRightArmCm] = useState<number>(latestMeas?.rightArmCm || 34.0);
  const [leftArmCm, setLeftArmCm] = useState<number>(latestMeas?.leftArmCm || 33.5);
  const [rightThighCm, setRightThighCm] = useState<number>(latestMeas?.rightThighCm || 58.0);
  const [leftThighCm, setLeftThighCm] = useState<number>(latestMeas?.leftThighCm || 57.5);

  if (!isOpen || !client) return null;

  const calculatedBmi = calculateBMI(weightKg, client.heightCm || 175);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    addInBodyMetric({
      clientId: client.id,
      recordedAt,
      weightKg,
      bodyFatPercentage,
      metabolicAge,
      basalKcal,
      visceralFat
    });

    addBodyMeasurement({
      clientId: client.id,
      recordedAt,
      waistCm,
      hipsCm,
      chestCm,
      rightArmCm,
      leftArmCm,
      rightThighCm,
      leftThighCm
    });

    onClose();
  };

  return (
    <div className="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
      <div className="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-xl w-full space-y-5 shadow-2xl relative my-8">
        
        <div className="flex items-center justify-between pb-3 border-b border-slate-800">
          <div>
            <h3 className="font-bold text-base text-white flex items-center gap-2">
              <Activity className="w-5 h-5 text-cyan-400" />
              Ingresar Control InBody & Antropometría
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
          
          <div className="flex items-center justify-between bg-slate-800/80 p-3 rounded-xl border border-slate-700">
            <div>
              <label className="block text-slate-300 font-semibold mb-0.5">Fecha de la Evaluación</label>
              <input
                type="date"
                required
                value={recordedAt}
                onChange={(e) => setRecordedAt(e.target.value)}
                className="bg-slate-900 text-slate-200 py-1.5 px-3 rounded-lg border border-slate-700 text-xs font-medium"
              />
            </div>

            <div className="text-right">
              <span className="text-[10px] text-slate-400 block font-medium">BMI Calculado:</span>
              <span className="text-xl font-black text-cyan-400">{calculatedBmi}</span>
            </div>
          </div>

          {/* Section 1: InBody Readings */}
          <div className="space-y-3 pt-1">
            <h4 className="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5 border-b border-slate-800 pb-1">
              <Scale className="w-4 h-4" /> Datos de Báscula Inteligente InBody
            </h4>

            <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
              <div>
                <label className="block text-slate-300 font-semibold mb-1">Peso (kg) *</label>
                <input
                  type="number"
                  step="0.1"
                  required
                  value={weightKg}
                  onChange={(e) => setWeightKg(Number(e.target.value))}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-bold text-sm text-cyan-300"
                />
              </div>

              <div>
                <label className="block text-slate-300 font-semibold mb-1">% Grasa Corporal *</label>
                <input
                  type="number"
                  step="0.1"
                  required
                  value={bodyFatPercentage}
                  onChange={(e) => setBodyFatPercentage(Number(e.target.value))}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-bold text-sm text-cyan-300"
                />
              </div>

              <div>
                <label className="block text-slate-300 font-semibold mb-1">Edad Metabólica</label>
                <input
                  type="number"
                  value={metabolicAge}
                  onChange={(e) => setMetabolicAge(Number(e.target.value))}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
                />
              </div>

              <div>
                <label className="block text-slate-300 font-semibold mb-1">Metabolismo Basal (kcal)</label>
                <input
                  type="number"
                  value={basalKcal}
                  onChange={(e) => setBasalKcal(Number(e.target.value))}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
                />
              </div>

              <div>
                <label className="block text-slate-300 font-semibold mb-1">Grasa Visceral (1-20)</label>
                <input
                  type="number"
                  value={visceralFat}
                  onChange={(e) => setVisceralFat(Number(e.target.value))}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
                />
              </div>
            </div>
          </div>

          {/* Section 2: Body Measurements */}
          <div className="space-y-3 pt-2">
            <h4 className="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5 border-b border-slate-800 pb-1">
              <Ruler className="w-4 h-4" /> Medidas Antropométricas (cm)
            </h4>

            <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
              <div>
                <label className="block text-slate-300 font-semibold mb-1">Cintura (cm)</label>
                <input
                  type="number"
                  step="0.5"
                  value={waistCm}
                  onChange={(e) => setWaistCm(Number(e.target.value))}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-semibold text-emerald-300"
                />
              </div>

              <div>
                <label className="block text-slate-300 font-semibold mb-1">Cadera (cm)</label>
                <input
                  type="number"
                  step="0.5"
                  value={hipsCm}
                  onChange={(e) => setHipsCm(Number(e.target.value))}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
                />
              </div>

              <div>
                <label className="block text-slate-300 font-semibold mb-1">Pecho (cm)</label>
                <input
                  type="number"
                  step="0.5"
                  value={chestCm}
                  onChange={(e) => setChestCm(Number(e.target.value))}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 font-medium"
                />
              </div>

              <div>
                <label className="block text-slate-300 font-semibold mb-1">Brazo Der / Izq (cm)</label>
                <div className="flex gap-1">
                  <input
                    type="number"
                    step="0.5"
                    value={rightArmCm}
                    onChange={(e) => setRightArmCm(Number(e.target.value))}
                    className="w-1/2 bg-slate-800 text-slate-200 py-2 px-2 rounded-xl border border-slate-700 text-center font-medium"
                  />
                  <input
                    type="number"
                    step="0.5"
                    value={leftArmCm}
                    onChange={(e) => setLeftArmCm(Number(e.target.value))}
                    className="w-1/2 bg-slate-800 text-slate-200 py-2 px-2 rounded-xl border border-slate-700 text-center font-medium"
                  />
                </div>
              </div>

              <div>
                <label className="block text-slate-300 font-semibold mb-1">Muslo Der / Izq (cm)</label>
                <div className="flex gap-1">
                  <input
                    type="number"
                    step="0.5"
                    value={rightThighCm}
                    onChange={(e) => setRightThighCm(Number(e.target.value))}
                    className="w-1/2 bg-slate-800 text-slate-200 py-2 px-2 rounded-xl border border-slate-700 text-center font-medium"
                  />
                  <input
                    type="number"
                    step="0.5"
                    value={leftThighCm}
                    onChange={(e) => setLeftThighCm(Number(e.target.value))}
                    className="w-1/2 bg-slate-800 text-slate-200 py-2 px-2 rounded-xl border border-slate-700 text-center font-medium"
                  />
                </div>
              </div>
            </div>
          </div>

          <div className="pt-3 flex justify-end gap-2">
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
              Guardar InBody & Medidas
            </button>
          </div>

        </form>

      </div>
    </div>
  );
};
