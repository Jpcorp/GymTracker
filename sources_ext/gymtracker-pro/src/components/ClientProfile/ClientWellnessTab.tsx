import React, { useState } from 'react';
import { Client } from '../../types';
import { useGym } from '../../context/GymContext';
import { Smile, Utensils, Heart, Plus, Sparkles, Check } from 'lucide-react';

interface ClientWellnessTabProps {
  client: Client;
}

export const ClientWellnessTab: React.FC<ClientWellnessTabProps> = ({ client }) => {
  const { moodRecords, nutritionLogs, satisfactionSurveys, addMoodRecord, addNutritionLog, addSatisfactionSurvey } = useGym();

  const clientMoods = moodRecords.filter((m) => m.clientId === client.id);
  const clientNutrition = nutritionLogs.filter((n) => n.clientId === client.id);
  const clientSurveys = satisfactionSurveys.filter((s) => s.clientId === client.id);

  // Form states
  const [moodLevel, setMoodLevel] = useState<number>(8);
  const [moodNotes, setMoodNotes] = useState<string>('');

  const [nutritionCompliance, setNutritionCompliance] = useState<'complete' | 'partial' | 'missed'>('complete');
  const [nutritionNotes, setNutritionNotes] = useState<string>('');

  const [satisfactionLevel, setSatisfactionLevel] = useState<number>(10);
  const [surveyComments, setSurveyComments] = useState<string>('');

  const moodEmojis: Record<number, string> = {
    1: '😫', 2: '😫',
    3: '😔', 4: '😔',
    5: '😐', 6: '😐',
    7: '🙂', 8: '🙂',
    9: '😄', 10: '😄'
  };

  const handleSaveMood = (e: React.FormEvent) => {
    e.preventDefault();
    addMoodRecord({
      clientId: client.id,
      weekStart: new Date().toISOString().split('T')[0],
      weekEnd: new Date().toISOString().split('T')[0],
      moodLevel,
      energyLevel: moodLevel,
      motivationLevel: moodLevel,
      notes: moodNotes || 'Registro de estado de ánimo semanal'
    });
    setMoodNotes('');
  };

  const handleSaveNutrition = (e: React.FormEvent) => {
    e.preventDefault();
    addNutritionLog({
      clientId: client.id,
      logDate: new Date().toISOString().split('T')[0],
      compliance: nutritionCompliance,
      mealsLogged: 4,
      mealsPlanned: 4,
      notes: nutritionNotes || 'Registro de pauta alimentaria'
    });
    setNutritionNotes('');
  };

  const handleSaveSurvey = (e: React.FormEvent) => {
    e.preventDefault();
    addSatisfactionSurvey({
      clientId: client.id,
      surveyDate: new Date().toISOString().split('T')[0],
      overallSatisfaction: satisfactionLevel,
      trainerSatisfaction: satisfactionLevel,
      facilitiesSatisfaction: satisfactionLevel,
      routinesSatisfaction: satisfactionLevel,
      comments: surveyComments || 'Encuesta periódica de satisfacción'
    });
    setSurveyComments('');
  };

  return (
    <div className="space-y-6">
      
      {/* Header Bar */}
      <div className="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-xl">
        <h2 className="text-base font-bold text-white flex items-center gap-2">
          <Sparkles className="w-5 h-5 text-amber-400" />
          Seguimiento de Bienestar, Alimentación y Satisfacción
        </h2>
        <p className="text-xs text-slate-400 mt-0.5">
          Registro del estado emocional del alumno, cumplimiento de pauta nutricional y feedback del gimnasio.
        </p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {/* Mood Section */}
        <div className="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
          <div className="flex items-center gap-2 border-b border-slate-800 pb-3">
            <Smile className="w-5 h-5 text-amber-400" />
            <h3 className="text-sm font-bold text-white">Estado de Ánimo & Motivación</h3>
          </div>

          <form onSubmit={handleSaveMood} className="space-y-3 text-xs">
            <div>
              <label className="block text-slate-300 font-semibold mb-1">
                Calificación (1 al 10): <span className="text-amber-400 font-extrabold">{moodLevel}/10 {moodEmojis[moodLevel]}</span>
              </label>
              <input
                type="range"
                min="1"
                max="10"
                value={moodLevel}
                onChange={(e) => setMoodLevel(Number(e.target.value))}
                className="w-full accent-amber-400"
              />
            </div>

            <div>
              <input
                type="text"
                placeholder="Comentarios o energía..."
                value={moodNotes}
                onChange={(e) => setMoodNotes(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-1.5 px-3 rounded-xl border border-slate-700 text-xs"
              />
            </div>

            <button
              type="submit"
              className="w-full py-1.5 bg-amber-500 text-slate-950 font-bold rounded-xl text-xs hover:bg-amber-400 transition"
            >
              Guardar Ánimo
            </button>
          </form>

          {/* History */}
          <div className="space-y-2 pt-2 border-t border-slate-800">
            <span className="text-[10px] font-bold text-slate-400 uppercase">Últimos Registros:</span>
            {clientMoods.length > 0 ? (
              clientMoods.map((m) => (
                <div key={m.id} className="p-2.5 bg-slate-800/60 rounded-xl text-xs flex items-center justify-between">
                  <span>{m.weekStart}: {m.notes}</span>
                  <span className="font-bold text-amber-400">{m.moodLevel}/10 {moodEmojis[m.moodLevel]}</span>
                </div>
              ))
            ) : (
              <p className="text-[11px] text-slate-500 italic">Sin registros aún.</p>
            )}
          </div>
        </div>

        {/* Nutrition Section */}
        <div className="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
          <div className="flex items-center gap-2 border-b border-slate-800 pb-3">
            <Utensils className="w-5 h-5 text-emerald-400" />
            <h3 className="text-sm font-bold text-white">Cumplimiento Nutricional</h3>
          </div>

          <form onSubmit={handleSaveNutrition} className="space-y-3 text-xs">
            <div>
              <label className="block text-slate-300 font-semibold mb-1">Nivel de Cumplimiento</label>
              <select
                value={nutritionCompliance}
                onChange={(e) => setNutritionCompliance(e.target.value as any)}
                className="w-full bg-slate-800 text-slate-200 py-1.5 px-3 rounded-xl border border-slate-700 text-xs"
              >
                <option value="complete">Completo (100% Pauta Cumplida)</option>
                <option value="partial">Parcial (Cumplió la mayoría)</option>
                <option value="missed">Incumplido (Fuera de pauta)</option>
              </select>
            </div>

            <div>
              <input
                type="text"
                placeholder="Notas de comidas/proteínas..."
                value={nutritionNotes}
                onChange={(e) => setNutritionNotes(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-1.5 px-3 rounded-xl border border-slate-700 text-xs"
              />
            </div>

            <button
              type="submit"
              className="w-full py-1.5 bg-emerald-500 text-slate-950 font-bold rounded-xl text-xs hover:bg-emerald-400 transition"
            >
              Guardar Nutrición
            </button>
          </form>

          {/* History */}
          <div className="space-y-2 pt-2 border-t border-slate-800">
            <span className="text-[10px] font-bold text-slate-400 uppercase">Últimos Registros:</span>
            {clientNutrition.length > 0 ? (
              clientNutrition.map((n) => (
                <div key={n.id} className="p-2.5 bg-slate-800/60 rounded-xl text-xs flex items-center justify-between">
                  <span>{n.logDate}: {n.notes}</span>
                  <span className="font-bold text-emerald-400 capitalize">{n.compliance}</span>
                </div>
              ))
            ) : (
              <p className="text-[11px] text-slate-500 italic">Sin registros aún.</p>
            )}
          </div>
        </div>

        {/* Satisfaction Survey Section */}
        <div className="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
          <div className="flex items-center gap-2 border-b border-slate-800 pb-3">
            <Heart className="w-5 h-5 text-rose-400" />
            <h3 className="text-sm font-bold text-white">Satisfacción del Alumno</h3>
          </div>

          <form onSubmit={handleSaveSurvey} className="space-y-3 text-xs">
            <div>
              <label className="block text-slate-300 font-semibold mb-1">
                Satisfacción General: <span className="text-rose-400 font-extrabold">{satisfactionLevel}/10</span>
              </label>
              <input
                type="range"
                min="1"
                max="10"
                value={satisfactionLevel}
                onChange={(e) => setSatisfactionLevel(Number(e.target.value))}
                className="w-full accent-rose-400"
              />
            </div>

            <div>
              <input
                type="text"
                placeholder="Comentarios o feedback..."
                value={surveyComments}
                onChange={(e) => setSurveyComments(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-1.5 px-3 rounded-xl border border-slate-700 text-xs"
              />
            </div>

            <button
              type="submit"
              className="w-full py-1.5 bg-rose-500 text-slate-950 font-bold rounded-xl text-xs hover:bg-rose-400 transition"
            >
              Guardar Encuesta
            </button>
          </form>

          {/* History */}
          <div className="space-y-2 pt-2 border-t border-slate-800">
            <span className="text-[10px] font-bold text-slate-400 uppercase">Último Feedback:</span>
            {clientSurveys.length > 0 ? (
              clientSurveys.map((s) => (
                <div key={s.id} className="p-2.5 bg-slate-800/60 rounded-xl text-xs flex items-center justify-between">
                  <span className="italic">"{s.comments}"</span>
                  <span className="font-bold text-rose-400">{s.overallSatisfaction}/10</span>
                </div>
              ))
            ) : (
              <p className="text-[11px] text-slate-500 italic">Sin encuestas aún.</p>
            )}
          </div>
        </div>

      </div>

    </div>
  );
};
